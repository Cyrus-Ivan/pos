<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;

class MigrateSupabase extends Command
{
    protected $signature = 'migrate:supabase {--fresh}';
    protected $description = 'Run Laravel migrations against Supabase';

    protected array $exclude = [
        'create_jobs_table'
    ];

    public function handle()
    {
        $this->info('Running migrations on Supabase...');

        $migrationPath = database_path('migrations');
        $files = glob($migrationPath . '/*.php');

        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);

            foreach ($this->exclude as $excluded) {
                if (str_contains($filename, $excluded)) {
                    $this->line("Skipping: {$filename}");
                    continue 2;
                }
            }

            require_once $file;
        }

        $options = [
            '--database' => 'supabase',
            '--force' => true,
            '--path' => $this->getFilteredPaths(),
        ];

        if ($this->option('fresh')) {
            $this->call('migrate:fresh', $options);
        } else {
            $this->call('migrate', $options);
        }

        $this->info('Supabase migrations complete.');
    }
    protected function getFilteredPaths(): array
    {
        $migrationPath = database_path('migrations');
        $files = glob($migrationPath . '/*.php');
        $paths = [];

        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);

            $excluded = false;
            foreach ($this->exclude as $excludedName) {
                if (str_contains($filename, $excludedName)) {
                    $excluded = true;
                    break;
                }
            }

            if (!$excluded) {
                // Use relative path from base_path instead of absolute
                $paths[] = 'database/migrations/' . basename($file);
            }
        }

        return $paths;
    }
}