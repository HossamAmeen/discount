<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingCard extends Model
{
     use SoftDeletes;
     protected $fillable = ['number','is_used', 'user_table' ,'date', 'benefactor_id','user_id' ];
     protected $hidden = [
         'user_id',"created_at" , 'updated_at','deleted_at' 
    ];
    public function benefactor($table)
    {
       if($table == "vendors")
       {
           return $this->vendor->store_name ?? null ; 
       }
       elseif($table == 'clients')
       {
        return $this->client->full_name   ?? "not found client"; 
       }
      
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class ,  'benefactor_id');
    }
    public function client()
    {
        return $this->belongsTo(Client::class ,  'benefactor_id');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
