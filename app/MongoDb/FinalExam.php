<?php

namespace App\MongoDb;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class FinalExam extends \Moloquent
{
	use SoftDeletes;
	protected $hidden = ['_id'];
}
