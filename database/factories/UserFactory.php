<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */


use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {

    return [


        'user_name' => $faker->name.'_user',
        'name' => $faker->name ,
        'password' => bcrypt('admin'),
        'phone' => $faker->e164PhoneNumber,
        'email' => $faker->email,
        'role' => 2,
        'user_id' => 1
    ];
});




$factory->define(App\Models\Category::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'user_id' => 1
    ];
});

$factory->define(App\Models\ProductCategory::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'vendor_id'=>rand(1 , 6),
    ];
});

$factory->define(App\Models\Product::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description'=> $faker->text,
        'price' => rand(10 , 600),
        'quantity'=>rand(10 , 600),
        'discount_ratio'=>rand(20,70),
        'vendor_id'=>rand(1 , 10),
        'category_id'=>rand(1,9),
        'user_id'=>1
    ];
});

$factory->define(App\Models\Vendor::class, function (Faker $faker) {
    $statusArray = ['pending','accept' , 'blocked'];
    return [
        'first_name'=> $faker->name,
        'last_name'=> $faker->name,
        'email'=> $faker->email ,
        'password'=>bcrypt('admins'),
        'phone' => $faker->e164PhoneNumber,
        'store_name'=> $faker->name,
        'discount_ratio'=>rand(1,90),
        'client_ratio'=>rand(1,6),
        'client_vip_ratio'=>rand(1,9),
        'store_description'=> $faker->name,
        'status' => $faker->randomElement($statusArray),
        'category_id'=>rand(1,6),
        'city_id'=>rand(1,2),
    ];
});


$factory->define(App\Models\WishList::class, function (Faker $faker) {
    return [
        'client_id'=>rand(1,2),
        'product_id' =>rand(1,15)
    ];
});

$factory->define(App\Models\VendorCategory::class, function (Faker $faker) {
    return [
        'vendor_id'=>1,
        'category_id' =>rand(1,9)
    ];
});
$factory->define(App\Models\Cart::class, function (Faker $faker) {
    return [
        'is_done'=>rand(0,1) ,
        'date'=>date('Y-m-d'),
        'total_cost' =>rand(100 , 300),
        'client_id'=>rand(1,2),
        // 'client_address_id'=>1
    ];
});

$factory->define(App\Models\Order::class, function (Faker $faker) {
    $statues = ['pending from client','sending from client','edit from vendor','accept from client' ,'accept from vendor'
    , 'cancelled from vendor' ,'working' , 'delivering' , 'done'] ;
    return [
        'price'=>rand(20 , 300),
        'delivery_cost'=>rand(20 , 300),
        'discount_ratio'=>rand(20 , 90),
        'total_discount'=>rand(20 , 300),
        'status'=>$statues[rand(0,8)],
        'date'=>date('Y-m-d'),
        'cart_id'=>rand(1 , 5),
        'time'=>date("h:i"),

        // 'address'=>$faker->address(),
        // 'phone'=>"01010079798",
        // 'city'=>$faker->city(),
        // 'product_id'=>rand(1,20),
        // 'is_vip'=>rand(0,1),
        'vendor_id'=>rand(1,20),
        'client_id'=>rand(1,2),
        'client_address_id'=>1
    ];
});

$factory->define(App\Models\OrderItem::class, function (Faker $faker) {
    $statues = ['pending from client','sending from client','edit from vendor','accept from client' ,'accept from vendor'
    , 'cancelled from vendor' ,'working' , 'delivering' , 'done'] ;
   return [
        'price'=>rand(20 , 300),
        'choice_price'=>rand(20 , 300),
        'discount'=>rand(20 , 300),
        'discount_ratio'=>rand(20 , 90),
        'vendor_benefit'=>rand(20 , 300),
        'status'=>$statues[rand(0,8)],
        'quantity'=>rand(3,15),
        'is_vip'=>rand(0,1),
        'product_id'=>rand(1,20),
        'vendor_id'=>rand(1,20),
        'order_id'=>rand(1,20),
        'client_id'=>rand(1,2),
    ];
});

$factory->define(App\Models\Question::class, function (Faker $faker) {
    return [
        'question'=>$faker->text ,
        'answer'=>$faker->text,
        'user_id'=>1
    ];
});

$factory->define(App\Models\Complaint::class, function (Faker $faker) {
    return [
        'complaint'=>$faker->text ,
        'phone'=>$faker->e164PhoneNumber,
        'name'=>$faker->name,
        'user_id'=>1
    ];
});
