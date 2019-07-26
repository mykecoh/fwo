<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class CreditTransferLog extends Model
{
    use Notifiable;

    protected $rememberTokenName = false;

    protected $connection = 'mysql';
    protected $table = 'credit_transfer_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from', 'transfer_to', 'amount', 'date'
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
