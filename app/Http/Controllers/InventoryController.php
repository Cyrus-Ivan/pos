<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'name' => 'required|string|max:255',
            'item-cost' => 'required|numeric|min:0',
            'selling-price' => 'required|numeric|min:0',
            'stocks' => 'required|array',
            'stocks.*' => 'required|integer|min:0',
        ]);

        $item = Item::create([
            'sku' => $validated['sku'],
            'name' => $validated['name'],
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

    public function update(Request $request)
    {
        // 1. Validate incoming data including the toggle mode (add or set)
        $validated = $request->validate([
            'id' => 'required|exists:items,id',
            'item-cost' => 'required|numeric|min:0',
            'selling-price' => 'required|numeric|min:0',
            'stocks' => 'required|array',
            'stocks.*' => 'required|integer|min:0',
            'stock_mode' => 'nullable|array',
            'stock_mode.*' => 'in:add,set',
        ]);

        $item = Item::findOrFail($validated['id']);

        // 2. Wrap all database operations in a transaction to ensure atomicity
        $changesMade = DB::transaction(function () use ($item, $validated) {
            // 3. Update the basic item attributes (cost and price)
            $item->fill([
                'cost' => $validated['item-cost'],
                'selling_price' => $validated['selling-price'],
            ]);

            $changed = false;

            // Only perform a save query if the cost or price actually changed
            if ($item->isDirty()) {
                $item->save();
                $changed = true;
            }

            // 4. Iterate over each branch's stock payload
            foreach ($validated['stocks'] as $branch_id => $stockValue) {
                // Default to 'add' mode if no explicit mode was passed for this branch
                $mode = $validated['stock_mode'][$branch_id] ?? 'add';

                // Fetch existing inventory or initialize a new record for this branch
                $inventory = Inventory::firstOrNew([
                    'item_id' => $item->id,
                    'branch_id' => $branch_id,
                ]);

                // 5. Apply the correct logic based on the requested mode
                if ($mode === 'add') {
                    // Add to the current stock (Skip math if they submitted a 0 value to avoid a wasteful save)
                    if ((int) $stockValue !== 0) {
                        $inventory->stock = ($inventory->stock ?? 0) + (int) $stockValue;
                    }
                } elseif ($mode === 'set') {
                    // Hard override the stock to the exact value submitted
                    $inventory->stock = (int) $stockValue;
                }

                // 6. Only hit the database if the stock actually fluctuated
                if ($inventory->isDirty()) {
                    $inventory->save();
                    $changed = true;
                }
            }

            return $changed; // Transmits the result out of the transaction block
        });

        // 7. Return feedback corresponding to whether any data was realistically edited
        if (!$changesMade) {
            return redirect()->back()->with('info', 'No changes were made to the item.');
        }

        return redirect()->back()->with('success', 'Item updated successfully!');
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:items,id',
            'confirm' => 'required|in:confirm'
        ]);

        $item = Item::findOrFail($validated['id']);

        DB::transaction(function () use ($item) {
            $item->inventories()->delete(); // clear associated inventory first!
            $item->delete(); // then delete the item
        });

        return redirect()->back()->with('success', 'Item deleted successfully!');
    }
}
