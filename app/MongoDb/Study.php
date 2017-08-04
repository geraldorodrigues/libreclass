<?php

namespace App\MongoDb;

class Study extends \Moloquent
{
	protected $hidden = ['_id'];
	protected $fillable = ['institution_id', 'student_id', 'register'];

	public function study()
	{
		return $this->belongsTo('App\MongoDb\User');
	}

	public function institution()
	{
		return $this->belongsTo('App\MongoDb\User');
	}
}
