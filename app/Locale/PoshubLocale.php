<?php

namespace App\Locale;

use App\Exceptions\LocaleException;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;

class PoshubLocale
{
    public const DTF_POSHUB_LOCALE = 'poshub_locale';

    public const DTF_POSHUB_SYSTEM = 'poshub_system';

    public const DTF_THUISBEZORGD_ORDER = 'thuisbezorgd_order_received';

    public static array $validDtFormatTypes = [
        self::DTF_POSHUB_LOCALE,
        self::DTF_POSHUB_SYSTEM,
        self::DTF_THUISBEZORGD_ORDER
    ];

    public const TZ_POSHUB_LOCALE = 'poshub_locale';

    public const TZ_POSHUB_SYSTEM = 'poshub_system';

    public static array $validTimezoneTypes = [
        self::TZ_POSHUB_LOCALE,
        self::TZ_POSHUB_SYSTEM
    ];

    public int $decimals;

    public string $decimalSeparator;

    public string $thousandSeparator;

    public string $currencySymbol;

    public string $poshubSystemDtFormat;

    public string $poshubSystemTz;

    public CarbonTimeZone $poshubSystemCarbonTz;

    public string $poshubLocaleDtFormat;

    public string $poshubLocaleTz;

    public CarbonTimeZone $poshubLocaleCarbonTz;

    public string $thuisbezorgdReceivedOrderDtFormat;

    public function __construct()
    {
        $this->decimals = (int)config('app.locale_decimals');
        $this->decimalSeparator = config('app.locale_decimal_separator');
        $this->thousandSeparator = config('app.locale_thousand_separator');
        $this->currencySymbol = config('app.locale_currency_symbol');
        $this->poshubSystemDtFormat = config('app.datetime_format');
        $this->poshubSystemTz = config('app.timezone');
        $this->poshubSystemCarbonTz = new CarbonTimeZone($this->poshubSystemTz);
        $this->poshubLocaleDtFormat = config('app.locale_datetime_format');
        $this->poshubLocaleTz = config('app.locale_timezone');
        $this->poshubLocaleCarbonTz = new CarbonTimeZone($this->poshubLocaleTz);
        $this->thuisbezorgdReceivedOrderDtFormat = config('app.thuisbezorgd_received_order_datetime_format');
    }

    public static function make(): PoshubLocale
    {
        return new PoshubLocale();
    }

    public function formatPrice(float $price): string
    {
        return number_format($price, $this->decimals, $this->decimalSeparator, $this->thousandSeparator);
    }

    public function formatPriceWithCurrency(float $price): string
    {
        return $this->currencySymbol . ' ' . $this->formatPrice($price);
    }

    /**
     * @param  string          $dateTime
     * @param  string          $dtFormatType
     * @param  string          $tzType
     * @return Carbon|false
     * @throws LocaleException
     */
    public function getCarbon(string $dateTime, string $dtFormatType, string $tzType): Carbon
    {
        return Carbon::createFromFormat(
            $this->getDtFormatType($dtFormatType),
            $dateTime,
            $this->getTimezoneString($tzType)
        );
    }

    /**
     * @param  string          $dateTime
     * @return Carbon|false
     * @throws LocaleException
     */
    public function getCarbonFromLocale(string $dateTime): Carbon
    {
        return $this->getCarbon(
            $dateTime,
            self::DTF_POSHUB_LOCALE,
            self::TZ_POSHUB_LOCALE
        );
    }

    /**
     * @param  string          $dateTime
     * @return Carbon|false
     * @throws LocaleException
     */
    public function getCarbonFromSystem(string $dateTime): Carbon
    {
        return $this->getCarbon(
            $dateTime,
            PoshubLocale::DTF_POSHUB_SYSTEM,
            PoshubLocale::TZ_POSHUB_SYSTEM
        );
    }

    /**
     * @param  string          $dateTime
     * @return Carbon|false
     * @throws LocaleException
     */
    public function getCarbonFromSystemToLocaleTz(string $dateTime): Carbon
    {
        return $this->getCarbonFromSystem($dateTime)
            ->setTimezone($this->poshubLocaleTz);
    }

    /**
     * @param  string          $dateTime
     * @return string
     * @throws LocaleException
     */
    public function getStringFromSystemToLocaleTzToDtfSystem(string $dateTime): string
    {
        return $this->getCarbonFromSystemToLocaleTz($dateTime)
            ->format($this->poshubSystemDtFormat);
    }

    /**
     * @param  string          $dateTime
     * @return string
     * @throws LocaleException
     */
    public function getStringFromSystemToLocaleTzToDtfLocale(string $dateTime): string
    {
        return $this->getCarbonFromSystemToLocaleTz($dateTime)
            ->format($this->poshubLocaleDtFormat);
    }

    /**
     * @param  string          $dateTime
     * @return string
     * @throws LocaleException
     */
    public function getStringFromSystemToLocaleTzToDtfDateOnly(string $dateTime): string
    {
        return $this->getCarbonFromSystemToLocaleTz($dateTime)
            ->format('Y-m-d');
    }

    /**
     * @param  string          $dateTime
     * @return string
     * @throws LocaleException
     */
    public function getStringFromSystemToLocaleTzToDtfTimeOnly(string $dateTime): string
    {
        return $this->getCarbonFromSystemToLocaleTz($dateTime)
            ->format('H:i');
    }

    /**
     * @param  string          $dateTime
     * @return string
     * @throws LocaleException
     */
    public function getStringFromSystemToAmPmTimeOnly(string $dateTime): string
    {
        return $this->getCarbonFromSystemToLocaleTz($dateTime)
            ->format('g:i A');
    }



    public function getCarbonNowLocaleTz(): Carbon
    {
        return Carbon::now($this->poshubLocaleCarbonTz);
    }

    /**
     * @param  string          $dateTime
     * @return Carbon|false
     * @throws LocaleException
     */
    public function getCarbonFromThuisbezorgdDtfAndSystemTz(string $dateTime): Carbon
    {
        return $this->getCarbon(
            $dateTime,
            PoshubLocale::DTF_THUISBEZORGD_ORDER,
            PoshubLocale::TZ_POSHUB_SYSTEM
        );
    }

    /**
     * @param  string          $dateTime
     * @return string
     * @throws LocaleException
     */
    public function getStringForThuisbezorgdDtf(string $dateTime): string
    {
        return $this->getCarbonFromThuisbezorgdDtfAndSystemTz($dateTime)
            ->format($this->poshubSystemDtFormat);
    }

    public function getCarbonNowSystemTz(): Carbon
    {
        return Carbon::now()->timezone($this->poshubSystemCarbonTz);
    }

    public function getStringNowSystemDtfSystemTz(): string
    {
        return $this->getCarbonNowSystemTz()->format($this->poshubSystemDtFormat);
    }

    /**
     * @param  string          $dtFormatType
     * @return string
     * @throws LocaleException
     */
    protected function getDtFormatType(string $dtFormatType): string
    {
        if (in_array($dtFormatType, self::$validDtFormatTypes) === false) {
            throw new LocaleException("Invalid datetime format type: " . $dtFormatType, 1);
        }

        switch ($dtFormatType) {
            case self::DTF_POSHUB_LOCALE:
                return $this->poshubLocaleDtFormat;
            case self::DTF_POSHUB_SYSTEM:
                return $this->poshubSystemDtFormat;
            case self::DTF_THUISBEZORGD_ORDER:
                return $this->thuisbezorgdReceivedOrderDtFormat;
            default:
                throw new LocaleException('Unused datetime format type: ' . $dtFormatType, 2);
        }
    }

    /**
     * @param  string          $timezoneType
     * @return string
     * @throws LocaleException
     */
    protected function getTimezoneString(string $timezoneType): string
    {
        if (in_array($timezoneType, self::$validTimezoneTypes) === false) {
            throw new LocaleException("Invalid timezone type: " . $timezoneType, 3);
        }

        switch ($timezoneType) {
            case self::TZ_POSHUB_LOCALE:
                return $this->poshubLocaleTz;
            case self::TZ_POSHUB_SYSTEM:
                return $this->poshubSystemTz;
            default:
                throw new LocaleException('Unused timezone type: ' . $timezoneType, 4);
        }
    }
}
