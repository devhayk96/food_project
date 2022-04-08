<?php

namespace App\Console\Commands;

use App\Exceptions\ModelException;
use App\HasCliUser;
use App\Locale\PoshubLocale;
use App\Models\Shop;
use App\Models\User;
use App\Models\WorkDay;
use App\Models\WorkHours;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Class ShopsPulse
 *
 * This object is supposed to work as an heartbeat to perform all the operations for the shop such as:
 * - Open the shops based on the work hours
 * - Close the shops based on the work hours
 * - ...
 *
 * @package App\Console\Commands
 */
class ShopsPulse extends AbstractCommand
{
    use HasCliUser;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shops:pulse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform temporized operations for the shops.';

    protected PoshubLocale $locale;

    protected Carbon $today;

    protected User $user;

    protected string $hour;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->locale = new PoshubLocale();
        $this->today = $this->locale->getCarbonNowLocaleTz();
        $this->hour = $this->today->format('H:i');
    }

    /**
     * Execute the console command.
     *
     * 1- Select al the active shop
     * 2- foreach shop
     *  2.1- select workday where date is today
     *  2.2- if empty, try selecting yesterday
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->startCommandAndLogTime();
            $this->user = $this->getCurrentUserOrCli();
            $activeShops = Shop::where('is_active', '=', true)->get();

            foreach ($activeShops as $activeShop) {
                if ((bool)$activeShop->is_open === false) {
                    $this->tryOpenShop($activeShop);
                } else {
                    $this->tryCloseShop($activeShop);
                }
                if ((bool)$activeShop->is_delivering === false) {
                    $this->tryOpenDelivery($activeShop);
                } else {
                    $this->tryCloseDelivery($activeShop);
                }
            }
        } catch (\Throwable $exception) {
            Log::channel(self::LOG_CHANNEL)->error($this->signature . " ERROR executing handle");
            Log::channel(self::LOG_CHANNEL)->error(
                $exception->getCode() . ' - ' . $exception->getMessage()
            );
            Log::channel(self::LOG_CHANNEL)->error($exception->getTraceAsString());
        }
        return $this->completeSuccessfullyCommandAndLogTime();
    }

    /**
     * @param  Shop           $shop
     * @return bool
     * @throws ModelException
     */
    protected function tryOpenShop(Shop $shop): bool
    {
        Log::channel(self::LOG_CHANNEL)->info($this->signature . " Try opening shop id " . $shop->id);

        $wHours = WorkHours::where([
            'shop_id' => $shop->id,
            'day' => $this->today->format('w'),
            'type' => WorkHours::TYPE_OPENING,
            'opening_hour' => $this->hour,
            'is_open' => true
        ])->first();
        if (empty($wHours)) {
            Log::channel(self::LOG_CHANNEL)->info(
                $this->signature . " " . $this->hour . " is not an opening hour"
            );
            return false;
        }

        $result = $shop->openShop($shop, $this->user);

        if ($result === false) {
            Log::channel(self::LOG_CHANNEL)->error(
                $this->signature . " Failed opening shop id " . $shop->id
            );
            return false;
        }
        Log::channel(self::LOG_CHANNEL)->info(
            $this->signature . " Successfully opened shop id " . $shop->id
        );
        return true;
    }

    protected function tryOpenDelivery(Shop $shop): bool
    {
        $wHours = WorkHours::where([
            'shop_id' => $shop->id,
            'day' => $this->today->format('w'),
            'type' => WorkHours::TYPE_DELIVERY,
            'opening_hour' => $this->hour,
            'is_open' => true
        ])->first();
        if (empty($wHours)) {
            Log::channel(self::LOG_CHANNEL)->info(
                $this->signature . " " . $this->hour . " is not a delivery hour"
            );
            return false;
        }

        $result = $shop->openDeliveryShop($shop, $this->user);

        if ($result === false) {
            Log::channel(self::LOG_CHANNEL)->error(
                $this->signature . " Failed opening delivery for shop id " . $shop->id
            );
            return false;
        }
        Log::channel(self::LOG_CHANNEL)->info(
            $this->signature . " Successfully opened delivery for shop id " . $shop->id
        );
        return true;
    }

    /**
     * @param  Shop           $shop
     * @return bool
     * @throws ModelException
     */
    protected function tryCloseShop(Shop $shop): bool
    {
        $wHours = WorkHours::where([
            'shop_id' => $shop->id,
            'day' => $this->today->format('w'),
            'type' => WorkHours::TYPE_OPENING,
            'closing_hour' => $this->hour,
            'is_open' => true
        ])->first();
        if (empty($wHours)) {
            Log::channel(self::LOG_CHANNEL)->info(
                $this->signature . " " . $this->hour . " is not a closing hour"
            );
            return false;
        }

        $result = $shop->closeShop($shop, $this->user);

        if ($result === false) {
            Log::channel(self::LOG_CHANNEL)->error(
                $this->signature . " Failed closing shop id " . $shop->id
            );
            return false;
        }
        Log::channel(self::LOG_CHANNEL)->info(
            $this->signature . " Successfully closed shop id " . $shop->id
        );
        return true;

    }

    protected function tryCloseDelivery(Shop $shop): bool
    {
        $wHours = WorkHours::where([
            'shop_id' => $shop->id,
            'day' => $this->today->format('w'),
            'type' => WorkHours::TYPE_DELIVERY,
            'closing_hour' => $this->hour,
            'is_open' => true
        ])->first();
        if (empty($wHours)) {
            Log::channel(self::LOG_CHANNEL)->info(
                $this->signature . " " . $this->hour . " is not a closing delivery hour"
            );
            return false;
        }

        $result = $shop->closeDeliveryShop($shop, $this->user);

        if ($result === false) {
            Log::channel(self::LOG_CHANNEL)->error(
                $this->signature . " Failed closing delivery for shop id " . $shop->id
            );
            return false;
        }
        Log::channel(self::LOG_CHANNEL)->info(
            $this->signature . " Successfully closing delivery for shop id " . $shop->id
        );
        return true;
    }
}
