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
}
