<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchSwitchController extends Controller
{
    public function switch(Request $request)
    {
        $data = $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
        ]);

        // Save selected branch to session
        session(['branch_id' => (int) $data['branch_id']]);

        return back();
    }
}