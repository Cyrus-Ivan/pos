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

    public function create(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:items,sku|max:16',
            'item-name' => 'required|string|max:255',
            'item-cost' => 'required|numeric|min:0',
            'selling-price' => 'required|numeric|min:0',
            'stocks' => 'required|array',
            'stocks.*' => 'required|integer|min:0',
        ]);

        $item = Item::create([
            'sku' => $validated['sku'],
            'item_name' => $validated['item-name'],
            'cost' => $validated['item-cost'],
            'selling_price' => $validated['selling-price'],
        ]);

        foreach ($validated['stocks'] as $branch_id => $stock) {
            Inventory::create([
                'item_id' => $item->id,
                'branch_id' => $branch_id,
                'stock' => $stock,
            ]);
        }

        return redirect()->back()->with('success', 'Item created successfully!');
    }
}
