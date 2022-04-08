<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vat;

class VatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Vat::create([
            'name' => 'test',
            'description' => 'testing vate',
            'created_by' => 1
        ]);
    }
}
