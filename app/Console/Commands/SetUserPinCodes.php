<?php

namespace App\Console\Commands;

use App\Helpers\Util;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SetUserPinCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:set-pin-code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Generating a PIN code for those users who don't have";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        try {
            $users = User::query()
                ->where('pin_code', '=', '')
                ->orWhereNull('pin_code');

            if ($users->exists()) {
                $users->get()->map(function (User $user){
                    $user->update(['pin_code' => Util::generatePinCode()]);
                });
                echo "Generated pin code for those users who don't have";
            } else {
                echo "There are no users who has not pin code";
            }
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
