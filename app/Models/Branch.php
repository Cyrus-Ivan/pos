<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'code', 'name', 'address'];

    public function loginAudits()
    {
        return $this->hasMany(LoginAudit::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
