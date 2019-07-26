<?php

namespace App;

//use Illuminate\Notifications\Notifiable;
//use Illuminate\Foundation\Auth\User as Authenticatable;
use Eloquent;


class DevWorld extends Eloquent
{
    //use Notifiable;

    protected $rememberTokenName = false;

    protected $connection = 'mysql3';
    protected $table = 'pcharacter';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'username', 'email', 'password','name','group','date_registered','nonce','banned','status'
    // ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = [
    //     'password', 'remember_token',
    // ];
}
