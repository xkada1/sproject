<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $users = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $branches = Branch::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'branches', 'q'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'in:admin,manager,cashier'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        $user->role = $data['role'];
        $user->branch_id = $data['branch_id'] ?? null;
        $user->save();

        return back()->with('success', 'User updated.');
    }
}
