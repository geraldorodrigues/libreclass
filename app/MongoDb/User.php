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

	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
	protected $fillable = ['name', 'type', 'status', 'birth_date', 'enrollment', 'gender'];

	//type: A - Adminer;	E - Employee
	/**
	* The attributes that should be hidden for arrays.
	*
	* @var array
	*/
	protected $hidden = [
		'password', 'remember_token','_id'
	];

	public function disciplines()
	{
		return $this->belongsToMany('App\MongoDb\Discipline', 'App\MongoDb\Binds', 'user_id', 'discipline_id');
	}

	public function offers()
	{
		return $this->belongsToMany('App\MongoDb\Offer', 'App\MongoDb\Lectures', 'user_id', 'offer_id');
	}

	public function courses()
	{
		return $this->hasMany('App\MongoDb\Course', 'institution_id');
	}

	public function setNameAttribute($value)
	{
		$this->attributes['name'] = ucwords($value);
	}

	public function printLocation()
	{
		$city = City::find($this->idCity);
		if (!$city) {
			return "";
		}

		$state = State::find($city->idState);
		$country = Country::find($state->idCountry);

		return "$city->name, $state->name, $country->name";
	}

	public function printCityState()
	{
		$city = City::find($this->idCity);
		if (!$city) {
			return "";
		}

		$state = State::find($city->idState);

		return "$city->name - $state->name";
	}
}
