<?php

namespace App\Http\Controllers;

use App\MongoDb\User;
use App\MongoDb\Lecture;
use App\MongoDb\Offer;
use App\MongoDb\Unit;
use App\MongoDb\FinalExam;

use Session;
use Crypt;
use Redirect;

class LectureController extends Controller
{
	//Adiciona professor a uma oferta
	public function save(Request $in)
	{
		if (!isset($in->teacher_id) || !isset($in->offer_id)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$teacher = User::find(Crypt::decrypt($in->teacher_id));
		if (!$teacher) {
			return ['status' => 0, 'message' => 'Professor não encontrado'];
		}

		$offer = Offer::find(Crypt::decrypt($in->offer_id));
		if (!$offer) {
			return ['status' => 0, 'message' => 'Oferta não encontrada'];
		}

		if (Lecture::where('teacher_id', $teacher->id)->where('offer_id', $offer->id)->count()){
			return ['status' => 0, 'message' => 'Professor já está registrado para esta oferta'];
		}

		$lecture = Lecture::create(['teacher_id'=>$teacher->id, 'offer_id'=>$offer->id, 'order'=>$teacher->lectures->max('order')]);

		unset($lecture->created_at);
		unset($lecture->updated_at);
		$lecture->id = Crypt::encrypt($lecture->id);

		return ['status'=>1, 'lecture'=>$lecture];
	}

	//Remove professor de uma oferta
	public function delete(Request $in)
	{
		if (!isset($in->lecture_id)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$lecture = Lecture::find(Crypt::decrypt($in->lecture_id));
		if (!$lecture) {
			return ['status' => 0, 'message' => 'Relacionamento não encontrado'];
		}

		$lecture->delete();

		return ['status'=>1];
	}

	public function changeOrder(Request $in)
	{
		if (!isset($in->teacher_id) || !isset($in->lecture_id) || !isset($in->order)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$teacher = User::find(Crypt::decrypt($in->teacher_id));
		if (!$teacher) {
			return ['status' => 0, 'message' => 'Professor não encontrado'];
		}

		$lecture = Lecture::find(Crypt::decrypt($in->lecture_id));
		if (!$lecture) {
			return ['status' => 0, 'message' => 'Relacionamento não encontrado'];
		}

		$old_order = $lecture->order;
		$new_order = (int)$in->order;

		if ($new_order < $old_order){
			$lectures = $teacher->lectures()->where('order', '>=', $new_order)->where('order', '<', $old_order)->get();
			foreach ($lectures as $lecture) {
				$lecture->order++;
				$lecture->save();
			}
		} else {
			$lectures = $teacher->lectures()->where('order', '>', $old_order)->where('order', '<=', $new_order)->get();
			foreach ($lectures as $lecture) {
				$lecture->order--;
				$lecture->save();
			}
		}

		$lecture->order = $new_order;
		$lecture->save();

		return ['status'=>1];
	}


	public function finalReport(Request $in)
	{
		if (auth()->id()) {
			$user = User::find(auth()->id());
		}
		$offer = Offer::find(Crypt::decrypt($in->offer_id));
		$course = $offer->getDiscipline()->getPeriod()->getCourse();
		$qtdLessons = $offer->qtdLessons();

		/*$alunos = DB::select("SELECT Users.id, Users.name
													FROM Attends, Units, Users
													WHERE Units.idOffer=? AND Units.id=Attends.idUnit AND Attends.idUser=Users.id
													GROUP BY Attends.idUser
													ORDER BY Users.name", [$offer->id]);*/

		$units = Unit::where("offer_id", $offer->id)->get();

		$alunos = $units->attends()->user()->get();

		foreach ($alunos as $aluno) {
			$aluno->absence = $offer->qtdAbsences($aluno->id);
			$aluno->averages = [];
			$sum = 0.;
			foreach ($units as $unit) {
				$exam = $unit->getAverage($aluno->id);

				if ($exam[1] !== null) {
					$aluno->averages[$unit->value] = $exam[0] < $course->average ? $exam[1] : $exam[0];
				} else {
					$aluno->averages[$unit->value] = $exam[0];
				}

				$sum += $aluno->averages[$unit->value];
			}
			$aluno->med = $sum / count($units);

			if ($aluno->med >= $course->average) {
				$aluno->rec = "-";
				$aluno->result = "Ap. por nota";
				$aluno->status = "label-success";
			} else {
				$rec = FinalExam::where("offer_id", $offer->id)->where("user_id", $aluno->id)->first();
				$aluno->rec = $rec ? $rec->value : "0.00";
				if ($aluno->rec >= $course->averageFinal) {
					$aluno->result = "Aprovado";
					$aluno->status = "label-success";
				} else {
					$aluno->result = "Rep. por nota";
					$aluno->status = "label-danger";
				}
			}
			$qtdLessons = $qtdLessons ? $qtdLessons : 1; /* evitar divisão por zero */
			if ($aluno->absence / $qtdLessons * 100. > $course->absentPercent) {
				$aluno->result = "Rep. por falta";
				$aluno->status = "label-danger";
			}
		}

		return ["status" => 1,
				"user" => $user,
				"units" => $units,
				"students" => $alunos,
				"offer" => $offer,
				"qtdLessons" => $qtdLessons,
				"course" => $course];
	}

	public function frequency(Request $in)
	{
		$user = User::find(auth()->id());
		$offer = Offer::find(Crypt::decrypt($in->offer));
		$lectures_users_ids = $offer->lectures()->get(['user_id'])->pluck('user_id');
		if (!in_array(auth()->id(), $lectures_users_ids)) {
			return Redirect::to("/lectures")->with("error", "Você não tem acesso a essa página");
		}
		$units = Unit::where("offer_id", $offer->id)->get();
		/*$students = DB::select("select Users.id, Users.name "
			. "from Users, Attends, Units "
			. "where Units.idOffer=? and Attends.idUnit=Units.id and Attends.idUser=Users.id "
			. "group by Users.id order by Users.name", [$offer->id]);*/
		$students_ids = Unit::where("offer_id", $offer->id)->attends()->get(['user_id'])->pluck('user_id');
		$students = User::where('_id',$students_ids)->orderBy('name')->get();

		return ["status"=>1, "user" => $user, "offer" => $offer, "units" => $units, "students" => $students];
	}

	public function sort(Request $in)
	{
		foreach ($in->order as $key => $value) {
			Lecture::where('offer_id', Crypt::decrypt($value))->where('user_id', auth()->id())->update(["order" => $key + 1]);
		}
	}
}
