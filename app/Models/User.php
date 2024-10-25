<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'sales_type',
    ];



    public function leads()
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    /**
     * Relasi ke tabel Punishment (hukuman).
     */
    public function punishments()
    {
        return $this->hasMany(Punishment::class);
    }

    /**
     * Relasi ke tabel StatusHistory (untuk melacak perubahan status yang dilakukan oleh user).
     */
    public function surveys()
    {
        return $this->hasMany(Survey::class, 'approved_by');
    }

    public function rejected_by()
    {
        return $this->hasMany(Survey::class, 'rejected_by');
    }


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
    ];
}
