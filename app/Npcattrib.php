<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class Npcattrib extends Model
{
    use Notifiable;

    protected $rememberTokenName = false;

    protected $table = 'npcattrib';
    protected $connection = 'mysql3';
    public $timestamps = false;
    protected $primaryKey = "AttribID";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'AttribID', 'ModelID', 'AttachmentID','AttRating'
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
