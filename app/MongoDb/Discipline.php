<?php

namespace App\MongoDb;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Discipline extends \Moloquent
{
	use SoftDeletes;
	
	protected $fillable = ['name', 'period_id'];
	protected $hidden = ['_id'];

	public function period()
	{
		return $this->belongsTo('App\Period');
	}

	public function offers()
	{
		return $this->hasMany('Offer');
	}
}
