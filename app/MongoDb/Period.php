<?php

namespace App\MongoDb;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Period extends \Moloquent
{
	use SoftDeletes;
	protected $fillable = ['name', 'course_id'];

	public function course()
	{
		return $this->belongsTo('App\MongoDb\Course');
	}

	public function disciplines()
	{
		return $this->hasMany('App\MongoDb\Discipline');
	}

	public function classes()
	{
		return $this->hasMany('App\MongoDb\Classe');
	}
}
