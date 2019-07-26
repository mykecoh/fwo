<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class ItemPurchaseLog extends Model
{
    use Notifiable;

    protected $rememberTokenName = false;

    protected $connection = 'mysql';
    protected $table = 'item_purchase_log';
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'users_id', 'item_id','date','total','discount','credit'
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
