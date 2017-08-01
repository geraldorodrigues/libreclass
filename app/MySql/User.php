<?php

namespace App\MySql;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class User extends Authenticatable
{
	use Notifiable, SoftDeletes;

	protected $table = 'Users';
	protected $connection = 'mysql';
	protected $fillable = ['name', 'type', 'cadastre', 'birthdate', 'enrollment', 'gender'];

	public function disciplines()
	{
		return $this->belongsToMany('Discipline', 'Binds', 'idUser', 'idDiscipline');
	}

	public function offers()
	{
		return $this->belongsToMany('Offer', 'Lectures', 'idUser', 'idOffer');
	}

	public function courses()
	{
		return $this->hasMany('Course', 'idInstitution');
	}

	//Instituição
	public function relationships()
	{
		return $this->hasMany('Relationship', 'institution_id');
	}

	//Professor
	public function teachers()
	{
		return $this->b('User', 'institution_id');
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
