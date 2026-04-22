<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class Inventory extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\InventoryFactory> */
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

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
