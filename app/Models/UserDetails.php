<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class UserDetails extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name','last_name','email','email_verified','mobile','mobile_verified','gender','dob','anniversary','picture','referral_code','device_id','device_type','device_token','secret_hash','salt','auth_token','notification_enabled','last_login_at','active','enabled','created_at','updated_at','country_code',
    ];

    protected $connection = 'clykk_um';
    protected $table = 'user_detail'; 

    
}