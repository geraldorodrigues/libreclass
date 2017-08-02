<?php

namespace App\MongoDb;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Classe extends \Moloquent
{
	use SoftDeletes;

	protected $fillable = ['name', 'period_id', 'class'];
	protected $hidden = ['_id'];

	public function period()
	{
		return $this->belongsTo('App\MongoDb\Period');
	}

	public function offers()
	{
		return $this->hasMany('App\MongoDb\Offer');
	}
}
