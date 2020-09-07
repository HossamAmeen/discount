<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
     use SoftDeletes;
     protected $fillable = ['price', 'status', 'time', 'date', 'quantity' ,'address','phone','city','product_id','client_address_id', 'client_id'];
     protected $hidden = [
        'user_id' , "created_at" , 'updated_at' ,'deleted_at'
    ];
   public function address()
   {
    return $this->belongsTo(ClientAddress::class)->select(['id','name','price','image']);
       
   }
    public function product(){
        return $this->belongsTo(Product::class)->select(['id','name','price','image']);
    }
    public function choices()
    {
        return $this->hasMany(OrderChoice::class , 'order_id');
    }
    public function client(){
        return $this->belongsTo(Client::class)->select(['id','first_name','last_name', ]);
    }
}
