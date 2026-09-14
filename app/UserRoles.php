<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserRoles extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that should be say the guard name.
     *
     * @var array
     */
    protected $guard = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id', 'name', 'status', 'permission', 'actionPermission',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function company() {
        return $this->hasOne(CompanySetup::class, 'id', 'company_id');
    }

}
