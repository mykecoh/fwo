<?php

namespace App;

use Eloquent;

class ItemCategory extends Eloquent
{
    protected $table = "item_category";

    public function item()
    {
        return $this->hasMany('App\ItemDB');
    }

}