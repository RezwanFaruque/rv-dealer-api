<?php

namespace App\Console\Commands;

use App\Syncing\Syncer;
use Illuminate\Console\Command;

class SyncOnly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:only {filter*} {--s|sold}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync local data with rvusa api...';

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
        $filter_argument = $this->argument('filter') ;
        $filters['is_sold'] =  ($this->option('sold')) ? $this->option('sold') : false;

        if( $filter_argument) {
           foreach ($filter_argument as $value)
           {
               $parts = explode('=', $value);
               $filters[ $parts[0]] = $parts[1];
           }
       }

       $syncer = new Syncer();
       $syncer->enableConsole($this);
       $syncer->incremental($filters);

    }
}
