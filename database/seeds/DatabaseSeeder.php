<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        $this->call([
            UserSeed::class,
        ]);
        factory('App\Models\User',25)->create();
        \App\Models\Category::create([
            'name'=> 'deals',
            
        ]);
        \App\Models\City::create([
            'name'=> 'assuit',
           
        ]);
        \App\Models\Vendor::create([
            'first_name' => "hossam",
            'last_name'  => "ameen",
            'gender'     => "male",
            'email'      => "hosamameen948@gmail.com",
            'password'   => bcrypt('admin'),
            'phone'      => "01010079798",
            'store_name' => "mac"
        ]);
        factory('App\Models\Category',9)->create();
        factory('App\Models\Product',25)->create();
    }
}
