<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'empresa_id',
        'perfil_id',
        'location',
        'phone',
        'about',
        'password_confirmation',
        'last_session',
        'status',
        
    ];


    public function perfil()
    {
        return $this->belongsTo(PerfilModel::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresas::class,'empresa_id ','id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /*public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }*/

    // Evento para llenar el campo last_session automáticamente con un timestamp
        protected static function boot()
        {
            parent::boot();

            static::creating(function ($user) {
                $user->last_session = now(); // now() genera un timestamp
            });
        }

}
