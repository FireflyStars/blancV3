<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user=DB::table('users')->where('email','=','op@blanc.local.com')->first();
        if($user==null) {
             DB::table('users')->insertGetId([
                'name' => 'Production OP',
                'email' => 'op@blanc.local',
                'password' => Hash::make('admin123'),
                'firstname'=>'',
                'lastname'=>'',
                 'phone'=>'',
                'group_access'=>'',
                 'driver_id'=>0,
                'role_id'=>2
            ]);
        }
        $usercc=DB::table('users')->where('email','=','cc@blanc.local.com')->first();
        if($usercc==null) {
            $role=DB::table('roles')->where('name','=','cc')->first();
            if($role!=null)
            DB::table('users')->insertGetId([
                'name' => 'Customer Care',
                'email' => 'cc@blanc.local',
                'password' => Hash::make('admin123'),
                'firstname'=>'',
                'lastname'=>'',
                'phone'=>'',
                'group_access'=>'',
                'driver_id'=>0,
                'role_id'=>$role->id
            ]);
        }

    }
}
