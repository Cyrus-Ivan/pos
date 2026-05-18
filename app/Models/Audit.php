<?php

namespace App\Models;

use App\Models\Traits\Syncable;

use OwenIt\Auditing\Models\Audit as AuditBase;

class Audit extends AuditBase
{
    use Syncable;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($audit) {
            $audit->branch_id = auth()->user()?->branch_id ?? env('BRANCH_ID'); // Ensure it falls back safely
        });
    }
}