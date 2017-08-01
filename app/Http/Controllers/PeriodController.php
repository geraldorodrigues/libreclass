<?php

namespace App\Http\Controllers;

use App\MySql\Course;
use App\MySql\Period;

use Session;
use Crypt;
use Redirect;

class PeriodController extends Controller
{
	public function save(Request $in)
	{
		if (auth()->user()->type != 'I'){
			return ['status'=>0, 'message'=>'Operação não autorizada'];
		}

		if (!isset($in->name)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		if (isset($in->period_id)){//Edição
			$period = Period::find(Crypt::decrypt($in->period_id);
			if (!$period){
				return ['status'=>0, 'message'=>'Período não encontrado'];
			}
			if ($period->course->institution_id != auth()->id()){
				return ['status'=>0, 'message'=>'Operação não autorizada'];
			}

			$period->name = $in->name;
			$period->save();
		} else {//Criação
			if (!isset($in->course_id)){
				return ['status'=>0, 'message'=>'Operação não autorizada'];
			}
			$course = Course::find(Crypt::decrypt($in->course_id));
			if (!$course){
				return ['status'=>0, 'message'=>'Curso não encontrado'];
			}
			$period = $course->periods()->create(['name'=>$in->name]);
		}

		return ['status'=>1, 'period'=>$period];
	}

	public function list(Request $in)
	{
		if (auth()->user()->type != 'I'){
			return ['status'=>0, 'message'=>'Operação não autorizada'];
		}

		if (isset($in->course_id)){
			$course = Course::find(Crypt::decrypt($in->course_id));
			if (!$course){
				return ['status'=>0, 'message'=>'Curso não encontrado'];
			}
			if ($course->institution_id != auth()->id()){
				return ['status'=>0, 'message'=>'Operação não autorizada'];
			}
			$periods = $course->periods;
		} else {
			$courses_ids = auth()->user()->courses()->get(['_id'])->pluck('_id');
			$periods = Period::whereIn('course_id', $courses_ids)->get();
		}

		foreach ($periods as $period) {
			$period->id = Crypt::encrypt($period->id);
			unset($period->course_id);
			unset($period->created_at);
			unset($period->updated_at);
		}

		return ['status'=>1, 'periods'=>$periods];
	}
}
