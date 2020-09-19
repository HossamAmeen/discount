<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
     use SoftDeletes;
     protected $fillable = ['price','choice_price','discount','discount_ratio','is_vip','status', 'quantity','over_quantity','vendor_benefit','rating','product_id','order_id'];
     protected $hidden = [
         'user_id',"created_at" , 'updated_at','deleted_at' 
    ];
    public function product(){
        return $this->belongsTo(Product::class)->select(['id','name','description','image']);
    }
    public function choices()
    {
        return $this->hasMany(OrderChoice::class , 'order_item_id');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }

}
