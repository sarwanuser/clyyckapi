<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject; 

class SPPayment extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subscription_id','sp_id','amount','commission','payment_status','created_at','updated_at','create_by',
    ];

    protected $connection = 'sp_management';
    protected $table = 'sp_payment'; 


    // Get patment transaction
    public function getPaymentTransaction(){
        return $this->hasMany(SPPaymentTransaction::class, 'subscription_id','subscription_id');
    }
    
    // Get subscription details
    public function getSubscriptionDetails(){
        return $this->hasMany(Subscription::class, 'id','subscription_id')->with('getCartPackageDetails')->with('getAddressDetails');
    }
    
    
    
}