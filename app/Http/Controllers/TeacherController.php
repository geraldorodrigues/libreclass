<?php

namespace App\Http\Controllers;

use App\MongoDb\User;
use App\MongoDb\Relationship;
use App\MongoDb\Course;
use App\MongoDb\FinalExam;
use App\MongoDb\Offer;
use App\MongoDb\ExamsValue;
use App\MongoDb\Attend;
use App\MongoDb\Lesson;
use App\MongoDb\Frequency;
use App\MongoDb\Unit;
use App\MongoDb\DescriptiveExam;
use Crypt;
use Mail;
use StdClass;
use Session;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
	public function save(Request $in)
	{
		if (!in_array(auth()->user()->type, ['P', 'I'])){
			return ['status'=>0, 'message'=>'Operação não permitida'];
		}

		if (auth()->user()->type == 'P'){// Tipo P é professor com conta liberada. Ele mesmo deve atualizar as suas informações e não a instituição.
			$teacher = auth()->user();
		} else {//type == 'I'
			if (isset($in->teacher_id)) {//Edição
				$teacher = User::find(Crypt::decrypt($in->teacher_id));
				if (!$teacher) {
					return ['status' => 0, 'message' => "Professor não encontrado"];
				}

				// Tipo P é professor com conta liberada. Ele mesmo deve atualizar as suas informações e não a instituição.
				if ($teacher->type == "P") {
					return ['status' => 0, 'message' => "Professor não pode ser editado"];
				}

				// if (isset($in->email)){
				// 	if (User::where('email', trimpp(strtolower($in->email)))->where('register', '!=', $teacher->register)->count()){
				// 		return ['status' => 0, 'message' => "Email já cadastrado"];
				// 	}
				// 	$teacher->email = $in->email;
				// }
			} else {
				if (!isset($in->register)){
					return ['status' => 0, 'message' => "Dados incompletos"];
				}

				if (Relationship::where('institution_id', auth()->id())->where('register', $in->register)->count()) {
					return ['status' => 0, 'message' => "Este número de inscrição já está cadastrado"];
				}

				if (User::where('email', trimpp(strtolower($in->email)))->count()) {
					return ['status' => 0, 'message' => "Este email já cadastrado"];
				}

				$teacher = User::create();
				$teacher->type = "M";
				$teacher->email = $in->email;
				$teacher->password = Crypt::encrypt('12345');

				$relationship = new Relationship;
				$relationship->institution_id = auth()->id();
				$relationship->teacher_id = $teacher->id;
				$relationship->register = $in->register;
				$relationship->status = "E";
				$relationship->save();

				// $request = new Request;
				// $request->teacher_id = $teacher->id;
				//$this->invite($request);
			}
		}
		$teacher->name = $in->name;
		$teacher->formation = $in->formation;
		$teacher->gender = $in->gender;
		$teacher->birthdate = $in->birthdate;
		$teacher->save();

		unset($teacher->created_at);
		unset($teacher->updated_at);
		unset($teacher->password);
		$teacher->id = Crypt::encrypt($teacher->birthdate);

		return ['status' => 1, 'teacher' => $teacher];
	}

	public function list(Request $in)
	{
		if (auth()->user()->type != 'I'){
			return ['status'=>0, 'message'=>'Operação não permitida'];
		}
		$block = 30;
		if (!isset($in->search)){
			$in->search = "";
		}
		$current = (int) isset($in->current) ? $in->current : 0;

		$courses = Course::where('institution_id', auth()->id())->whereStatus("E")->orderBy("name")->get();
		/*$listCourses = ["" => ""];
		foreach ($courses as $course) {
			$listCourses[$course->name] = $course->name;
		}*/

		/*$relationships = DB::select("SELECT Users.id, Users.name, Relationships.enrollment, Users.type "
			. "FROM Users, Relationships "
			. "WHERE Relationships.idUser=? AND Relationships.type='2' AND Relationships.idFriend=Users.id "
			. "AND Relationships.status='E' AND (Users.name LIKE ? OR Relationships.enrollment=?) "
			. " ORDER BY name LIMIT ? OFFSET ?",
			[auth()->user()->id, "%$search%", $search, $block, $current * $block]);*/

		if ($in->search) {
			$teachers_ids = auth()->user()->relationships()->whereNotNull('teacher_id')->where('status', 'E')->get(['teacher_id'])->pluck('teacher_id');
			$teachers = User::whereIn('_id', $teachers_ids)->where('name', 'regexp', "/$in->search/i");
			$length = clone $teachers;
			$length = $length->count();
			$teachers = $teachers->skip($current * $block)->take($block)->get(['_id', 'name', 'type']);
		}
		elseif (isset($in->register)) {
			$teachers_ids = auth()->user()->relationships()->whereNotNull('teacher_id')->where('status', 'E')->where('register',$in->register)->get(['teacher_id'])->pluck('teacher_id');
			$teachers = User::whereIn('_id', $teachers_ids);
			$length = clone $teachers;
			$length = $length->count();
			$teachers = $teachers->skip($current * $block)->take($block)->get(['_id', 'name', 'type']);
		}
		else {
			$teachers_ids = auth()->user()->relationships()->whereNotNull('teacher_id')->where('status', 'E')->get(['teacher_id'])->pluck('teacher_id');
			$teachers = User::whereIn('_id', $teachers_ids);
			$length = clone $teachers;
			$length = $length->count();
			$teachers = $teachers->skip($current * $block)->take($block)->get(['_id', 'name', 'type']);
		}

		foreach ($teachers as $teacher) {
			$teacher->register = Relationship::where('teacher_id', $teacher->id)->where('institution_id', auth()->id())->where('status', 'E')->first(['register'])->register;
			//$teacher->selected = Lecture::where('user_id', $teacher->id)->where('offer_id', $offer)->count();
			unset($teacher->created_at);
			unset($teacher->updated_at);
			$teacher->id = Crypt::encrypt($teacher->id);
		}

		/*$length = DB::select("SELECT count(*) as 'length' "
			. "FROM Users, Relationships "
			. "WHERE Relationships.idUser=? AND Relationships.type='2' AND Relationships.idFriend=Users.id "
			. "AND (Users.name LIKE ? OR Relationships.enrollment=?) ", [auth()->user()->id, "%$search%", $search]);*/

		return [
			"status" => 1,
			"teachers" => $teachers,
			"length" => (int) $length,
			"block" => (int) $block,
			"current" => (int) $current,
		];
	}

	public function read(Request $in)
	{
		if (!isset($in->teacher_id)) {
			return ['status'=>0,'message'=>"Dados incompletos"];
		}

		$teacher = User::where('_id', Crypt::decrypt($in->teacher_id))->whereIn('type', ['P', 'M'])->first();
		if (!$teacher){
			return ['status'=>0,'message'=>"Professor não encontrado"];
		}

		$relationship = Relationship::where('user_id', auth()->id())->where('teacher_id', $teacher->id)->where('status', 'E')->first();
		$teacher->register = $relationship->register;
		switch ($teacher->formation) {
			case '0':$teacher->formation = "Não quero informar";
				break;
			case '1':$teacher->formation = "Ensino Fundamental";
				break;
			case '2':$teacher->formation = "Ensino Médio";
				break;
			case '3':$teacher->formation = "Ensino Superior Incompleto";
				break;
			case '4':$teacher->formation = "Ensino Superior Completo";
				break;
			case '5':$teacher->formation = "Pós-Graduado";
				break;
			case '6':$teacher->formation = "Mestre";
				break;
			case '7':$teacher->formation = "Doutor";
				break;
		}

		unset($teacher->created_at);
		unset($teacher->updated_at);
		unset($teacher->password);

		return ["status"=>1, "teacher" => $teacher];
	}

	public function searchByEmail(Request $in)
	{
		if (!isset($in->email)) {
			return ['status' => 0, 'message' => 'Dados incompletos'];
		}

		$teacher = User::where('email', trimpp(strtolower($in->email)))->first();
		if (!$teacher) {
			return ['status' => 0, 'message' => 'Professor não encontrado'];
		}

		if (!Relationship::where('institution_id', auth()->id())->where('teacher_id', $teacher->id)->count()){
			return ['status' => 0, 'message' => 'Professor não vinculado à instituição'];
		}

		return ['status' => 1, 'teacher' => $teacher];
	}

	//Vincula um professor a uma instituição
	public function link(Request $in)
	{
		if (!isset($in->teacher_id) || !isset($in->register)) {
			return ['status' => 0, 'message' => 'Dados incompletos'];
		}

		$teacher = User::find(Crypt::decrypt($in->teacher_id));
		if (!$teacher) {
			return ['status' => 0, 'message' => "Professor não encontrado"];
		}

		$relationship = auth()->user()->relationships()->where('teacher_id', $teacher->id)->first();
		if ($relationship) {
			return ['status' => 0, 'message' => 'Professor já vinculado à instituição'];
		}

		$relationship = new Relationship;
		$relationship->institution_id = auth()->id();
		$relationship->teacher_id = $teacher->id;
		$relationship->register = $in->register;
		$relationship->status = "E";
		$relationship->save();

		return ['status' => 1];
	}

	//Desfaz vínculo de professor com uma instituição
	public function unlink(Request $in)
	{
		if (!isset($in->teacher_id)) {
			return ['status' => 0, 'message' => 'Dados incompletos'];
		}

		$teacher = User::find(Crypt::decrypt($in->teacher_id));
		if (!$teacher){
			return ['status' => 0, 'message' => 'Professor não encontrado'];
		}

		if (!Relationship::where('institution_id', auth()->id())->where('teacher_id, '$teacher->id)->where('status', 'E')->count()){
			return ['status' => 0, 'message' => 'Professor não está vinculado à instituição'];
		}

		$offers_ids = $teacher->lectures()->get(['offer_id'])->pluck('offer_id');
		$classes_ids = Offer::whereIn('_id', $offers_ids)->get(['class_id'])->pluck('class_id')->unique();
		$periods_ids = Classe::whereIn('_id', $classes_ids)->get(['period_id'])->pluck('period_id')->unique();
		$courses_ids = Period::whereIn('_id', $periods_ids)->get(['course_id'])->pluck('course_id')->unique();
		$institutions_ids = Course::whereIn('_id', $courses_ids)->get(['institution_id'])->pluck('institution_id')->unique();
		if (in_array(auth()->id(), $institutions_ids)){
			return ['status'=>0, 'message'=>'Professor não pode ser removido. Já está vinculado a ofertas'];
		}

		Relationship::where('institution_id', auth()->id())->where('teacher_id', $in->teacher_id)->update(["status" => "D"]);

		return ['status'=>1];
	}

	public function invite(Request $in)
	{
		if (isset($in->guest_id)) {
			$guest = User::find($in->guest_id);
		} else if (isset($in->teacher_id)) {
			$guest = User::find(Crypt::decrypt($in->teacher_id));
		}
		else {
			return ['status' => 0, 'message' => "Dados incompletos"];
		}

		if (($guest->type == "M" && Relationship::where('institution_id', auth()->id())->where('teacher_id', $guest->id)->first()) || ($guest->type == "N" && Study::where('institution_id', auth()->id())->where('study_id', $guest->id)->first())) {
			if (User::whereEmail($in->email)->first()) {
				return ['status' => 0, 'message' => "O email " . $guest->email . " já está cadastrado."];
			}
			//try
			//{
				$password = substr(md5(microtime()), 1, rand(4, 7));
				$guest->password = bcrypt($password);
				Mail::send('email.invite', [
					"institution" => auth()->user()->name,
					"name" => $guest->name,
					"email" => $guest->email,
					"password" => $password,
				], function ($message) use ($guest) {
					$message->to($guest->email, $guest->name)
						->subject("Seja bem-vindo");
				});
				$guest->save();
				return ['status'=>1];
			/*} catch (Exception $e) {
				return Redirect::back()->with("error", "Erro ao realizar a operação, tente mais tarde (" . $e->getMessage() . ")");
			}*/
		} else {
			return ['status' => 0, 'message' => "Operação inválida"];
		}
	}

}
