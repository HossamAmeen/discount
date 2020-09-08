<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
     use SoftDeletes;
     protected $fillable = ['is_done' ,'date', 'client_id'];
     protected $hidden = [
         "created_at" , 'updated_at' 
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
