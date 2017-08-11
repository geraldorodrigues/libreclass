<?php

namespace App\MongoDb;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Attend extends \Moloquent
{
	use SoftDeletes;
	protected $fillable = ['student_id', 'unit_id'];

	public function unit()
	{
		return $this->belongsTo('App\MongoDb\Unit', 'unit_id');
	}

	public function student()
	{
		return $this->belongsTo('App\MongoDb\User','student_id');
	}

	public function frequencies()
	{
		return $this->hasMany('App\MongoDb\Frequency');
	}

	public function results()
	{
		return $this->hasMany('App\MongoDb\Result');
	}

}
