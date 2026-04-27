<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    private function getItems()
    {
        return Item::query()
            ->select(['id', 'sku', 'name', 'cost', 'selling_price'])
            ->with([
                'inventories' => function ($query) {
                    $query->select(['item_id', 'branch_id', 'stock']);
                },
            ])
            ->get()
            ->map(function ($item) {
                $item->inventories->each(function ($inventory) use ($item) {
                    $item->setAttribute($inventory->branch_id, $inventory->stock);
                });

                $item->current_stock = $item->inventories->first()?->stock ?? 0;

                return $item;
            });
    }

    public function index(Request $request): View
    {
        $branches = Branch::all();
        $items = $this->getItems();

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
