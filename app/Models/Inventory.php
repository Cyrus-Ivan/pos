<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    /** @use HasFactory<\Database\Factories\InventoryFactory> */
    use HasFactory;

    protected $fillable = [
        'item_id',
        'branch_id',
        'stock',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
