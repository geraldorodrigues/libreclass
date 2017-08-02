<?php

namespace App\MongoDb;

class Bind extends \Moloquent
{
	protected $hidden = ['_id'];

	public function discipline()
	{
		return $this->hasOne('App\MongoDb\Discipline');
	}
}
