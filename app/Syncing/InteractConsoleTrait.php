<?php

namespace App\Syncing;

trait InteractConsoleTrait
{
    protected $console;

    public function __construct()
    {
    }

    public function enableConsole($console)
    {
        $this->console = $console;
    }


    protected function prepareProgressBar($count)
    {
        if($this->console){
            $this->console->getOutput()->progressStart($count);
        }
    }

    protected function tellInfo($string)
    {
        if($this->console){
            $this->console->info($string );
        }
    }

    private function progressBar()
    {
        if($this->console) {
            $this->console->getOutput()->progressAdvance();
        }
    }

    protected function finishProgress()
    {
        if($this->console) {
            $this->console->getOutput()->progressFinish();
        }
    }
}