<?php

namespace App\MongoDb;

class Discipline extends \Moloquent
{
	protected $fillable = ['name', 'period_id'];
	protected $hidden = ['_id'];

	public function period()
	{
		return $this->belongsTo('App\Period');
	}
}
