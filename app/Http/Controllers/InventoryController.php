<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Branch;

class InventoryController extends Controller
{
    public function index(Request $request)
    {

        $selectedBranch = (Gate::allows('admin') || Gate::allows('owner')) ? $request->query('branch_id', session('branch_id')) : session('branch_id');
        $items = Item::with([
            'inventories' => function ($query) use ($selectedBranch) {
                $query->where('branch_id', $selectedBranch);
            }
        ])->get();

        if (Gate::allows('admin') || Gate::allows('owner')) {

            $branches = Branch::all();
            return view('inventory', compact('items', 'branches', 'selectedBranch'));

        } elseif (Gate::allows('cashier')) {

            $items = $items->makeHidden(['cost']);

            return view('inventory', compact('items'));

        }
    }
}
