<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_date','end_date','start_pick_date','end_pick_date'
    ];

    protected $dates = [
        'start_date','end_date','start_pick_date','end_pick_date'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function choices()
    {
        return $this->hasMany('App\Choice');
    }
}
