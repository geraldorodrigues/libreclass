<?php

namespace App\MongoDb;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Result extends \Moloquent
{
	use SoftDeletes;
	protected $hidden = ['_id'];

	public function unit()
	{
		return $this->belongsTo('App\MongoDb\Exam');
	}

	public function attend()
	{
		return $this->belongsTo('App\MongoDb\Attend');
	}
}
