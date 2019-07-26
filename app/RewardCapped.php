<?php

namespace App;

use Eloquent;

class RewardCapped extends Eloquent
{
    protected $table = "reward_capped";

    public function rewardlog()
    {
        return $this->hasMany('App\RewardLog');
    }

}