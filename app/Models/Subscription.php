<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class Subscription extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cart_id','status','service_time','service_date','resched_count',
    ];

    protected $connection = 'cart_management';
    protected $table = 'subscription';

    // Get CartPackage details
    public function getCartPackageDetails(){
        return $this->hasOne(CartPackage::class, 'cartID','cart_id');
    }

    // Get cart details
    public function getcartdetails(){
        return $this->hasOne(Cart::class, 'id','cart_id')->with('getUserDetails');
    }

    // Get Address details
    public function getAddressDetails(){
        return $this->hasOne(Address::class, 'cartID','cart_id');
    }

    // Get SP details
    public function getSPDetails(){
        return $this->hasOne(SPDetails::class, 'id','accept_provider_id');
    }

    
}
