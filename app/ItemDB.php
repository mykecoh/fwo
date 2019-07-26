<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class ItemDB extends Model
{
    //use Notifiable;

    protected $rememberTokenName = false;


    protected $table = 'item';
    protected $connection = 'mysql';

    public function item_category()
    {
        return $this->belongsTo('App\ItemCategory');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'Weight', 'LevelGroup', 'BuyPrice','PopLimit'
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
