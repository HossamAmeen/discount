<?php

namespace App\Models;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class Client extends Authenticatable
{
    use HasApiTokens , Notifiable , SoftDeletes;
     protected $fillable = [
        'first_name','last_name', 'gender', 'email', 'password', 'phone','google_id' , 'facebook_id', 'rating' ,'user_id'
     ];
     protected $hidden = [
        'user_id' , "created_at" , 'updated_at' ,'deleted_at'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function scopeAccepted($query)
    {
        return $query->where('approvement', 'accept');
    }
}
