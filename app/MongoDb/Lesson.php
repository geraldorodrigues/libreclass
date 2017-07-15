<?php

namespace App\MongoDb;

class Lesson extends \Moloquent
{
	protected $hidden = ['_id'];

	public function unit()
	{
		return $this->belongsTo("Unit", "unit_id");
	}
}
