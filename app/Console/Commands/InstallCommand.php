<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'install package';

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
    public function handle()
    {
        try {
            // Generate permission
            $this->call('permission:generate', ['--force' => true]);

            // Generate super admin role
            $this->line('');
            $this->info('Starting generate Super admin Role');
            $this->line('');

            $data = config('permissions.super_admin', []);
            $superAdminRole = Role::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );

            $this->info('generate Super admin Role success');
            $this->line('name: ' . $superAdminRole->name);
            $this->line('slug: ' . $superAdminRole->slug);

            return 1;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
