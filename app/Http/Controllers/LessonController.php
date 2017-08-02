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
	private $idUser;

	public function __construct()
	{
		$id = Session::get("user");
		if ($id == null || $id == "") {
			$this->idUser = false;
		} else {
			$this->idUser = Crypt::decrypt($id);
		}

	}

	public function index(Requesg $in)
	{

		if (auth()->id()) {
			$user = User::find(auth()->id());

			$lesson = Lesson::find(Crypt::decrypt($in->lesson_id));

			/*$students = DB::select("SELECT Users.name AS name, Attends.id AS idAttend, Frequencies.value AS value, Units.idOffer, Attends.idUser
																FROM Frequencies, Attends, Users, Units
																WHERE Frequencies.idAttend=Attends.id AND
																			Attends.idUser=Users.id AND
																			Frequencies.idLesson=? AND
																			Attends.idUnit=Units.id
																ORDER BY Users.name", [$lesson->id]);*/

			$students = [];
			$frequencies = Frequencies::where('lesson_id', $in->lesson_id)->get(['attend_id','value']);
			foreach ($frequencies as $frequency) {
				$attend = $frequency->attend;
				$student = $attend->student;
				$student->value = $frequency->value;
				$student->attend_id = $frequency->attend_id;
				$student->offer_id = $attend->offer_id;
				$students[] = $student;
			}

			foreach ($students as $student) {
				/*$frequency = DB::select("SELECT Offers.maxlessons, COUNT(*) as qtd "
					. "FROM Offers, Units, Attends, Frequencies "
					. "WHERE Offers.id=? AND Offers.id=Units.idOffer AND Units.id=Attends.idUnit "
					. "AND Attends.idUser=? AND Attends.id=Frequencies.idAttend AND Frequencies.value='F'",
					[$student->idOffer, $student->idUser])[0];*/
				$student->maxlessons = Offer::where('_id', $student->offer_id)->first(['maxlessons'])->maxlessons;
				$student->qtd = Attend::where('_id', $student->attend_id)->frequency()->where('value','F')->count();
				/*$student->maxlessons = $frequency->maxlessons;
				$student->qtd = $frequency->qtd;*/
			}

			return ["status" => 1, "user" => $user, "lesson" => $lesson, "students" => $students]);
		} else {
			return Redirect::guest("/");
		}
	}

	public function newLesson(Request $in)
	{
		$unit = Unit::find(Crypt::decrypt($in->unit_id));

		$lesson = new Lesson;
		$lesson->idUnit = $unit->id;
		$lesson->date = date("Y-m-d");
		$lesson->title = "Sem título";
		$lesson->save();

		$attends = Attend::where("unit_id", $unit->id)->get();
		foreach ($attends as $attend) {
			$frequency = new Frequency;
			$frequency->attend_id = $attend->id;
			$frequency->lesson_id = $lesson->id;
			$frequency->value = "P";
			$frequency->save();
		}

		return ['status' => 1, 'lesson' => $lesson];
	}

	public function save(Request $in)
	{
		//~ var_dump(Input::all());

		$lesson = Lesson::find(Crypt::decrypt($in->lesson_id));

		$values = ['date','title','description','goals','content','methodology','resources','valuation','estimatedTime','keyworks','bibliography','notes'];

		foreach($values as $value) {
			$lesson->{$value} = $in->{$value};
		}

		/*$lesson->date = Input::get("date-year") . "-" . Input::get("date-month") . "-" . Input::get("date-day");
		$lesson->title = Input::get("title");
		$lesson->description = Input::get("description");
		$lesson->goals = Input::get("goals");
		$lesson->content = Input::get("content");
		$lesson->methodology = Input::get("methodology");
		$lesson->resources = Input::get("resources");
		$lesson->valuation = Input::get("valuation");
		$lesson->estimatedTime = Input::get("estimatedTime");
		$lesson->keyworks = Input::get("keyworks");
		$lesson->bibliography = Input::get("bibliography");
		$lesson->notes = Input::get("notes");
		$lesson->save();*/

		//~ return $lesson;
		/*$unit = DB::select("SELECT Units.id, Units.status
													FROM Units, Lessons
													WHERE Units.id = Lessons.idUnit AND
														Lessons.id=?", [$lesson->id]);*/

		//return Redirect::guest("/lectures/units?u=" . Crypt::encrypt($unit[0]->id))->with("success", "Aula atualizada com sucesso");
		return ['status' => 1, 'lesson' => $lesson];
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

	public function delete(Request $in)
	{
		$lesson = Lesson::find(Crypt::decrypt($in->lesson_id));

		/*$unit = DB::select("SELECT Units.id, Units.status
													FROM Units, Lessons
													WHERE Units.id = Lessons.idUnit AND
														Lessons.id=?", [$lesson->id]);*/

		$unit = $lesson->unit;

		if ($unit->status == 'D') {
			return ['status' => 0, 'message' => "Não foi possível deletar.<br>Unidade desabilitada."];
		}
		if ($lesson) {
			$lesson->status = "D";
			$lesson->save();
			return ['status' => 1, 'message'=>"Aula excluída com sucesso!"];
		} else {
			return ['status' => 0, 'message' => "Não foi possível deletar"];
		}
	}

	public function info(Request $in)
	{
		$lesson = Lesson::find(Crypt::decrypt($in->lesson_id));
		$lesson->date = date("d/m/Y", strtotime($lesson->date));
		return $lesson;
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

	/**
	 * seleciona as ofertas ministradas pelo professor que está logado
	 *
	 * @return lista das ofertas
	 */
	public function listOffers()
	{
		/*$offers = DB::select("SELECT Offers.id, Disciplines.name, Classes.class FROM Lectures, Offers, Classes, Disciplines "
			. "WHERE Lectures.idUser=? AND Lectures.idOffer=Offers.id AND Offers.idClass=Classes.id AND Offers.idDiscipline=Disciplines.id",
			[$this->idUser]);*/
		$offers_ids = Lecture::where('user_id',auth()->id())->get(['offer_id'])->pluck('offer_id');
		$offers = Offer::whereIn('_id',$offers_ids)->get();

		foreach ($offers as $offer) {
			$offer->id = Crypt::encrypt($offer->id);
		}

		return $offers;
	}

	public function delete(Request $in)
	{
		// return Crypt::decrypt(Input::get("input-trash"));
		Lesson::find(Crypt::decrypt($in->lesson_id))->delete();
		return  ['status' => 1, 'message' => "Aula excluída!"];
	}
}
