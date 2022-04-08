<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currency_table =  new Currency();
        $currency_table->name = 'US Dollar';
        $currency_table->short_name = 'USD';
        $currency_table->symbol = '&#36;';
        $currency_table->created_by = 1;
        $currency_table->created_at = date_create();
        $currency_table->save();
        $currency_table =  new Currency();
        $currency_table->name = 'Euro';
        $currency_table->short_name = 'EUR';
        $currency_table->symbol = '&#8364;';
        $currency_table->created_by = 1;
        $currency_table->created_at = date_create();
        $currency_table->save();
    }
}
