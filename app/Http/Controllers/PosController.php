<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->through(function ($item) use ($request) {
                $item->stock = $item->inventories->first()?->stock ?? 0;

                return $item;
            })->withQueryString();
    }
    public function index(Request $request): View
    {
        $items = $this->getItems($request);
        return view('pos', compact('items'));
    }
    public function toggleItem(Request $request)
    {
        $request->validate([
            'id' => 'integer|required',
        ]);

        $cart = session()->get('cart', []);
        $id = $request->id;

        if (isset($cart[$id])) {
            unset($cart[$id]);
        } else {
            // Store ID and basic info so you don't have to query the DB again later
            $cart[$id] = true;
        }

        session()->put('cart', $cart);
        return response()->json(['status' => 'success', 'count' => count($cart)]);
    }

    public function checkout(Request $request)
    {
        // Retrieve the cart from the session or database
        $cart = session()->get('cart', []);

        // Dump and die to see the data in your Network Tab
        dd([
            'message' => 'Checkout triggered successfully',
            'cart_contents' => $cart,
            'total_items' => count($cart),
        ]);
    }
}
