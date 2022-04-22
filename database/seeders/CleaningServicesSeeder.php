<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CleaningServicesSeeder extends Seeder
{
     /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('cleaningservices')->truncate();
        DB::table('cleaningservices')->insert([
            [
                'name'=>'Cleaning',
                'cleaning_group'=>1,
                'perc'=>30,
                'fixed_price'=>0,
                'selected_default'=>1,
                'created_at'=>date('Y-m-d H:i:s')
            ],
            [
                'name'=>'Spotting',
                'cleaning_group'=>1,
                'perc'=>15,
                'fixed_price'=>0,
                'selected_default'=>1,
                'created_at'=>date('Y-m-d H:i:s')
            ],
            [
                'name'=>'Pressing',
                'cleaning_group'=>1,
                'perc'=>15,
                'fixed_price'=>0,
                'selected_default'=>1,
                'created_at'=>date('Y-m-d H:i:s')
            ],
            [
                'name'=>'Ozonation',
                'cleaning_group'=>1,
                'perc'=>30,
                'fixed_price'=>0,
                'selected_default'=>0,
                'created_at'=>date('Y-m-d H:i:s')
            ],
            [
                'name'=>'Debobble',
                'cleaning_group'=>2,
                'perc'=>0,
                'fixed_price'=>2,
                'selected_default'=>0,
                'created_at'=>date('Y-m-d H:i:s')
            ],
            [
                'name'=>'Starch',
                'cleaning_group'=>2,
                'perc'=>0,
                'fixed_price'=>3,
                'selected_default'=>0,
                'created_at'=>date('Y-m-d H:i:s')
            ],
            [
                'name'=>'Folding',
                'cleaning_group'=>2,
                'perc'=>0,
                'fixed_price'=>2,
                'selected_default'=>0,
                'created_at'=>date('Y-m-d H:i:s')
            ],
            [
                'name'=>'With Crease',
                'cleaning_group'=>2,
                'perc'=>0,
                'fixed_price'=>2,
                'selected_default'=>0,
                'created_at'=>date('Y-m-d H:i:s')
            ],

        ]);

    }
}
