<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{

    protected $fillable = ['code', 'name', 'address'];

    public function loginAudits()
    {
        return $this->hasMany(LoginAudit::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
