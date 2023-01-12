<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class SPServiceSettings extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sp_id','category_id','subcategory_id','s2_c','c2_s','s2_vc','s2_c2_c','enabled',
    ];

    protected $connection = 'sp_management';
    protected $table = 'sp_service_settings';

    // Get routing details
    public function getSPdetails(){
        return $this->hasMany(SPDetails::class, 'id','sp_id');
    }
}
