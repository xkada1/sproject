<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $status = $request->get('status');
        $branchId = session('branch_id');

        $purchaseOrders = PurchaseOrder::query()
            ->with('supplier')
            ->where('branch_id', $branchId)
            ->when($q, fn ($query) => $query->where('po_number', 'like', "%{$q}%"))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.purchase_orders.index', compact('purchaseOrders', 'q', 'status'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('admin.purchase_orders.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $branchId = session('branch_id');

        $po = DB::transaction(function () use ($data, $branchId) {
            $total = 0;

            $po = PurchaseOrder::create([
                'branch_id' => $branchId,
                'supplier_id' => $data['supplier_id'] ?? null,
                'po_number' => 'PO-' . now()->format('Ymd-His') . '-' . random_int(100, 999),
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
                'total_amount' => 0,
            ]);

            foreach ($data['items'] as $row) {
                $qty = (int) $row['qty'];
                $cost = (float) ($row['cost'] ?? 0);
                $line = $qty * $cost;
                $total += $line;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_name' => $row['name'],
                    'quantity' => $qty,
                    'unit_cost' => $cost,
                    'line_total' => $line,
                ]);
            }

            $po->update(['total_amount' => $total]);

            return $po;
        });

        Audit::log('po_created', PurchaseOrder::class, (int)$po->id, ['total' => (float)$po->total_amount]);

        return redirect()->route('purchase-orders.show', $po)->with('success', 'Purchase order created.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeBranch($purchaseOrder);
        $purchaseOrder->load('items', 'supplier');
        return view('admin.purchase_orders.show', compact('purchaseOrder'));
    }

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorizeBranch($purchaseOrder);

        $data = $request->validate([
            'status' => ['required', 'in:draft,ordered,received,cancelled'],
        ]);

        $from = $purchaseOrder->status;
        $to = $data['status'];

        $purchaseOrder->status = $to;
        if ($to === 'ordered' && !$purchaseOrder->ordered_at) {
            $purchaseOrder->ordered_at = now();
        }
        if ($to === 'received') {
            $purchaseOrder->received_at = now();
        }
        $purchaseOrder->save();

        Audit::log('po_status_changed', PurchaseOrder::class, (int)$purchaseOrder->id, ['from' => $from, 'to' => $to]);

        return back()->with('success', 'PO status updated.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeBranch($purchaseOrder);

        Audit::log('po_deleted', PurchaseOrder::class, (int)$purchaseOrder->id, ['status' => $purchaseOrder->status]);
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase order deleted.');
    }

    private function authorizeBranch(PurchaseOrder $purchaseOrder): void
    {
        $branchId = session('branch_id');
        if ((int) $purchaseOrder->branch_id !== (int) $branchId) {
            abort(403, 'You cannot access this branch record.');
        }
    }
}
