<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAudit extends Model
{
    protected $fillable = [
        'branch_id',
        'user_id',
        'photo_path',
        'photo_taken_at',
        'latitude',
        'longitude',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
