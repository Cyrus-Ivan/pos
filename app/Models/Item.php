<?php

namespace App\Models;

use App\Models\Traits\Syncable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use Syncable;

    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'synced_at',
        'sku',
        'name',
        'cost',
        'selling_price',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'selling_price' => 'decimal:2',
        ];
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(Transaction_Item::class);
    }
}
