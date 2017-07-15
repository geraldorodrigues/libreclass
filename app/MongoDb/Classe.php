<?php

namespace App\MongoDb;

class Classe extends \Moloquent
{
	protected $fillable = ['name', 'period_id', 'class'];
	protected $hidden = ['_id'];

	public function period()
	{
		return $this->belongsTo('App\Period');
	}
}
