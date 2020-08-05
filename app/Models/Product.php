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
         "created_at" , 'updated_at' 
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
