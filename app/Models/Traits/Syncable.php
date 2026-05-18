<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Syncable
{
    /**
     * Boot the trait and hook into Eloquent events.
     */
    public static function bootSyncable(): void
    {
        static::saving(function ($model) {
            // Whenever a record is saved/updated locally, mark it as dirty
            $model->synced_at = null;
        });
    }

    /**
     * Scope a query to only include records that haven't been synced to Supabase yet.
     */
    public function scopeUnsynced(Builder $query): Builder
    {
        return $query->whereNull('synced_at');
    }
}
