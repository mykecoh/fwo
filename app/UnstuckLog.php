<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class UnstuckLog extends Model
{
    use Notifiable;

    protected $rememberTokenName = false;

    protected $connection = 'mysql';
    protected $table = 'unstuck_log';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'charid', 'to_x', 'to_y', 'to_z', 'to_scene', 'date',
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
