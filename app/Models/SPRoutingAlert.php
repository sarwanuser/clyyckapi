<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;


use Tymon\JWTAuth\Contracts\JWTSubject;

class SPRoutingAlert extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'assigned_provider_id','user_id','subscription_id','commission_from','routing_before','status','cart_id','no_of_route','sub_category_id','commission_to','type','accept_provider_id'
    ];

    protected $connection = 'routing';
    protected $table = 'sp_routing_alerts';

    // Get routing details
    public function getroutngdetails(){
        return $this->hasMany(SPRoutingAlertDetail::class, 'sp_routing_id','id');
    }

    // Get subscription details
    public function getsubsdetails(){
        return $this->hasOne(Subscription::class, 'id','subscription_id');
    }

    // Get cart details
    public function getcartdetails(){
        return $this->hasOne(Cart::class, 'id','cart_id');
    }

    // Get CartPackage details
    public function getCartPackageDetails(){
        return $this->hasOne(CartPackage::class, 'cartID','cart_id');
    }
    
    // Get SP details
    public function getSPDetails(){
        return $this->hasOne(SPDetails::class, 'id','accept_provider_id');
    }
    
    // Get SP details
    public function getSPAssignDetails(){
        return $this->hasOne(SPDetails::class, 'id','assigned_provider_id');
    }

    // Get Address details
    public function getAddressDetails(){
        return $this->hasOne(Address::class, 'cartID','cart_id');
    }

    // Get User details
    public function getUserDetails(){
        return $this->hasOne(UserDetails::class, 'id','user_id');
    }
    

    
}
