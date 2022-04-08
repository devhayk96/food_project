<?php

namespace App\Console\Commands;

use App\Locale\PoshubLocale;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

abstract class AbstractCommand extends Command
{
    public const LOG_CHANNEL = 'console';

    protected \DateTime $startDateTime;

    protected PoshubLocale $locale;

    public function __construct()
    {
        parent::__construct();
        $this->locale = new PoshubLocale();
    }

    protected function startCommandAndLogTime()
    {
        Log::channel(self::LOG_CHANNEL)->info($this->signature . ' STARTED.');
        $this->startDateTime = new \DateTime();
    }

    protected function completeSuccessfullyCommandAndLogTime(): int
    {
        $end = new \DateTime();
        $duration = $end->diff($this->startDateTime);

        Log::channel(self::LOG_CHANNEL)->info(
            $this->signature . ' COMPLETED in ' . $duration->format('%s') . ' seconds'
        );

        return 0;
    }
}
