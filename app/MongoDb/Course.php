<?php

namespace App\MongoDb;

use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Course extends \Moloquent
{
	use SoftDeletes;
	protected $fillable = ['name', 'institution_id'];

	public function institution()
	{
		return $this->belongsTo('App\Mongodb\User', 'institution_id');
	}

	public function periods()
	{
		return $this->hasMany('App\Mongodb\Period');
	}

	public function getInstitution()
	{
		return User::find($this->idInstitution);
	}

}
