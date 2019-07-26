<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class Treasure extends Model
{
    use Notifiable;

    protected $rememberTokenName = false;

    protected $table = 'treasure';
    protected $connection = 'mysql3';
    public $timestamps = false;
    protected $primaryKey = "Indx";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'IndexID', 'Quantity1', 'GoldMin','Rarity','GoldMax','Durability1','TableID','Indx','Item1'
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
