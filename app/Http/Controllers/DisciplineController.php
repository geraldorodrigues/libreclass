<?php

namespace App\Http\Controllers;

use App\MySql\Discipline;
use App\MySql\User;
use App\MySql\Course;
use App\MySql\Period;
use Crypt;
use Session;
use Redirect;

class DisciplineController extends Controller
{
	public function save(Request $in)
	{
		if (!isset($in->period_id) || !isset($in->name || !isset($in->syllabus))){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$period = Period::find(Crypt::decrypt($in->period_id));
		if (!$period){
			return ['status'=>0, 'message'=>'Período não encontrado'];
		}
		if ($period->course->institution_id != auth()->id()){
			return ['status'=>0, 'message'=>'Operação não autorizada'];
		}

		if (isset($in->discipline_id)) {//Edição
			$discipline = Discipline::find(Crypt::decrypt($in->discipline_id));
		} else {
			$discipline = new Discipline;
		}
		$discipline->period_id = $period->id;
		$discipline->name = $in->name;
		$discipline->ementa = $in->syllabus;
		$discipline->save();

		unset($disciplines->created_at);
		unset($disciplines->updated_at);
		$discipline->id = Crypt::encrypt($discipline->id);

		return ['status'=>1, 'discipline'=>$discipline];
	}

	public function list(Request $in)
	{
		if (!isset($in->period_id)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$period = Period::find(Crypt::decrypt($in->period_id));
		if (!$period){
			return ['status'=>0, 'message'=>'Período não encontrado'];
		}
		if ($period->course->institution_id != auth()->id()){
			return ['status'=>0, 'message'=>'Operação não autorizada'];
		}

		$disciplines = $period->disciplines;

		foreach ($disciplines as $discipline) {
			unset($disciplines->created_at);
			unset($disciplines->updated_at);
			$discipline->id = Crypt::encrypt($discipline->id);
		}

		return ['status'=>1, 'disciplines'=>$disciplines];
	}

	public function read(Request $in)
	{
		if (!isset($in->discipline_id)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$discipline = Discipline::find(Crypt::decrypt($in->discipline_id));
		if (!$discipline){
			return ['status'=>0, 'message'=>'Disciplina não encontrada'];
		}

		unset($disciplines->created_at);
		unset($disciplines->updated_at);
		$discipline->id = Crypt::encrypt($discipline->id);

		return ['status'=>1, 'discipline'=>$discipline];
	}

	public function delete(Request $in)
	{
		if (!isset($in->discipline_id)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$discipline = Discipline::find(Crypt::decrypt($in->discipline_id));
		if (!$discipline){
			return ['status'=>0, 'message'=>'Disciplina não encontrada'];
		}
		if ($discipline->period->course->institution_id != auth()->id()){
			return ['status'=>0, 'message'=>'Operação não autorizada'];
		}

		if ($discipline->offers()->count()){
			return ['status'=>0, 'message'=>'Não foi possível excluir. Disciplina já vinculada a turma(s)'];
		}

		$discipline->delete();

		return ['status'=>1];
	}
}
