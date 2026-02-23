<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $branchId = $request->get('branch_id');
        $from = $request->get('from');
        $to = $request->get('to');

        $branches = Branch::orderBy('name')->get();

        $expenses = Expense::with(['branch', 'user'])
            ->when($q, fn ($qr) => $qr->where('category', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%"))
            ->when($branchId, fn ($qr) => $qr->where('branch_id', $branchId))
            ->when($from, fn ($qr) => $qr->whereDate('spent_at', '>=', $from))
            ->when($to, fn ($qr) => $qr->whereDate('spent_at', '<=', $to))
            ->latest('spent_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.expenses.index', compact('expenses', 'branches', 'q', 'branchId', 'from', 'to'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();
        return view('admin.expenses.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'spent_at' => ['required', 'date'],
        ]);

        $data['user_id'] = Auth::id();
        // If not chosen, fallback to user's branch
        if (empty($data['branch_id'])) {
            $data['branch_id'] = Auth::user()?->branch_id;
        }

        Expense::create($data);

        return redirect()->route('expenses.index')->with('success', 'Expense added.');
    }

    public function edit(Expense $expense)
    {
        $branches = Branch::orderBy('name')->get();
        return view('admin.expenses.edit', compact('expense', 'branches'));
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'spent_at' => ['required', 'date'],
        ]);

        $expense->update($data);

        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Expense deleted.');
    }
}
