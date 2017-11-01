<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable
{
    use Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Fname', 'Lname', 'Password',
    ];

    public $timestamps=false;
    protected $hidden = [
        'password', 'remember_token',
    ];



}
