<?php

namespace App\Http\Controllers;

use App\MySql\Course;
use App\MySql\Period;

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

		$course->save();
		$course->id = Crypt::encrypt($course->id);

		return ['status'=>1, 'course'=>$course];
	}

	// public function getIndex()
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
		// $listcourses = [];
		// foreach ($courses as $course) {
		// 	$listcourses[Crypt::encrypt($course->id)] = $course->name;
		// 	$course->periods = Period::where("idCourse", $course->id)->get();
		// }
		// return view("social.courses", ["courses" => $courses, "user" => $this->user, "listcourses" => $listcourses]);
	}

	/*Mesclado com o método anterior (getIndex()) e transformado em list()*/
	// public function postAllCourses()
	// {
	// 	if ($this->idUser) {
	// 		$courses = Course::where("idInstitution", $this->idUser)->whereStatus("E")->orderBy("name")->get();
	//
	// 		foreach ($courses as $course) {
	// 			$course->id = Crypt::encrypt($course->id);
	// 		}
	// 		return $courses;
	// 	}
	// }

	public function read(Request $in)
	{
		if (!isset($in->course_id)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$course = Course::find(Crypt::decrypt($in->course_id);
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

		$course = Course::find(Crypt::decrypt($in->course_id);
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
