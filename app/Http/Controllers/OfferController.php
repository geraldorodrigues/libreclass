<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MongoDb\User;
use App\MongoDb\Classe;
use App\MongoDb\Period;
use App\MongoDb\Offer;
use App\MongoDb\Course;
use App\MongoDb\Unit;
use App\MongoDb\Lector;
use App\MongoDb\Attend;

use Session;
use Crypt;
use Redirect;

class OfferController extends Controller
{
	//Cadastra a oferta de uma disciplina em uma turma
	public function save(Request $in)
	{
		if (!isset($in->classe_id) || !isset($in->discipline_id)){
			return ['status' => 0, 'message' => 'Dados incompletos'];
		}

		$classe = Classe::find(Crypt::decrypt($in->classe_id));
		if (!$classe) {
			return ['status' => 0, 'message' => 'Turma não encontrada'];
		}

		$discipline = Discipline::find(Crypt::decrypt($in->discipline_id));
		if (!$discipline) {
			return ['status' => 0, 'message' => 'Disciplina não encontrada'];
		}

		if (isset($in->offer_id)){//Edição
			$offer = Offer::find(Crypt::decrypt($in->offer_id));
			if (!$offer) {
				return ['status' => 0, 'message' => 'Oferta não encontrada'];
			}
		} else {//Criação
			if (!isset($in->calculation)){
				return ['status' => 0, 'message' => 'Dados incompletos'];
			}

			$offer = new Offer;
			$offer->classe_id = $classe->id;
			$offer->discipline_id = $discipline->id;
			$offer->status = 'E';

			//Cria a primeira Unidade
			$unit = $offer->units()->create(['value'=>'1', 'calculation'=>$in->calculation]);
		}

		$offer->classroom = $in->classroom;
		$offer->day_period = $in->day_period;
		$offer->max_lessons = $in->max_lessons;
		$offer->comments = $in->comments;
		$offer->save();

		unset($offer->created_at);
		unset($offer->updated_at);
		$offer->id = Crypt::encrypt($offer->id);

		return ['status'=>1, 'offer'=>$offer];
	}


	// public function teacher(Request $in)
	// {
	// 	if (!isset($in->offer_id)){
	// 		return ['status' => 0, 'message' => 'Dados incompletos'];
	// 	}
	// 	$offer = Offer::find(Crypt::decrypt($in->offer_id));
	// 	if (!$offer) {
	// 		return ['status' => 0, 'message' => 'Oferta não encontrada'];
	// 	}
	//
	// 	$lectures = $offer->getAllLectures();
	//
	// 	$teachers = [];
	// 	if (Input::has("teachers")) {
	// 		$teachers = Input::get("teachers");
	// 		for ($i = 0; $i < count($teachers); $i++) {
	// 			$teachers[$i] = base64_decode($teachers[$i]);
	// 		}
	//
	// 	}
	// 	// return $teachers;
	// 	foreach ($lectures as $lecture) {
	// 		$find = array_search($lecture->idUser, $teachers);
	// 		if ($find === false) {
	// 			Lecture::where('idOffer', $offer->id)->where('idUser', $lecture->idUser)->delete();
	// 		} else {
	// 			unset($teachers[$find]);
	// 		}
	//
	// 	}
	//
	// 	foreach ($teachers as $teacher) {
	// 		$last = Lecture::where("idUser", $teacher)->orderBy("order", "desc")->first();
	// 		$last = $last ? $last->order + 1 : 1;
	//
	// 		$lecture = new Lecture;
	// 		$lecture->idUser = $teacher;
	// 		$lecture->idOffer = $offer->id;
	// 		$lecture->order = $last;
	// 		$lecture->save();
	// 	}
	//
	// 	//	 $idTeacher = Crypt::decrypt(Input::get("teacher"));
	// 	//	 $last = Lecture::where("idUser", $idTeacher)->orderBy("order", "desc")->first();
	// 	//	 $last = $last ? $last->order+1 : 1;
	// 	//
	// 	//	 if (!$lecture) {
	// 	//		 $lecture = new Lecture;
	// 	//		 $lecture->idUser = $idTeacher;
	// 	//		 $lecture->idOffer = $offer->id;
	// 	//		 $lecture->order = $last;
	// 	//		 $lecture->save();
	// 	//	 }
	// 	//	 else if($lecture->idUser != $idTeacher) {
	// 	//		 Lecture::where('idOffer', $offer->id)->where('idUser', $lecture->idUser)->update(["idUser" => $idTeacher, "order" => $last]);
	// 	//	 }
	// 	// }
	// 	// else if ($lecture)
	// 	// {
	// 	//	 Lecture::where('idOffer', $offer->id)->where('idUser', $lecture->idUser)->delete();
	// 	// }
	//
	// 	return Redirect::guest(Input::get("prev"))->with("success", "Modificado com sucesso!");
	//
	// }

	public function changeStatus(Request $in)
	{
		if (!isset($in->offer_id) || !isset($in->status)){
			return ['status' => 0, 'message' => 'Dados incompletos'];
		}
		$offer = Offer::find(Crypt::decrypt($in->offer_id));
		if (!$offer) {
			return ['status' => 0, 'message' => 'Oferta não encontrada'];
		}

		if (!in_array($in->status, ['E', 'D'])){
			return ['status' => 0, 'message' => 'Status inválido'];
		}

		$offer->status = $in->status;
		$offer->save();

		return ['status'=>1];
	}

	public function getStudents($offer)
	{
		if ($this->idUser) {
			$user = User::find($this->idUser);

			//$students = User::whereType("N")->orderby("name")->get();
			//$list_students = [];
			//foreach( $students as $student )
			//$list_students[Crypt::encrypt($student->id)] = $student->name;
			$info = DB::select("SELECT Courses.name as course, Periods.name as period, Classes.id as idClass, Classes.class as class
													FROM Courses, Periods, Classes, Offers
													WHERE Courses.id = Periods.idCourse
													AND Periods.id = Classes.idPeriod
													AND Classes.id = Offers.idClass
													AND Offers.id = " . Crypt::decrypt($offer) . "
													");
			$students = DB::select("SELECT Users.name as name, Users.id as id, Attends.status as status
															FROM Users, Attends, Units
															WHERE Users.id=Attends.idUser
															AND Attends.idUnit = Units.id
															AND Units.idOffer = " . Crypt::decrypt($offer) . " GROUP BY Users.id ORDER BY Users.name");

			return view("modules.liststudentsoffers", ["user" => $user, "info" => $info, "students" => $students, "offer" => $offer]);
		} else {
			return Redirect::guest("/");
		}
	}

	public function postStatusStudent()
	{

		//~ return Input::all();
		$offer = Crypt::decrypt(Input::get("offer"));
		$student = Crypt::decrypt(Input::get("student"));
		$units = Unit::where("idOffer", $offer)->get();

		if (Input::get("status") == 'M') {
			foreach ($units as $unit) {
				Attend::where('idUnit', $unit->id)->where('idUser', $student)->update(["status" => 'M']);
			}

		}

		if (Input::get("status") == 'D') {
			foreach ($units as $unit) {
				Attend::where('idUnit', $unit->id)->where('idUser', $student)->update(["status" => 'D']);
			}

		}

		if (Input::get("status") == 'T') {
			foreach ($units as $unit) {
				Attend::where('idUnit', $unit->id)->where('idUser', $student)->update(["status" => 'T']);
			}

		}

		if (Input::get("status") == 'R') {
			foreach ($units as $unit) {
				Attend::where("idUnit", $unit->id)->where("idUser", $student)->delete();
			}

			return Redirect::back()->with("success", "Aluno removido com sucesso");
		}
		return Redirect::back()->with("success", "Status atualizado com sucesso");
	}

	public function anyDeleteLastUnit($offer)
	{
		$offer = Offer::find(Crypt::decrypt($offer));

		$unit = Unit::where('idOffer', $offer->id)->orderBy('value', 'desc')->first();
		$unit->delete();

		return Redirect::to("/classes/offers?t=" . Crypt::encrypt($offer->idClass))->with("success", "Unidade deletada com sucesso!");
	}

}
