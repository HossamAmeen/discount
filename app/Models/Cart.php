<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
     use SoftDeletes;
     protected $fillable = ['is_done' ,'date','total_cost', 'client_id','client_address_id'];
     protected $hidden = [
         "deleted_at","created_at" , 'updated_at' 
    ];
    public function orders()
    {
        return $this->hasMany(Order::class)->select(['id' ,'cart_id' ,'product_id' , 'price' , 'discount']);
    }
    public function address(){
        return $this->belongsTo(ClientAddress::class ,'client_address_id');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
