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
        $current_branch = Branch::find(session('branch_id'));
        $items = Item::all()->where();
        return view('inventory', compact('inventory', 'items', 'current_branch', 'branches'));
    }
}
