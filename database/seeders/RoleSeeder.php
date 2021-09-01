<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('roles')->updateOrInsert([
            'name'=>'cc'
        ],[
            'name' => 'cc','display_name'=>'Customer care',
        ]);
    }
}
