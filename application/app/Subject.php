<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'subjects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cn_name','en_name','day','week'
    ];

    protected $hidden = [
        'deleted_at'
    ];

}
