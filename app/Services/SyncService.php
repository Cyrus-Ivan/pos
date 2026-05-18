<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class SyncService
{
    /**
     * Models in order of insertion dependency (parents before children).
     */
    protected array $models = [
        \App\Models\Branch::class,
        \App\Models\User::class,
        \App\Models\Item::class,
        \App\Models\Inventory::class,
        \App\Models\Transaction::class,
        \App\Models\Transaction_Item::class,
        \App\Models\Audit::class,
        \App\Models\LoginAudit::class,
    ];

    public function handle(): void
    {
        $this->pullFromRemote();
        $this->pushToRemote();
        $this->syncDeletions();
    }

    protected function pullFromRemote(): void
    {
        // Simple logic for pulling. We pull based on updated_at since the last successful sync pull.
        // We'll store the last pull timestamp in the cache.
        $lastPull = cache()->get('sync_last_pull', '1970-01-01 00:00:00');
        $currentPullTime = now()->toDateTimeString();

        foreach ($this->models as $modelClass) {
            /** @var Model $model */
            $model = new $modelClass;
            $table = $model->getTable();

            // Fetch records from Supabase updated since last pull
            $remoteRecords = DB::connection('supabase')
                ->table($table)
                ->where('updated_at', '>', $lastPull)
                ->orderBy('updated_at')
                ->chunk(500, function ($records) use ($modelClass, $table) {
                    if ($records->isEmpty()) return;

                    DB::transaction(function () use ($records, $modelClass, $table) {
                        $upsertData = [];
                        foreach ($records as $record) {
                            $data = (array) $record;
                            // Ensure local synced_at is null so if it needs re-syncing locally...? 
                            // No, if pulling FROM remote, it is deemed synced!
                            $data['synced_at'] = now()->toDateTimeString();
                            $upsertData[] = $data;
                        }

                        // Laravel upsert relies on primary or unique keys. We assume 'id' here.
                        $modelClass::upsert($upsertData, ['id']);
                    });
                });
        }

        cache()->put('sync_last_pull', $currentPullTime);
    }

    protected function pushToRemote(): void
    {
        foreach ($this->models as $modelClass) {
            $model = new $modelClass;
            $table = $model->getTable();

            $modelClass::unsynced()
                ->chunk(500, function ($localRecords) use ($table) {
                    if ($localRecords->isEmpty()) return;

                    $payload = [];
                    $syncLog = [];

                    foreach ($localRecords as $record) {
                        $attributes = $record->getAttributes();
                        // Remote doesn't have synced_at, so remove it
                        Arr::forget($attributes, 'synced_at');
                        $payload[] = $attributes;
                        $syncLog[] = $record->id;
                    }

                    // Push to Supabase transactionally on the remote side if possible, but upsert handles it
                    DB::connection('supabase')->table($table)->upsert($payload, ['id']);

                    // If successful, mark local as synced
                    DB::transaction(function () use ($table, $syncLog) {
                        DB::table($table)
                            ->whereIn('id', $syncLog)
                            ->update(['synced_at' => now()]);
                    });
                });
        }
    }

    protected function syncDeletions(): void
    {
        $deletionModels = [
            \App\Models\Transaction::class,
            \App\Models\Transaction_Item::class,
        ];

        foreach ($deletionModels as $modelClass) {
            $model = new $modelClass;
            $table = $model->getTable();

            // Find synced records locally
            $modelClass::whereNotNull('synced_at')
                ->select('id')
                ->chunkById(500, function ($records) use ($table) {
                    $localIds = $records->pluck('id')->toArray();
                    
                    if (empty($localIds)) return;

                    // Ask remote what IDs still exist
                    $remoteIds = DB::connection('supabase')
                        ->table($table)
                        ->whereIn('id', $localIds)
                        ->pluck('id')
                        ->toArray();

                    // If missing remotely, it has been deleted
                    $missingIds = array_diff($localIds, $remoteIds);

                    if (!empty($missingIds)) {
                        DB::transaction(function () use ($table, $missingIds) {
                            DB::table($table)->whereIn('id', $missingIds)->delete();
                        });
                    }
                });
        }
    }
}
