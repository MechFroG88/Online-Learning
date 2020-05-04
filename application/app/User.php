<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;
    protected $table = 'users';
    //protected $with = ['class_user'];

    public function getAuthPassword() {
        return $this->password_real;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username','password','cn_name','type','en_name','password_real','email'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password','deleted_at','created_at','updated_at','remember_token','password_real'
    ];

    public function class_user()
    {
        return $this->hasMany('App\Class_user');
    }

}
