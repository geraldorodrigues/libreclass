<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MongoDb\Course;
use App\MongoDb\Period;

use Session;
use Crypt;
use Redirect;

class CourseController extends Controller
{
	public function save(Request $in)
	{
		if (auth()->user()->type != "I") {
			return ['status'=>0, 'message'=>'Operação não permitida'];
		}

		if (isset($in->course_id)) {
			$course = Course::find(Crypt::decrypt($in->course_id));
		} else {
			$course = new Course;
			$course->institution_id = auth()->id();
		}

		$course->name = $in->name;
		$course->type = $in->type;
		$course->modality = $in->modality;
		$course->absent_percent = $in->absent_percent;
		$course->average = $in->average;
		$course->average_final = $in->average_final;
		$course->status = 'E';
		'content' => new MongoBinData(file_get_contents($in->file('file')), MongoBinData::GENERIC),
		$associated->file_id = File::create(['content' => new Binary($decoded_str, Binary::TYPE_GENERIC)])->id;

		$course->save();

		$course->id = Crypt::encrypt($course->id);

		return ['status'=>1, 'course'=>$course];
	}

	public function list()
	{
		if (auth()->user()->type != "I") {
			return ['status'=>0, 'message'=>'Operação não permitida'];
		}

		$courses = auth()->user()->courses()->whereStatus("E")->orderBy("name")->get();
		foreach ($courses as $course) {
			$course->periods = $course->periods;
			$course->unset('created_at');
			$course->unset('updated_at');
			$course->id = Crypt::encrypt($course->id);
		}

		return ['status'=>1, 'courses'=>$courses];
	}

	public function read(Request $in)
	{
		if (!isset($in->course_id)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$course = Course::find(Crypt::decrypt($in->course_id));
		if (!$course){
			return ['status'=>0, 'message'=>'Curso não encontrado'];
		}

		$course->id = Crypt::encrypt($course->id);

		return ['status'=>1, 'course'=>$course];
	}

	public function delete(Request $in)
	{
		if (auth()->user()->type != "I") {
			return ['status'=>0, 'message'=>'Operação não permitida'];
		}

		if (!isset($in->course_id)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$course = Course::find(Crypt::decrypt($in->course_id));
		if (!$course){
			return ['status'=>0, 'message'=>'Curso não encontrado'];
		}

		if ($course->periods()->count()){
			return ['status'=>0, 'message'=>'Operação não realizada. Curso já possui período(s)'];
		}

		$course->delete();

		return ['status'=>1];
	}
}
