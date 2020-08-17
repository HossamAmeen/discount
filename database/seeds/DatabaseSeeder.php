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
          //  'gender'     => "male",
            'email'      => "hosamameen948@gmail.com",
            'password'   => bcrypt('admin'),
            'phone'      => "01010079798",
            'store_name' => "mac"
        ]);
        \App\Models\Vendor::create([
            'first_name' => "hossam",
            'last_name'  => "ameen",
          //  'gender'     => "male",
            'email'      => "hosamameen948s@gmail.com",
            'password'   => bcrypt('admins'),
            'phone'      => "01010079798",
            'store_name' => "mac"
        ]);
        \App\Models\Client::create([
            'first_name' => "hossam client",
            'last_name'  => "ameen",
            'gender'     => "male",
            'email'      => "hosamameen948@gmail.com",
            'password'   => bcrypt('admin'),
            'phone'      => "01010079798",
        ]);
        
        
      
        factory('App\Models\Category',9)->create();
        factory('App\Models\ProductCategory',9)->create();
        factory('App\Models\Product',25)->create();
        factory('App\Models\Order',150)->create();
        $this->productChoices();
        $this->productChoices();
    }
    public function productChoices()
    {
        \App\Models\ProductChoice::create([
            'name' => "larage",
            'price'=>rand(30 , 60),
            'type' => rand(1,2),
            'group_name'=>"size",
            'product_id'=>1
        ]);
        \App\Models\ProductChoice::create([
            'name' => "meduim",
            'price'=>rand(30 , 60),
            'type' => rand(1,2),
            'group_name'=>"size",
            'product_id'=>1
        ]);
        \App\Models\ProductChoice::create([
            'name' => "small",
            'price'=>rand(30 , 60),
            'type' => rand(1,2),
            'group_name'=>"size",
            'product_id'=>1
        ]);
        \App\Models\ProductChoice::create([
            'name' => "in",
            'price'=>rand(30 , 60),
            'type' => rand(1,2),
            'group_name'=>"place",
            'product_id'=>1
        ]);
        \App\Models\ProductChoice::create([
            'name' => "out",
            'price'=>rand(30 , 60),
            'type' => rand(1,2),
            'group_name'=>"place",
            'product_id'=>1
        ]);
    }
}
