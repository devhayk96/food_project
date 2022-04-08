<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

class AddressSeeder extends AbstractWithUserSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = $this->getSystemUser();
        DB::table('poshub_addresses')->insert([
            'street' => 'Wilhelminakade 909 3072 AP',
            'postcode' =>'8888MX',
            'city' => 'Rotterdam',
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id,
        ]);
    }
}
