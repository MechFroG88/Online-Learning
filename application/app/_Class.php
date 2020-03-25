<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class _Class extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'classes';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cn_name','en_name'
    ];

    protected $hidden = [
        'deleted_at'
    ];

}
