<?php

namespace App\Models;

use App\Models\Traits\Syncable;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use Syncable;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'synced_at','id', 'name', 'address'];

    public function loginAudits()
    {
        return $this->hasMany(LoginAudit::class, 'branch_id', 'id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
