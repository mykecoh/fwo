<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class Effect extends Model
{
    use Notifiable;

    protected $rememberTokenName = false;

    protected $table = 'effects';
    protected $connection = 'mysql3';
    public $timestamps = false;
    protected $primaryKey = "EffectID";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'EffectID', 'Stun', 'Slow'
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
