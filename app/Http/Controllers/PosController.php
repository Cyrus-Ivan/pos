<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory;
use App\Models\Transaction;
use App\Models\Transaction_Item;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PosController extends Controller
{
    private function getItems(Request $request)
    {
        $query = Item::query()
            ->select(['id', 'sku', 'name', 'selling_price']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        return $query->with([
            'inventories' => function ($query) {
                $query->select(['item_id', 'branch_id', 'stock']);
                $query->where('branch_id', env('BRANCH_ID'));
            },
        ])
            ->paginate($request->input('per_page', 50))
            ->through(function ($item) {
                $item->stock = $item->inventories->first()?->stock ?? 0;

                return $item;
            })->withQueryString();
    }

    public function index(Request $request): View
    {
        $items = $this->getItems($request);

        return view('pos.select-items', compact('items'));
    }

    public function toggleItem(Request $request)
    {
        $request->validate([
            'id' => 'integer|required',
        ]);

        $selectedItems = session()->get('selectedItems', []);
        $id = $request->id;

        if (isset($selectedItems[$id])) {
            unset($selectedItems[$id]);
        } else {
            // Store ID and basic info so you don't have to query the DB again later
            $selectedItems[$id] = true;
        }

        session()->put('selectedItems', $selectedItems);

        return response()->json(['status' => 'success', 'count' => count($selectedItems)]);
    }

    public function confirmCheckoutView(Request $request)
    {
        $selectedItems = Item::whereIn('id', array_keys(session('selectedItems', [])))->get()->each(function ($item) {
            $item->stock = $item->inventories->first()?->stock ?? 0;
        });

        return view('pos.confirm-checkout', compact('selectedItems'));
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'payment_type' => 'required|in:cash,online',
            'items' => 'required|array',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric',
            'items.*.discount' => 'nullable|numeric',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $totalAmount = 0;
            $itemsPayload = $validated['items'];

            // Setup new Transaction parent record
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'branch_id' => env('BRANCH_ID'),
                'total_amount' => 0,
                'payment_type' => $request->payment_type
            ]);

            // Iterate payload mapping item_id to details
            foreach ($itemsPayload as $itemId => $details) {
                // Determine item calculation variables
                $price = $details['price'];
                $quantity = $details['quantity'];
                $discount = $details['discount'] ?? 0;

                if ($discount > ($price * $quantity)) {
                    throw ValidationException::withMessages([
                        "items" => "Discount cannot exceed total price for item id {$itemId}!"
                    ]);
                }

                $rowSubtotal = ($price * $quantity) - $discount;
                $totalAmount += $rowSubtotal;

                // 1. Pessimistic lock the inventory row for update to prevent race conditions
                /** @var \App\Models\Inventory $inventory */
                $inventory = Inventory::where('item_id', $itemId)
                    ->where('branch_id', env('BRANCH_ID', 1))
                    ->with('item')
                    ->lockForUpdate()
                    ->first();

                if (!$inventory || $inventory->stock < $quantity) {
                    throw ValidationException::withMessages([
                        "items" => "Insufficient stock for item id {$itemId}!"
                    ]);
                }

                // 2. Perform the stock deduction
                $inventory->stock -= $quantity;
                $inventory->save();

                // 3. Create the log entry for Transaction_Item
                Transaction_Item::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $itemId,
                    'quantity' => $quantity,
                    'cost' => $inventory->item->cost,
                    'selling_price' => $price,
                    'discount' => $discount,
                ]);
            }

            // Seal logic updates
            $transaction->update(['total_amount' => $totalAmount]);

            // Clear checkout cart
            $request->session()->forget('selectedItems');

            session()->flash('success', 'Checkout successful!');

            // Dispatch response payload
            return response()->json([
                'message' => 'Checkout processed successfully.',
                'redirect_url' => route('pos')
            ]);
        });
    }
}
