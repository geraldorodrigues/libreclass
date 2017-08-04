<?php

namespace App\MongoDb;

class Study extends \Moloquent
{
	protected $hidden = ['_id'];
	protected $fillable = ['institution_id', 'student_id', 'register'];

	public function student()
	{
		return $this->belongsTo('App\MongoDb\User','student_id');
	}

	public function institution()
	{
		return $this->belongsTo('App\MongoDb\User','institution_id');
	}
}
