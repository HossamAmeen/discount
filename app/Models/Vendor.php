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
        'first_name', 'last_name', 'email' ,'password' ,'phone',
        'store_name',
        'discount_ratio', 
        // 'client_ratio', 'client_vip_ratio',
        'store_description', 'store_logo', 'store_background_image',
        'company_registration_image', 'national_id_front_image', 'national_id_back_image',
         'expiration_date','delivery','status','category_id', 'city_id', 'user_id'
    ];
    protected $hidden = [
           'password', 'user_id' , "created_at" , 'updated_at' ,'deleted_at'
    ];
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accept');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function categories()
    {
        return $this->hasMany(ProductCategory::class);
    }
    // public function getStoreLogoAttribute()
    // {
    //     if($this->attributes['store_logo'] == "avatar.png")
    //     return asset($this->attributes['store_logo']);
    //     else
    //     return $this->attributes['store_logo'];
    // }
    public function getStoreLogoAttribute()
    {
        
        if($this->attributes['store_logo'] != null && file_exists(($this->attributes['store_logo'])) ){
            return asset($this->attributes['store_logo']);
        }
        else
        {
            return asset('assets/img/avatars/avatar-1.jpg');
        }
    }
    public function getStoreBackgroundImageAttribute()
    {
        
        if($this->attributes['store_background_image'] != null && file_exists(($this->attributes['store_background_image'])) ){
            return asset($this->attributes['store_background_image']);
        }
        else
        {
            return asset('assets/img/avatars/avatar-1.jpg');
        }
    }
    public function getCompanyRegistrationImageAttribute()
    {
        
        if($this->attributes['company_registration_image'] != null && file_exists(($this->attributes['company_registration_image'])) ){
            return asset($this->attributes['company_registration_image']);
        }
        else
        {
            return asset('assets/img/avatars/avatar-1.jpg');
        }
    }
    public function getNationalIdFrontImageAttribute()
    {
        
        if($this->attributes['national_id_front_image'] != null && file_exists(($this->attributes['national_id_front_image'])) ){
            return asset($this->attributes['national_id_front_image']);
        }
        else
        {
            return asset('assets/img/avatars/avatar-1.jpg');
        }
    }
    public function getNationalIdBackImageAttribute()
    {
        
        if($this->attributes['national_id_back_image'] != null && file_exists(($this->attributes['national_id_back_image'])) ){
            return asset($this->attributes['national_id_back_image']);
        }
        else
        {
            return asset('assets/img/avatars/avatar-1.jpg');
        }
    }
    
}
