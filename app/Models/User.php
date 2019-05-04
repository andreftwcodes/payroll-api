<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Permissions\HasPermissionsTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasPermissionsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->password = bcrypt($user->password);
        });
    }

    public function getJWTIdentifier()
    {   
        return $this->id;
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function createUser($request)
    {
        return $this->create(

            $request->only('email', 'name', 'password')

        )->assignRole(

            $request->roles
            
        );
    }

    public function updateUser($request)
    {
        $this->update(
            $request->only('name')
        );
        
        $this->updateRoles($request->roles);

        return $this;

    }
}
