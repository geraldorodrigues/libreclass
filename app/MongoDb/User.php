<?php

namespace App\MongoDb;

use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;


class User extends \Moloquent implements AuthenticatableContract,
AuthorizableContract,
CanResetPasswordContract
{
	use Notifiable;
	use SoftDeletes;
	use Authenticatable, Authorizable, CanResetPassword;

	protected $fillable = ['name', 'type', 'status', 'birth_date', 'enrollment', 'gender'];

	//type: A - Adminer;	E - Employee

	protected $hidden = ['password', 'remember_token','_id'];

	/*----------Institution----------*/

	public function courses()
	{
		return $this->hasMany('App\MongoDb\Course', 'institution_id');
	}

	//Relacionamentos de instituições com professores e alunos. Diferenciação pelo uso de teacher_id ou student_id
	public function relationships()
	{
		return $this->hasMany('App\MongoDb\Relationship', 'institution_id');
	}

	/*----------Teacher----------*/

	public function works()
	{
		return $this->hasMany('App\MongoDb\Relationship', 'teacher_id');
	}

	public function lectures()
	{
		return $this->hasMany('App\MongoDb\Lecture', 'teacher_id');
	}

	/*----------Student----------*/
	public function studies()
	{
		return $this->hasMany('App\MongoDb\Relationship', 'student_id');
	}

	public function attends()
	{
		return $this->hasMany('App\MongoDb\Attend', 'student_id');
	}

	//Set/Get Methods

	public function setNameAttribute($value)
	{
		$this->attributes['name'] = ucwords($value);
	}
}

// public function disciplines()
// {
// 	return $this->belongsToMany('App\MongoDb\Discipline', 'App\MongoDb\Binds', 'user_id', 'discipline_id');
// }
//
// public function offers()
// {
// 	return $this->belongsToMany('App\MongoDb\Offer', 'App\MongoDb\Lectures', 'user_id', 'offer_id');
// }
