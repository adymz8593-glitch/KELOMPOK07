<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username', // Wajib ada agar tidak error NOT NULL
        'email',    // Tambahkan jika di database ada kolom email
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}