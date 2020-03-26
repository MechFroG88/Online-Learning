<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{

    public $timestamps = false;
    protected $table = 'dayss';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'count'
    ];

}
