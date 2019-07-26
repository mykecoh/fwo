<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class Item extends Model
{
    //use Notifiable;

    protected $rememberTokenName = false;


    protected $table = 'item';
    protected $connection = 'mysql3';
    public $timestamps = false;
    protected $primaryKey = "ItemID";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Weight', 'LevelGroup', 'BuyPrice','PopLimit'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = [
    //     'password', 'remember_token',
    // ];
}
