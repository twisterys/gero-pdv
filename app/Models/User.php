<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasMagasinPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\QueuedResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, AuthenticationLoggable, HasMagasinPermission, HasRoles, Notifiable;


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new QueuedResetPasswordNotification($token));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'remember_token',
        'role',
        'disabled'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function activeMagasin()
    {
        return $this->hasMany(PosSession::class)->whereNull('date_fin')->first()->magasin_id;
    }

    public function dashboards(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Dashboard::class);
    }


}
