<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Branch;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::all();

        $branchId = $request->input('branch_id');
        $current_branch = $branchId ? $branches->firstWhere('id', $branchId) : $branches->first();

        $items = Item::with([
            'inventories' => function ($query) use ($current_branch) {
                if ($current_branch) {
                    $query->where('branch_id', $current_branch->id);
                }
            }
        ])->get()->map(function ($item) {
            $item->current_stock = $item->inventories->first()?->stock ?? 0;
            return $item;
        });

        return view('inventory', compact('items', 'branches', 'current_branch'));
    }
}
