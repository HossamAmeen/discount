<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
     use SoftDeletes;
     protected $fillable = ['name', 'description', 'price', 
     'quantity','image', 'vendor_id', 'category_id'];
     protected $hidden = [
        'user_id' , "created_at" , 'updated_at' ,'deleted_at'
    ];
    public function getImageAttribute()
    {
        
        if($this->attributes['image'] != null && file_exists(($this->attributes['image'])) ){
            return asset($this->attributes['image']);
        }
        else
        {
            return asset('assets/img/avatars/avatar-1.jpg');
        }
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function choices()
    {
        return $this->hasMany(ProductChoice::class , 'product_id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
