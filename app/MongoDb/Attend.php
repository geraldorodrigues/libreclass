<?php

namespace App\MongoDb;

class Attend extends \Moloquent
{

	protected $fillable = ['student_id', 'unit_id'];

	public function unit()
	{
		return $this->belongsTo('Unit', 'unit_id');
	}

	public function student()
	{
		return $this->belongsTo('User','student_id');
	}

	public function student()
	{
		return $this->hasMany('Frequency');
	}

	/*public function getUser()
	{
		return User::find($this->user_id);
	}

	public function getExamsValue($exam)
	{
		$examValue = ExamsValue::where("exam_id", $exam)->where("attend_id", $this->id)->first();
		if ($examValue) {
			return $examValue->value;
		} else {
			return null;
		}
	}

	public function getDescriptiveExam($exam)
	{
		$examDescriptive = DescriptiveExam::where("exam_id", $exam)->where("attend_id", $this->id)->first();
		if ($examDescriptive) {
			return ["description" => $examDescriptive->description, "approved" => $examDescriptive->approved];
		} else {
			return null;
		}
	}

	public function getUnit()
	{
		return Unit::find($this->idUnit);
	}*/
}
