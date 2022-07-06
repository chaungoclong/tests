<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class GeneratePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:generate {--f|force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command generate permission';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $this->info('Starting generate Permission');
            $this->line('');

            // If force option equal true then truncate permission table
            if ($this->option('force')) {
                Permission::truncate();
            }
            $data = getPermissionsFromRoute();
            Permission::upsert($data, ['slug'], ['slug']);

            $this->info('Generate Permission Success');

            // Show info
            foreach ($data as $key => $item) {
                $content = ($key + 1).'||'.$item['name'].'||'.$item['slug'].'||'.$item['action'];
                $row = str_repeat('=', strlen($content));
                $this->line($row);
                $this->info($content);
            }

            return 1;
        } catch (\Exception $e) {
            $this->error('Generate Permission Failed');

            return 0;
        }
    }
}
