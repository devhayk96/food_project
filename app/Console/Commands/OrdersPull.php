<?php

namespace App\Console\Commands;

use App\Models\OrderSourceShop;
use App\Models\OrderSourceType;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;

class OrdersPull extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:pull';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull new orders from all the active order sources.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->startCommandAndLogTime();
        $sources = OrderSourceShop::getWhereSourceAndSourceTypeAreActive();
        $sourcesNumber = count($sources);
        Log::channel('console')->info($this->signature . ' Found ' . $sourcesNumber . ' sources.');

        /**
         * @var OrderSourceShop $source
         */
        foreach ($sources as $source) {
            if ($source->is_active === false) {
                Log::channel('console')->info(
                    $this->signature . ' Skipped source ' . $source->name . ' because is not active.'
                );
                continue;
            }
            if ($source->orderSourceType->code === OrderSourceType::FOODYX) {
                continue;
            }
            Log::channel('console')->info($this->signature . ' Processing source ' . $source->name);
            $sourceType = $source->orderSourceType;
            $shops = [$source->shops];
            $shopsNumber = count($shops);
            Log::channel('console')->info($this->signature . ' Found ' . $shopsNumber . ' sources.');
            /**
             * @var Shop $shop
             */
            foreach ($shops as $shop) {
                Log::channel('console')->info(
                    $this->signature . ' Processing shop ' . $shop->name
                );
                if ($shop->is_open === false) {
                    Log::channel('console')->info(
                        $this->signature . ' Skipped shop ' . $shop->name . ' because is closed.'
                    );
                    continue;
                }
               
                $sourceType->client_class::make()
                    ->initClient($source, $shop)
                    ->getOrdersInTransit()
                    ->processOrdersInTransit();

                Log::channel('console')->info(
                    $this->signature . ' Completed shop ' . $shop->name
                );
            }
            Log::channel('console')->info($this->signature . ' Completed source ' . $source->name);
        }

        return $this->completeSuccessfullyCommandAndLogTime();
    }
}
