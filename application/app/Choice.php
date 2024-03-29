<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{

    public $timestamps = false;
    protected $table = 'choices';
    protected $with = ['class_user'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'class_user_id','event_id','period_id','date','method','link','streamId','streamPassword','description'
    ];

    public function event()
    {
        return $this->belongsTo('App\Event')->withTrashed();
    }

    public function period()
    {
        return $this->belongsTo('App\Period')->withTrashed();
    }

    public function class_user()
    {
        return $this->belongsTo('App\Class_user')->withTrashed();
    }
}
