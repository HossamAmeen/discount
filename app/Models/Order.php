<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
     use SoftDeletes;
    //  protected $fillable = ['price','discount','discount_ratio','is_vip', 'status', 'quantity','vendor_benefit','vendor_id','product_id', 'client_id','cart_id'];
     protected $fillable = ['date','price','delivery_cost','discount_ratio','total_discount','vendor_benefit', 'status','vendor_id', 'client_id','cart_id'];
     protected $hidden = [
        'user_id' , "created_at" , 'updated_at' ,'deleted_at'
    ];
   public function address()
   {
    return $this->belongsTo(ClientAddress::class , 'client_address_id')->select(['id','address','first_name','phone']);
       
   }
    public function product(){
        return $this->belongsTo(Product::class)->select(['id','name','description','image']);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class , 'order_id')->where('status', '==', 'pending from client');
    }
    public function itemsDone()
    {
        return $this->hasMany(OrderItem::class , 'order_id')->where('status', '!=', 'pending from client');
    }
    public function client(){
        return $this->belongsTo(Client::class)->select(['id','first_name','last_name', ]);
    }
}
