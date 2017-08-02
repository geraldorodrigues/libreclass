<?php

namespace App\MongoDb;

use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Course extends \Moloquent
{
	use SoftDeletes;
	protected $fillable = ['name', 'institution_id'];

	public function institution()
	{
		return $this->belongsTo('App\MongoDb\User', 'institution_id');
	}

	public function periods()
	{
		return $this->hasMany('App\MongoDb\Period');
	}

	public function getInstitution()
	{
		return User::find($this->idInstitution);
	}

}
