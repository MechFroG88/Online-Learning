<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Period extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'periods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'event_id','start_time','end_time'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function choices()
    {
        return $this->hasMany('App\Choice');
    }
}
