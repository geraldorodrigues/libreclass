<?php

namespace App\MongoDb;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Exam extends \Moloquent
{
	use SoftDeletes;
	protected $hidden = ['_id'];

	public function unit()
	{
		return $this->belongsTo('App\MongoDb\Unit');
	}

	public function results()
	{
		return $this->hasMany('App\MongoDb\Result');
	}
	// public function descriptive_exams()
	// {
	// 	$descriptive_exams = $this->hasMany("DescriptiveExam", "exam_id")->get();
	// 	foreach ($descriptive_exams as $key => $descriptive_exam) {
	// 		$descriptive_exams[$key]['student'] = $descriptive_exam->student();
	// 	}
	// 	return $descriptive_exams;
	// }

}
