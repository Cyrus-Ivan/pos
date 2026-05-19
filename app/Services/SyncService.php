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
                    if ($records->isEmpty())
                        return;

                    DB::transaction(function () use ($records, $modelClass, $table) {
                        $upsertData = [];
                        foreach ($records as $record) {
                            $data = (array) $record;
                            $data['synced_at'] = now()->toDateTimeString();
                            $upsertData[] = $data;
                        }

                        // Determine the unique key(s) for the model
                        $uniqueKeys = (new $modelClass)->getKeyName();
                        if ($table === 'transaction_items') {
                            $uniqueKeys = ['transaction_id', 'item_id', 'type'];
                        } else {
                            $uniqueKeys = (array) $uniqueKeys;
                        }

                        $modelClass::upsert($upsertData, $uniqueKeys);
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

            $query = $modelClass::unsynced();

            // Override chunk ordering for models without a standard 'id'
            if ($table === 'transaction_items') {
                $query->orderBy('created_at');
            }

            $query->chunk(500, function ($localRecords) use ($table, $modelClass) {
                if ($localRecords->isEmpty())
                    return;

                $payload = [];
                $syncLog = [];

                foreach ($localRecords as $record) {
                    $attributes = $record->getAttributes();
                    Arr::forget($attributes, 'synced_at');
                    $payload[] = $attributes;

                    if ($table === 'transaction_items') {
                        $syncLog[] = [
                            'transaction_id' => $record->transaction_id,
                            'item_id' => $record->item_id,
                            'type' => $record->type,
                        ];
                    } else {
                        $syncLog[] = $record->id;
                    }
                }

                $uniqueKeys = (new $modelClass)->getKeyName();
                if ($table === 'transaction_items') {
                    $uniqueKeys = ['transaction_id', 'item_id', 'type'];
                } else {
                    $uniqueKeys = (array) $uniqueKeys;
                }

                DB::connection('supabase')->table($table)->upsert($payload, $uniqueKeys);

                DB::transaction(function () use ($table, $syncLog) {
                    if ($table === 'transaction_items') {
                        foreach ($syncLog as $keys) {
                            DB::table($table)
                                ->where('transaction_id', $keys['transaction_id'])
                                ->where('item_id', $keys['item_id'])
                                ->where('type', $keys['type'])
                                ->update(['synced_at' => now()]);
                        }
                    } else {
                        DB::table($table)
                            ->whereIn('id', $syncLog)
                            ->update(['synced_at' => now()]);
                    }
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

            if ($table === 'transaction_items') {
                // For transaction_items, composite keys make mass deletion complex to check
                // For safety and performance, we'll verify transaction existence instead,
                // as transaction_items cascade delete based on transaction_id.
                // If a transaction_item was individually deleted remote, we might need a 
                // different check, but usually the parent transaction is deleted.
                continue; // Skip transaction_items specific deletion sync, let cascade handle it
            }

            // Find synced records locally
            $modelClass::whereNotNull('synced_at')
                ->select('id')
                ->chunkById(500, function ($records) use ($table) {
                    $localIds = $records->pluck('id')->toArray();

                    if (empty($localIds))
                        return;

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
