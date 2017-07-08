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
	protected $fillable = [
		'name', 'email', 'password', 'type', 'delegacia_id'
	];
	//type: A - Adminer;	E - Employee
	/**
	* The attributes that should be hidden for arrays.
	*
	* @var array
	*/
	protected $hidden = [
		'password', 'remember_token','_id'
	];

	//Relacionamentos do Admin
	public function syndicate()
	{
		return $this->belongsTo('App\Syndicate');
	}

	//Imagem de perfil
	public function file()
	{
		return $this->belongsTo('App\File');
	}

	public function documents()
	{
		return $this->hasMany('App\Document');
	}

	public function setNameAttribute($value)
	{
		$this->attributes['name'] = titleCase(trimpp($value));
	}

	public function setEmailAttribute($value)
	{
		$this->attributes['email'] = strtolower($value);
	}

	public function setCpfAttribute($value)
	{
		$this->attributes['cpf'] = str_replace(['.','-'], '', $value);
	}

	public function getCpfAttribute($value)
	{
		return format('%s%s%s.%s%s%s.%s%s%s-%s%s', $value);
	}
}
