<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class UniqueItem extends Model
{
    use Notifiable;

    protected $rememberTokenName = false;

    protected $table = 'uniqueitem';
    protected $connection = 'mysql3';
    public $timestamps = false;
    protected $primaryKey = "ItemID";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'CharID', 'DecayCounter','ItemID'
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
