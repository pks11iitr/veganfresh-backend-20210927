<?php

namespace App\Console\Commands;


use Kodeine\Acl\Models\Eloquent\Permission;

use Illuminate\Console\Command;

class CreatePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'dashboard',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);
        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'banner',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'configuration',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'product',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'homesection',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'category',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'subcategory',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'customer',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'coupon',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'customer',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'order',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'sale',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'inventory',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'complaint',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'notification',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'return',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'timeslot',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'arealist',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'rider',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'store',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);

        $permission = new Permission();
        $permUser = $permission->create([
            'name'        => 'subadmin',
            'slug'        => [          // pass an array of permissions.
                'create'     => true,
                'view'       => true,
                'update'     => true,
                'delete'     => true,
            ],
            'description' => 'manage banner permissions'
        ]);
    }
}
