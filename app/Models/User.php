<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name','address','city','payment_mode','email','mobile','password','picture','device_token','device_id','device_type','login_by','social_unique_id','latitude','longitude','stripe_cust_id','wallet_balance','rating','otp','remember_token','status','code','credit','referrer_code','use_referrer_code',
    ];


    protected $table = 'users';
}
