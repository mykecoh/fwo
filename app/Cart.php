<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class Cart extends Model
{
    use Notifiable;

    protected $rememberTokenName = false;

    protected $connection = 'mysql';
    protected $table = 'cart';

    public function item()
    {
        return $this->belongsTo('App\ItemDB');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'users_id', 'item_id', 'credit'
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
