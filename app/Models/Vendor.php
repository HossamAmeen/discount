<?php

namespace App\Models;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class Vendor extends Authenticatable
{
    use HasApiTokens , Notifiable , SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'gender', 'email' ,'password' ,'phone',
        'store_name', 'store_description', 'store_logo', 'store_background_image',
        'company_registration_image', 'national_id_front_image', 'national_id_back_image', 'expiration_date',
        'category_id', 'city_id', 'user_id'
    ];
    protected $hidden = [
            'user_id' , "created_at" , 'updated_at' ,'deleted_at'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
