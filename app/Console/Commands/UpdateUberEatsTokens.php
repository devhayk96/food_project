<?php

namespace App\Console\Commands;

class UpdateUberEatsTokens extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ubereats:update-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update UberEats token.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->startCommandAndLogTime();
        return $this->completeSuccessfullyCommandAndLogTime();
    }
}
