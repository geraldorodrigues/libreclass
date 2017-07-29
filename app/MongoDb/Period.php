<?php

namespace App\MongoDb;

class Period extends \Moloquent
{
	protected $fillable = ['name', 'course_id'];

	public function course()
	{
		return $this->belongsTo('Course');
	}

	public function disciplines()
	{
		return $this->hasMany('Discipline');
	}

}
