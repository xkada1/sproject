<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Promotion;
use App\Support\Audit;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $branchId = session('branch_id');

        $promotions = Promotion::query()
            ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%")
                ->orWhere('code', 'like', "%{$q}%"))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.promotions.index', compact('promotions', 'q', 'branchId'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();
        return view('admin.promotions.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:120'],
            'code' => ['required', 'string', 'max:40', 'unique:promotions,code'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? false);

        $promotion = Promotion::create($data);
        Audit::log('promotion_created', Promotion::class, (int)$promotion->id, ['code' => $promotion->code]);

        return redirect()->route('promotions.index')->with('success', 'Promotion created.');
    }

    public function edit(Promotion $promotion)
    {
        $branches = Branch::orderBy('name')->get();
        return view('admin.promotions.edit', compact('promotion', 'branches'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $data = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:120'],
            'code' => ['required', 'string', 'max:40', 'unique:promotions,code,' . $promotion->id],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? false);

        $promotion->update($data);
        Audit::log('promotion_updated', Promotion::class, (int)$promotion->id, ['code' => $promotion->code]);

        return redirect()->route('promotions.index')->with('success', 'Promotion updated.');
    }

    public function destroy(Promotion $promotion)
    {
        Audit::log('promotion_deleted', Promotion::class, (int)$promotion->id, ['code' => $promotion->code]);
        $promotion->delete();
        return redirect()->route('promotions.index')->with('success', 'Promotion deleted.');
    }
}
