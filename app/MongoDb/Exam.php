<?php

namespace App\MongoDb;

class Exam extends \Moloquent
{
	protected $hidden = ['_id'];
	
	public function unit()
	{
		return $this->belongsTo("Unit", "unit_id");
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
