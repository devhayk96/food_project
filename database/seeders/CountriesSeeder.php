<?php

namespace Database\Seeders;

use App\Models\Country;
use Countries;
use DB;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return  void
     */
    public function run()
    {
        $countriesTableName = \Config::get('countries.table_name');
        $countries = Countries::getList();
        foreach ($countries as $countryId => $country) {
            $countryData = [
                'id' => $countryId,
                'capital' => $country['capital'] ?? null,
                'citizenship' => $country['citizenship'] ?? null,
                'country_code' => $country['country-code'] ?? '',
                'currency' => $country['currency'] ?? null,
                'currency_code' => $country['currency_code'] ?? null,
                'currency_sub_unit' => $country['currency_sub_unit'] ?? null,
                'currency_decimals' => $country['currency_decimals'] ?? null,
                'full_name' => $country['full_name'] ?? null,
                'iso_3166_2' => $country['iso_3166_2'] ?? '',
                'iso_3166_3' => $country['iso_3166_3'] ?? '',
                'name' => $country['name'] ?? '',
                'region_code' => $country['region-code'] ?? '',
                'sub_region_code' => $country['sub-region-code'] ?? '',
                'eea' => (bool) ($country['eea'] ?? 0),
                'calling_code' => $country['calling_code'] ?? '',
                'currency_symbol' => $country['currency_symbol'] ?? null,
                'flag' => $country['flag'] ?? null,
            ];
            $existedCountry = Country::where('iso_3166_2', $country['iso_3166_2'])->first();
            if (!$existedCountry) {
                DB::table($countriesTableName)->insert($countryData);
            } else {
                $existedCountry->update($countryData);
            }
        }
    }
}
