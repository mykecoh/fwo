<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class RewardLog extends Authenticatable
{
    use Notifiable;

    protected $rememberTokenName = false;

    protected $table = 'reward_log';
    public $timestamps = false;

    public function rewardcapped()
    {
        return $this->belongTo('App\RewardCapped');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reward_id', 'charid'
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
