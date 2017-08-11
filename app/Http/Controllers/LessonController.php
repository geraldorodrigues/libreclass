<?php

namespace App\Http\Controllers;

use App\MongoDb\Lesson;
use App\MongoDb\User;
use App\MongoDb\Attend;
use App\MongoDb\Frequency;
use Session;
use Crypt;
use Redirect;

class LessonController extends Controller
{
	// public function index(Requesg $in)
	// {
	// 	$lesson = Lesson::find(Crypt::decrypt($in->lesson_id));
	//
	// 	/*$students = DB::select("SELECT Users.name AS name, Attends.id AS idAttend, Frequencies.value AS value, Units.idOffer, Attends.idUser
	// 														FROM Frequencies, Attends, Users, Units
	// 														WHERE Frequencies.idAttend=Attends.id AND
	// 																	Attends.idUser=Users.id AND
	// 																	Frequencies.idLesson=? AND
	// 																	Attends.idUnit=Units.id
	// 														ORDER BY Users.name", [$lesson->id]);*/
	//
	// 	$students = [];
	// 	$frequencies = Frequency::where('lesson_id', $in->lesson_id)->get(['attend_id','value']);
	// 	foreach ($frequencies as $frequency) {
	// 		$attend = $frequency->attend;
	// 		$student = $attend->student;
	// 		$student->value = $frequency->value;
	// 		$student->attend_id = $frequency->attend_id;
	// 		$student->offer_id = $attend->offer_id;
	// 		$students[] = $student;
	// 	}
	//
	// 	foreach ($students as $student) {
	// 		/*$frequency = DB::select("SELECT Offers.maxlessons, COUNT(*) as qtd "
	// 			. "FROM Offers, Units, Attends, Frequencies "
	// 			. "WHERE Offers.id=? AND Offers.id=Units.idOffer AND Units.id=Attends.idUnit "
	// 			. "AND Attends.idUser=? AND Attends.id=Frequencies.idAttend AND Frequencies.value='F'",
	// 			[$student->idOffer, $student->idUser])[0];*/
	// 		$student->maxlessons = Offer::where('_id', $student->offer_id)->first(['maxlessons'])->maxlessons;
	// 		$student->qtd = Attend::where('_id', $student->attend_id)->frequency()->where('value','F')->count();
	// 		/*$student->maxlessons = $frequency->maxlessons;
	// 		$student->qtd = $frequency->qtd;*/
	// 	}
	//
	// 	return ["status" => 1, "lesson" => $lesson, "students" => $students]);
	// }

	public function save(Request $in)
	{
		if (isset($in->lesson_id)){//Edição

		} else {//Criação
			if (!isset($in->unit_id)){
				return ['status'=>0, 'message'=>'Dados incompletos'];
			}

			$unit = Unit::find(Crypt::decrypt($in->unit_id));
			if (!$unit){
				return ['status'=>0, 'message'=>'Unidade não encontrada'];
			}

			$lesson = $unit->lessons()->create([]);

			//Lista de presença para esta aula
			foreach ($unit->attends as $attend) {
				$frequency = new Frequency;
				$frequency->attend_id = $attend->id;
				$frequency->lesson_id = $lesson->id;
				$frequency->value = "P";
				$frequency->save();
			}
		}

		foreach (['date', 'title', 'description', 'goals', 'content', 'methodology', 'resources', 'valuation', 'estimatedTime', 'keyworks', 'bibliography', 'notes'] as $key) {
				$lesson->$key = $in->$key;
		}

		$lesson->save();

		return ['status' => 1, 'lesson' => $lesson];
	}

	public function read(Request $in)
	{
		if (!isset($in->lesson_id)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$lesson = Lesson::find(Crypt::decrypt($in->lesson_id));
		if (!$lesson){
			return ['status'=>0, 'message'=>'Aula não encontrada'];
		}

		unset($lesson->created_at);
		unset($lesson->updated_at);
		$lesson->id = Crypt::encrypt($lesson->id);

		return ['status'=>1, 'lesson'=>$lesson];
	}

	public function delete(Request $in)
	{
		if (!isset($in->lesson_id)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$lesson = Lesson::find(Crypt::decrypt($in->lesson_id));
		if (!$lesson){
			return ['status'=>0, 'message'=>'Aula não encontrada'];
		}

		if ($lesson->unit->status == 'D'){
			return ['status'=>0, 'message'=>'Não é possível excluir a aula. Unidade desabilitada'];
		}

		//Verificar exclusão das frequências relacionadas à aula!!!
		// if ($lesson->frequencies()->count()){
		// 	return ['status'=>0, 'message'=>'Não é possível excluir a aula.'];
		// }

		$lesson->status = "D";
		$lesson->save();

		return ['status'=>1];
	}

	public function frequency(Request $in)
	{
		$attend = Attend::find(Crypt::decrypt($in->attend_id));
		$lesson_id = Crypt::decrypt($in->lesson);
		$value = $in->value == "P" ? "F" : "P";
		//$idOffer = DB::select("SELECT Units.idOffer FROM Lessons, Units WHERE Lessons.id=? AND Lessons.idUnit=Units.id", [$idLesson])[0]->idOffer;
		$offer_id = Lesson::where('_id',$lesson_id)->offer()->get(['_id'])->id;

		$status = Frequency::where("attend_id", $attend->id)->where("lesson_id", $lesson->id)->update(["value" => $value]);

		/*$frequency = DB::select("SELECT Offers.maxlessons, COUNT(*) as qtd FROM Offers, Units, Attends, Frequencies "
			. "WHERE Offers.id=? AND Offers.id=Units.idOffer AND Units.id=Attends.idUnit "
			. "AND Attends.idUser=? AND Attends.id=Frequencies.idAttend AND Frequencies.value='F'",
			[$idOffer, $attend->idUser])[0];*/
		$maxlessons = Offer::where('_id', $student->offer_id)->first(['maxlessons'])->maxlessons;
		$qtd = Attend::where('_id', $attend_id)->frequency()->where('value','F')->count();

		return ['status' => 1, 'value' => $value, 'qtd' => $qtd, 'percentage' => 100 * $frequency->qtd / $frequency->maxlessons]

		//return Response::json(["status" => $status, "value" => $value, "frequency" => sprintf("%d (%.1f %%)", $frequency->qtd, 100. * $frequency->qtd / $frequency->maxlessons)]);
	}

	/**
	 * Faz uma cópia de uma aula com ou sem frequecia
	 *		1 - cópia para a mesma unidade sem frequencia
	 *		2 - cópia para a mesma unidade com frequencia
	 *		3 - cópia para uma outra unidade sem frequencia
	 *
	 * @return type
	 */
	public function copy(Request $in)
	{
		$lesson = Lesson::find(Crypt::decrypt($in->lesson_id));
		/*$auth = DB::select("SELECT COUNT(*) as qtd FROM Units, Lectures WHERE Units.id=? AND Units.idOffer=Lectures.idOffer AND Lectures.idUser=?",
			[$lesson->idUnit, $this->idUser])[0]->qtd;*/
		$auth = Lesson::where('user_id',auth()->id())->unit()->count();
		if (!$auth) {
			return ['status' => 0];
		}

		$copy = $lesson->replicate();
		if ($in->type == 3) {
			$unit = Unit::where("offer_id", Crypt::decrypt($in->offer_id))->where('status', "E")->orderBy("value", "desc")->first();
			$copy->unit_id = $unit->id;
			$copy->save();

			$attends = Attend::where("unit_id", $unit->id)->get();
			foreach ($attends as $attend) {
				$frequency = new Frequency;
				$frequency->attend_id = $attend->id;
				$frequency->lesson_id = $copy->id;
				$frequency->value = "P";
				$frequency->save();
			}
		} else {
			$copy->save();
			$frequencies = Frequency::where("lesson_id", $lesson->id)->get();
			foreach ($frequencies as $frequency) {
				$frequency = $frequency->replicate();
				$frequency->lesson_id = $copy->id;
				if ($in->type == 1) {
					$frequency->value = "P";
				}

				$frequency->save();

			}
			$copy->id = Crypt::encrypt($copy->id);
			$copy->date = date("d/m/Y", strtotime($copy->date));
			return ['status' => 1, 'copy' => $copy];
		}
	}
}
