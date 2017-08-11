<?php

namespace App\MongoDb;

class Relationship extends \Moloquent
{
	protected $hidden = ['_id'];
	protected $fillable = ['institution_id', 'teacher_id', 'register'];

	public function institution()
	{
		return $this->belongsTo('App\MongoDb\User', 'institution_id');
	}

	public function teacher()
	{
		return $this->belongsTo('App\MongoDb\User', 'teacher_id');
	}

	public function student()
	{
		return $this->belongsTo('App\MongoDb\User', 'student_id');
	}
}
