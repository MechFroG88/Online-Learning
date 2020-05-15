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
        'cn_name','en_name','user_id','code'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function class_user()
    {
        return $this->hasMany('App\Class_user');
    }

    public function user()
    {
        return $this->belongsTo()->withTrashed();
    }

}
