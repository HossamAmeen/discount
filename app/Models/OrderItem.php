<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
     use SoftDeletes;
     protected $fillable = ['price','total_cost','choice_price','discount','discount_ratio','is_vip','status',
                            'refuse_reason','quantity','over_quantity','vendor_benefit',
                            'rating','product_id','vendor_id','client_id','order_id'];
     protected $hidden = [
         'user_id',"created_at" , 'updated_at','deleted_at'
    ];
    public function getTotalCostAttribute()
    {
        return $this->attributes['price'] * $this->attributes['quantity'] ;
    }
    public function product(){
        return $this->belongsTo(Product::class, 'product_id')->select(['id','name','description','discount_ratio','image']);
    }

    public function choices()
    {
        return $this->hasMany(OrderChoice::class , 'order_item_id');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class , 'order_id');
    }
    public function client()
    {
        return $this->belongsTo(Client::class , 'client_id');
    }
}
