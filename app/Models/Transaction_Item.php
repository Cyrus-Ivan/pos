<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction_Item extends Model
{
    protected $table = 'transaction_items';
    protected $fillable = [
        'transaction_id',
        'item_id',
        'quantity',
        'cost',
        'selling_price',
        'discount',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'cost' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'discount' => 'decimal:2',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
