<?php

namespace App\MongoDb;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Lecture extends \Moloquent
{
	use SoftDeletes;
	protected $fillable = ['teacher_id', 'offer_id', 'order'];

	public function offer()
	{
		return $this->belongsTo('App\MongoDb\Offer');
	}

	public function teacher()
	{
		return $this->belongsTo('App\MongoDb\User','teacher_id');
	}
}
