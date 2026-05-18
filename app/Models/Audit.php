<?php

namespace App\Models;

use OwenIt\Auditing\Models\Audit as AuditBase;

class Audit extends AuditBase
{
    protected static function booting()
    {
        parent::booting();

        static::creating(function ($audit) {
            $audit->branch_id = env('BRANCH_ID'); // or however you store the current branch
        });
    }
}