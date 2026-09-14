<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\User::create([
            'name'=>'hasan',
            'email'=>'hasan@user.com',
            'password'=>bcrypt(123456)
        ]);

        App\Admin::create([
            'name'=>'jisan',
            'email'=>'jisan@gmail.com',
            'password'=>bcrypt(123456)
        ]);

        App\Customer::create([
            'name'=>'Unknown',
            'email'=>'unknow@mugdh.com',
            'password'=>bcrypt('MdHasan@li'),
            'mobile'=>'132123',
            'gender'=>1,
        ]);

        App\Shipping::create([
            'name'=>'Unknown',
            'mobile'=>'132123',
            'address'=>'Unknown',
        ]);

        App\Customer::create([
            'name'=>'hasan',
            'email'=>'hasan@customer.com',
            'password'=>bcrypt(123456),
            'mobile'=>'1234124',
            'gender'=>1,
        ]);

        $this->call(BasicDatabaseSeeder::class);
    }
}
