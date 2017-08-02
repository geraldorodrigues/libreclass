<?php

namespace App\MongoDb;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Lesson extends \Moloquent
{
	use SoftDeletes;
	protected $hidden = ['_id'];

	public function unit()
	{
		return $this->belongsTo('App\MongoDb\Unit');
	}
}
