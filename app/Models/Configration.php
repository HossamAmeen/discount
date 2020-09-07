<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Configration extends Model
{
     use SoftDeletes;
     protected $fillable = ['website_name' , 'email' , 'address' , 'phone' , 'user_id'];
     protected $hidden = [
         "created_at" , 'updated_at' 
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
