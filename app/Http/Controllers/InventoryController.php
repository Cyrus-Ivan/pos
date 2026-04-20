<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Branch;

class InventoryController extends Controller
{
    private function getItems($branch_id)
    {
        return Item::with([
            'inventories' => function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            }
        ])->get()->map(function ($item) {
            $item->current_stock = $item->inventories->first()?->stock ?? 0;
            return $item;
        });
    }

    public function index(Request $request)
    {
        $branches = Branch::all();
        $branch_id = $request->input('branch_id', env('BRANCH_ID'));
        $items = $this->getItems($branch_id);

        return view('inventory', compact('items', 'branches'));
    }
}
