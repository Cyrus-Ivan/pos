<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SyncService;
use Exception;
use Illuminate\Database\QueryException;

class SyncDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sync:database';

    /**
     * The console command description.
     */
    protected $description = 'Synchronize data bidirectionally between SQLite and Supabase';

    /**
     * Execute the console command.
     */
    public function handle(SyncService $syncService)
    {
        $this->info('Starting database sync...');

        try {
            $syncService->handle();
            $this->info('Sync completed successfully!');
        } catch (QueryException $e) {
            // Check if it's a network/connection issue (e.g. pgsql connection refused)
            $message = $e->getMessage();
            if (str_contains($message, 'Connection refused') || str_contains($message, 'could not translate host name') || str_contains($message, 'network is unreachable') || str_contains($message, 'timeout')) {
                // We are offline; fail silently
                $this->warn('System is currently offline. Skipping sync.');
                return Command::SUCCESS;
            }

            // Otherwise, it's a real SQL error, report it
            $this->error("Sync failed due to query exception: {$message}");
            return Command::FAILURE;
        } catch (Exception $e) {
            $this->error("Sync failed: {$e->getMessage()}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
