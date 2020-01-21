<?php

namespace App\Console\Commands;

use App\Syncing\Syncer;
use Illuminate\Console\Command;

class SyncAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:all {--s|sold : Sync only sold unites}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all unites for all brands...';

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
        $is_sold = ($this->option('sold')) ? $this->option('sold') : false;
        $syncer = new Syncer();
        $syncer->enableConsole($this);
        ($is_sold) ? $syncer->allSold() : $syncer->allInventory();

    }
}
