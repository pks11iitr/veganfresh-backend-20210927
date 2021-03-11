<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DBBackupOnS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backupdb:s3';

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
        $host = env('DB_HOST');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $database = env('DB_DATABASE');

        $path = Storage::disk('backups')->path('database');

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        Storage::disk('backups')->delete('database/dbbackups3.mysql');

        $command = sprintf('mysqldump -h %s -u %s -p\'%s\' %s > %s', $host, $username, $password, $database, $path.'/dbbackups3.mysql');

        exec($command);

//        if(Storage::disk('s3')->exists('database/dbbackups3.mysql')){
//            Storage::disk('s3')->delete('database/dbbackups3.mysql');
//        }

        Storage::disk('s3')->put('database/dbbackups3-'.date('Y-m-d-H-i-s').'.mysql', Storage::disk('backups')->get('database/dbbackups3.mysql'), 'private');

        Storage::disk('backups')->delete('database/dbbackups3.mysql');

    }
}
