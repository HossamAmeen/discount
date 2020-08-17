<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->double('price');
            $table->enum('status' , ['pending from client','edit from vendor','accept from client' ,'accept from vendor', 
                                     'cancelled from vendor' ,'working' , 'delivering' , 'done'])->default('pending from client')->nullable();
            $table->time('time');
            $table->date('date');
            $table->integer('quantity');
            $table->string('address');
            $table->string('phone');
            $table->string('city');
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');

            $table->bigInteger('client_id')->unsigned()->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
