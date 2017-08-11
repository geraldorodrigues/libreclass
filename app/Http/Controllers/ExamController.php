<?php

namespace App\Http\Controllers;

use App\MongoDb\User;
use App\MongoDb\Exam;
use App\MongoDb\Attend;
use App\MongoDb\Unit;
use App\MongoDb\ExamsValue;
use App\MongoDb\FinalExam;
use App\MongoDb\Offer;
use App\MongoDb\Lecture;

use Crypt;
use Session;
use Redirect;

class ExamController extends Controller
{
	// public function getIndex()
	// {
	// 	$exam = Exam::find(Crypt::decrypt(Input::get("e")));
	// 	$students = null;
	// 	if ($exam->aval == "A") {
	// 		$students = Attend::where("unit_id", $exam->unit_id)->get();
	// 	}
	// 	//	elseif (	) /* gerar recuperação */
	// 	//		$students = Attend::where("unit_id", $exam->unit_id)->where->get();
	// 	//		$students = DB::select("SELECT Users.name AS name, Attends.id AS id_attend, Frequencies.value AS value
	// 	//														FROM Frequencies, Attends, Users
	// 	//														WHERE Frequencies.id_attend=Attends.id AND Attends.idUser=Users.id AND Frequencies.idLesson=?
	// 	//														ORDER BY Users.name", [$lesson->id]);
	// 	return view("modules.avaliable", ["user" => auth()->user(), "exam" => $exam, "students" => $students, "unit" => Unit::find($exam->unit_id)]);
	// 	//	return view("modules.avaliable", ["user" => $user, "students" => $students]);
	// }

	public function save(Request $in)
	{
		if (isset($in->exam_id)) {//Edição
			$exam = Exam::find(Crypt::decrypt($in->exam_id));
		} else {//Criação
			if (!isset($in->unit_id)){
				return ['status'=>0, 'message'=>'Dados incompletos'];
			}

			$unit = Unit::find(Crypt::decrypt($in->unit_id));
			if (!$unit){
				return ['status'=>0, 'message'=>'Unidade não encontrada'];
			}

			$exam = $unit->exams()->create();

			// $exam->aval = "A";
		}
		$exam->date = $in->date;
		$exam->title = $in->title;
		$exam->weight = $in->weight;
		$exam->type = $in->type;
		$exam->comments = $in->comments;
		$exam->save();

		unset($in->created_at);
		unset($in->updated_at);
		$exam->id = Crypt::encrypt($exam->id);

		return ['status'=>1, 'exam'=>$exam];
		// if (!Input::has("exam")) {
		// 	$attends = Attend::where("unit_id", $exam->unit_id)->get();
		// 	foreach ($attends as $attend) {
		// 		$value = new ExamsValue;
		// 		$value->id_attend = $attend->id;
		// 		$value->exam_id = $exam->id;
		// 		$value->value = "";
		// 		$value->save();
		// 	}
		// }

		// return Redirect::to("/lectures/units?u=" . Crypt::encrypt($exam->unit_id))->with("success", "Avaliação atualizada com sucesso.");
	}

	public function result(Request $in)
	{
		if (!isset($exam_id) || !isset($attend_id)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$exam = Exam::find(Crypt::decrypt($in->exam_id));
		if (!$exam){
			return ['status'=>0, 'message'=>'Exame não encontrado'];
		}

		$attend = Attend::find(Crypt::decrypt($in->attend_id));
		if ($attend){
			return ['status'=>0, 'message'=>'Aluno não encontrado nesta turma'];
		}

		$value = (float) str_replace(",", ".", $in->value);

		$average = $attend->unit()->offer()->class()->period()->course()->average;

		if (($average > 10 && ($value > 100 || $value < 0)) || ($average <= 10 && ($value > 10 || $value < 0))) {
			return ['status'=>0, 'message'=>'Nota inválida'];
		} else {
			if ($average <= 10) {
				$value = sprintf("%.2f", $value);
			}
		}
		if (Result::where("attend_id", $attend->id)->where("exam_id", $exam->id)->count()) {
			Result::where("attend_id", $attend->id)->where("exam_id", $exam->id)->update(["value" => $value]);
		} else {
			$result = new Result;
			$result->attend_id = $attend->id;
			$result->exam_id = $exam->id;
			$result->value = $value;
			$result->save();
		}

		return ['status'=>1];
	}

	// public function descriptive()
	// {
	// 	try {
	// 		$exam = Crypt::decrypt(Input::get("exam"));
	// 		$attend = Crypt::decrypt(Input::get("student"));
	// 		$examsvalue = DescriptiveExam::where("id_attend", $attend)->where("exam_id", $exam)->first();
	// 		if ($examsvalue) {
	// 			DescriptiveExam::where("id_attend", $attend)->where("exam_id", $exam)->update(["description" => Input::get("description"), "approved" => Input::get("approved")]);
	// 		} else {
	// 			$examsvalue = new DescriptiveExam;
	// 			$examsvalue->id_attend = $attend;
	// 			$examsvalue->exam_id = $exam;
	// 			$examsvalue->description = Input::get("description");
	// 			$examsvalue->approved = Input::get("approved");
	// 			$examsvalue->save();
	// 		}
	// 		return Response::json([
	// 			"status" => 1,
	// 			"description" => $examsvalue->description,
	// 			"approved" => $examsvalue->approved,
	// 		]);
	// 	} catch (Exception $e) {
	// 		return Response::json(["status" => 0, "message" => $e->getMessage()]);
	// 	}
	// }

	// public function getFinalunit($unit = "")
	// {
	// 	$unit = Crypt::decrypt($unit);
	// 	$final = Exam::whereAval("R")->where("unit_id", $unit)->first();
	// 	if (!$final) {
	// 		$final = new Exam;
	// 		$final->aval = "R";
	// 		$final->title = "Recuperação da Unidade";
	// 		$final->type = 2;
	// 		$final->unit_id = $unit;
	// 		$final->date = date("Y-m-d");
	// 	}
	// 	$course = Unit::find($unit)->getOffer()->getDiscipline()->getPeriod()->getCourse();
	// 	$attends = Attend::where("unit_id", $unit)->get();
	// 	return view("modules.units.retrieval", ["exam" => $final, "user" => auth()->user(), "attends" => $attends, "average" => $course->average]);
	// }

// 	public function postFinalunit($unit = "")
// 	{
// 		$cUnit = Unit::find(Crypt::decrypt($unit));
// 		$exam = Exam::where("unit_id", $cUnit->id)->whereAval("R")->first();
// 		if (!$exam) {
// 			$exam = new Exam;
// 			$exam->aval = "R";
// 			$exam->unit_id = $cUnit->id;
// 		}
// 		$exam->title = "Recuperação da Unidade $cUnit->value";
// 		$exam->date = Input::get("date-year") . "-" . Input::get("date-month") . "-" . Input::get("date-day");
// 		$exam->type = Input::get("type");
// 		$exam->comments = Input::get("comment");
// 		$exam->save();
// 		return Redirect::to("/lectures/units?u=$unit")->with("success", "Avaliação atualizada com sucesso.");
// //	return Redirect::to("avaliable/finalunit/$unit")->with("message", "Avaliação atualizada com sucesso.");
// 	}

	// public function postFinaldiscipline($id = "")
	// {
	// 	$offer = Offer::find(Crypt::decrypt($id));
	// 	$offer->dateFinal = Input::get("date-year") . "-" . Input::get("date-month") . "-" . Input::get("date-day");
	// 	$offer->typeFinal = Input::get("type");
	// 	$offer->comments = Input::get("comment");
	// 	$offer->save();
	// 	return Redirect::to("avaliable/finaldiscipline/$id")->with("success", "Recuperação Final atualizada com sucesso");
	// }

	public function postOffer()
	{
		$offer = Crypt::decrypt(Input::get("offer"));
		$student = Crypt::decrypt(Input::get("student"));
		$value = (float) str_replace(",", ".", Input::get("value"));

		$average = Offer::find($offer)->getClass()->getPeriod()->getCourse()->average;

		if (($average > 10 && ($value > 100 || $value < 0)) || ($average <= 10 && ($value > 10 || $value < 0))) {
			throw new Exception('Invalid value.');
		} else {
			if ($average <= 10) {
				$value = sprintf("%.2f", $value);
			}
		}

		if (FinalExam::where("idUser", $student)->where("idOffer", $offer)->first()) {
			FinalExam::where("idUser", $student)->where("idOffer", $offer)->update(["value" => $value]);
		} else {
			$offervalue = new FinalExam;
			$offervalue->idUser = $student;
			$offervalue->idOffer = $offer;
			$offervalue->value = $value;
			$offervalue->save();
		}
		return $value;
	}

	public function getFinaldiscipline($offer = "")
	{
		$offer = Offer::find(Crypt::decrypt($offer));

		/* caso não tenha data marcada, coloque a data de hoje */
		if (strtotime($offer->dateFinal) < 0) {
			$offer->dateFinal = date("Y-m-d");
		}

		if (!Lecture::where("idUser", auth()->id())->where("idOffer", $offer->id)->first()) {
			return Redirect::to("/logout");
		}
		$units = Unit::where("idOffer", $offer->id)->get();
		$course = Offer::find($offer->id)->getDiscipline()->getPeriod()->getCourse();
		$alunos = DB::select("select Users.id, Users.name
													from Attends, Units, Users
													where Units.idOffer=? AND Units.id=Attends.unit_id AND Attends.idUser=Users.id
													group by Attends.idUser
													order by Users.name", [$offer->id]);
		foreach ($alunos as $aluno) {
			$aluno->absence = $offer->qtdAbsences($aluno->id);
			$aluno->averages = [];
			$sum = 0.;
			foreach ($units as $unit) {
				$exam = $unit->getAverage($aluno->id);
				$aluno->averages[$unit->value] = $exam[0] < $course->average ? $exam[1] : $exam[0];
				$sum += $aluno->averages[$unit->value];
			}
			$aluno->med = $sum / count($units);
			$final = FinalExam::where("idUser", $aluno->id)->where("idOffer", $offer->id)->first();
			$aluno->final = $final ? $final->value : "";
		}
		return view("modules.disciplines.retrieval", ["user" => auth()->user(), "alunos" => $alunos, "course" => $course, "offer" => $offer]);
	}

	public function getAverageUnit($unit)
	{
		$final = Exam::whereAval("R")->where("unit_id", $unit->id)->first();
		$qtdExam = Exam::whereAval("A")->where("unit_id", $unit->id)->count();
		$sumWeight = Exam::whereAval("A")->where("unit_id", $unit->id)->sum("weight");
		$sumWeight = $sumWeight ? $sumWeight : 1;
		$attends = Attend::where("unit_id", $unit->id)->get();
		foreach ($attends as $attend) {
			if ($final and ($examfinal = ExamsValue::where("id_attend", $attend->id)->where("exam_id", $final->id)->first())) {
				$attend->final = $examfinal->value;
			} else {
				$attend->final = "F";
			}
			$values = ExamsValue::where("id_attend", $attend->id)->get();
			$sum = 0.;
			foreach ($values as $value) {
				$sum += $value->value * Exam::find($value->exam_id)->weight;
			}
			$attend->media = $sum / $sumWeight;
			$attend->name = User::find($attend->idUser)->name;
//		$result = $media < $course->average ? "FINAL" : "APROVADO";
			//		echo User::find($attend->idUser)->name . " | $sumWeight | $sum | $media | $result<br>";
		}
//	echo "Total de avaliações: $qtdExam<br>";
		//	echo "Peso: $sumWeight<br>";
		//	echo "Média do curso: $course->average<br><br>";
		return $attends;
	}

	public function getListstudentsexam($exam = "")
	{
		$exam = Exam::find(Crypt::decrypt($exam));
		$students = null;

		$calculation = $exam->unit->calculation;

		if ($exam->aval == "A") {
			$students = Attend::where("unit_id", $exam->unit_id)->get();
		}

		switch ($calculation) {
			case "S": // Soma
			case "A": // Média Aritmética
			case "W": // Média Ponderada
				return view("modules.liststudentsexam", ["user" => auth()->user(), "exam" => $exam, "students" => $students]);
				break;
			case "P": // Parecer Descritivo
				return view("modules.liststudentsexamDescriptive", ["user" => auth()->user(), "exam" => $exam, "students" => $students]);
				break;
		}
		// return Crypt::decrypt($exam);
	}

	public function delete(Request $in)
	{
		if (!isset($in->exam_id)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$exam = Exam::find(Crypt::decrypt($in->exam_id));
		if (!$exam){
			return ['status'=>0, 'message'=>'Exame não encontrado'];
		}

		if ($exam->unit->status == 'D'){
			return ['status'=>0, 'message'=>'Não é possível excluir o exame. Unidade desabilitada'];
		}

		if ($exam->results()->count()){
			return ['status'=>0, 'message'=>'Não é possível excluir o exame. Já possui resultados'];
		}

		$exam->status = "D";
		$exam->save();

		return ['status'=>1];
	}

}
