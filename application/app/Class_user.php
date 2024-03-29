<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Class_user extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'class_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','class_id','subject_id'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    protected $with = ['user','subject','class'];

    public function choices()
    {
        return $this->hasMany('App\Choice');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subject')->withTrashed();
    }

    public function class()
    {
        return $this->belongsTo('App\_Class')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\User')->withTrashed();
    }
}
