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
use App\MongoDb\Work;
use App\MongoDb\Study;
use App\MongoDb\DescriptiveExam;
use Crypt;
use StdClass;
use Session;
use Illuminate\Http\Request;

class TeacherController extends Controller
{

	public function list(Request $in)
	{
		if (auth()->user()) {
			$block = 30;
			if (!$in->has("search")){
				$in->search = "";
			}
			$current = (int) $in->has("current") ? $in->current : 0;
			$user = User::find(auth()->user()->id);
			$courses = Course::where('institution_id', auth()->user()->id)
				->whereStatus("E")
				->orderBy("name")
				->get();
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

				if (isset($in->search)) {
					$teachers_ids = auth()->user()->works()->get(['teacher_id'])->pluck('teacher_id');
					$teachers = Users::whereIn('_id', $teachers_ids)->where('name','regexp',"/$in->search/i");
					$length = clone $teachers;
					$length = $length->count();
					$teachers = $teachers->skip($current * $block)->take($block)->get(['_id', 'name', 'type']);
				}
				else if (isset($in->register)) {
					$teachers_ids = auth()->user()->works()->where('register',$in->register)->get(['teacher_id'])->pluck('teacher_id');
					$teachers = Users::whereIn('_id', $teachers_ids);
					$length = clone $teachers;
					$length = $length->count();
					$teachers = $teachers->skip($current * $block)->take($block)->get(['_id', 'name', 'type']);
				}
				else {
					$teachers_ids = auth()->user()->works()->get(['teacher_id'])->pluck('teacher_id');
					$teachers = Users::whereIn('_id', $teachers_ids);
					$length = clone $teachers;
					$length = $length->count();
					$teachers = $teachers->skip($current * $block)->take($block)->get(['_id', 'name', 'type']);
				}
				foreach ($teachers as $teacher) {
					$teacher->comment = $teacher->works()->where('status','E')->where('institution_id',auth()->id())->first(['enrollment'])->enrollment;
					//$teacher->selected = Lecture::where('user_id', $teacher->id)->where('offer_id', $offer)->count();
					$teacher->id = Crypt::encrypt($teacher->id);
				}

			/*$length = DB::select("SELECT count(*) as 'length' "
				. "FROM Users, Relationships "
				. "WHERE Relationships.idUser=? AND Relationships.type='2' AND Relationships.idFriend=Users.id "
				. "AND (Users.name LIKE ? OR Relationships.enrollment=?) ", [auth()->user()->id, "%$search%", $search]);*/

			return
				[
					"status" => 1,
					"courses" => $courses,
					"user" => $user,
					"teachers" => $teachers,
					"length" => (int) $length,
					"block" => (int) $block,
					"current" => (int) $current,
				];

		} else {
			return ['status'=>0,'message'=>'Usuario não logado.'];
		}
	}

	public function read(Request $in)
	{
		$user = User::find(auth()->id());
		if (!isset($in->teacher_id)) {
			return ['status'=>0,'message'=>"Dados incompletos"];
		}
		$in->teacher_id = Crypt::decrypt($in->teacher_id);
		$teacher = User::find($in->teacher_id);
		$work = Work::where('user_id', auth()->id())->where('teacher_id', $teacher->id)->first();
		$teacher->register = $work->register;
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

	public function search(Request $in) {
		if (!isset($in->email)) {
			return ['status' => 0, 'message' => 'Dados incompletos'];
		}

		$teacher = User::where('email', $in->email)->first(['_id','name','formation']);
		if ($teacher) {
			$relationship = Work::where('institution_id', auth()->id())->where('teacher_id', $teacher->id)->first();
			if (!$relationship) {
				return [
					'status' => 1,
					'teacher' => [
						'id' => Crypt::encrypt($teacher->id),
						'name' => $teacher->name,
						'formation' => $teacher->formation,
					],
					'message' => 'Este professor já está cadastrado no LibreClass e será vinculado à sua instituição.',
				];
			} else {
				return ([
					'status' => -1,
					'teacher' => [
						'id' => Crypt::encrypt($teacher->id),
						'name' => $teacher->name,
						'formation' => $teacher->formation,
						'enrollment' => $relationship->enrollment,
					],
					'message' => 'Este professor já está vinculado à instituição!',
				]);
			}
		} else {
			return ([
				'status' => 0,
			]);
		}
	}

	public function unlink(Request $in) {
		if (!isset($in->teacher_id)) {
			return ['status' => 0, 'message' => 'Dados incompletos'];
		}
		$in->teacher_id = Crypt::decrypt($in->teacher_id);

		/*$offers = DB::select("SELECT Courses.name AS course, Periods.name AS period, Classes.class as class, Disciplines.name AS discipline "
			. "FROM Courses, Periods, Classes, Offers, Lectures, Disciplines "
			. "WHERE Courses.idInstitution=? AND Courses.id=Periods.idCourse AND "
			. "Periods.id=Classes.idPeriod AND Classes.id=Offers.idClass AND "
			. "Offers.idDiscipline=Disciplines.id AND "
			. "Offers.id=Lectures.idOffer AND Lectures.idUser=?", [auth()->user()->id, $idTeacher]);*/

		$courses_ids = auth()->user()->courses()->get(['_id'])->pluck('_id');
		$periods_ids = Period::whereIn('course_id',$courses_ids)->get(['_id'])->pluck('_id');
		$classes_ids = Classe::whereIn('period_id',$periods_ids)->get(['_id'])->pluck('_id');
		$offers_ids = Offer::whereIn('classe_id',$classes_ids)->get(['_id'])->pluck('_id');
		$disciplines_ids = Discipline::whereIn('offer_id',$offers_ids)->get(['_id'])->pluck('_id');
		$lectures = Lecture::whereIn('discipline_id',$disciplines_ids)->where('teacher_id',$in->teacher_id)->count();

		if ($lectures) {
			/*$str = "Erro ao desvincular professor, ele está associado a(s) disciplina(s): <br><br>";
			$str .= "<ul class='text-justify text-sm list-group'>";
			foreach ($offers as $offer) {
				$str .= "<li class='list-group-item'>$offer->course/$offer->period/$offer->class/$offer->discipline</li>";
			}
			$str .= "</ul>";*/

			return ['status'=>0,'message'=>'Professor não pode ser removido pois está associado à disciplinas.'];
		} else {
			Work::where('institution_id', auth()->id())
				->where('teacher_id', $in->teacher_id)
				->update(["status" => "D"]);

			return ['status'=>1];
		}
	}

	public function save(Request $in) {
		if (isset($in->teacher_id)) {
			$teacher = User::find(Crypt::decrypt($in->teacher_id));

			if (!$teacher) {
				return ['status' => 0, 'message' => "Professor não encontrado!"];
			}

			// Tipo P é professor com conta liberada. Ele mesmo deve atualizar as suas informações e não a instituição.
			if ($teacher->type == "P") {
				return ['status' => 0, 'message' => "Professor não pode ser editado!"];
			}
			$teacher->email = $in->email;
			// $user->enrollment = Input::get("enrollment");
			$teacher->name = $in->name;
			$teacher->formation = $in->formation;
			$teacher->gender = $in->gender;
			$teacher->save();

			unset($teacher->created_at);
			unset($teacher->updated_at);
			unset($teacher->password);

			return ['status' => 1, 'teacher' => $teacher];
		} else {
			$verify = Work::where('register',$in->register)->where('user_id', auth()->id())->first();
			if (isset($verify) || $verify != null) {
				return ['status' => 0, 'message' => "Este número de inscrição já está cadastrado!"];
			}
			if (User::where('email', $in->email)->count()) {
				return ['status' => 0, 'message' => "Este email já está cadastrado!"];
			}
			$teacher = new User;
			$teacher->type = "M";
			// $user->email = Input::get("email");
			// $user->enrollment = Input::get("enrollment");
			$teacher->name = $in->name;
			$teacher->email = $in->email;
			$teacher->formation = $in->formation;
			$teacher->gender = $in->gender;
			$teacher->password = Crypt::encrypt('12345');
			if ($in->has("year")) {
				$teacher->birthdate = $in->year . "-"
				. $in->month . "-"
				. $in->day;
			}
			$teacher->save();

			$work = new Work;
			$work->institution_id = auth()->id();
			$work->teacher_id = $teacher->id;
			$work->register = $in->register;
			$work->status = "E";
			$work->save();

			$request = new Request;
			$request->teacher_id = $teacher->id;

			//$this->invite($request);

			unset($teacher->created_at);
			unset($teacher->updated_at);
			unset($teacher->password);

			return ['status' => 1, 'teacher' => $teacher];
		}
	}

	public function vinculateTeacher(Request $in)
	{
		if (!isset($in->teacher_id)) {
			return ['status' => 0, 'message' => 'Dados incompletos'];
		}
		$teacher = User::find(Crypt::decrypt($in->teacher_id));

		if (!$teacher) {
			return ['status' => 0, 'message' => "Professor não encontrado!"];
		}
		$work = auth()->user()->works()->where('teacher_id', $teacher->id)->first();
		if (!$work) {
			$work = new Work;
			$work->institution_id = auth()->id();
			$work->teacher_id = $teacher->id;
			$work->register = $in->register;
			$work->status = "E";
			$work->save();
		}
		return ['status' => 1];
	}

	public function invite(Request $in)
	{
		$user = User::find(auth()->id());
		if (isset($in->guest_id)) {
			$guest = User::find($in->guest_id);
		} else if (isset($in->guest_id)) {
			$guest = User::find(Crypt::decrypt($in->teacher_id));
		}
		else {
			return ['status' => 0, 'message' => "Dados incompletos"];
		}

		if (($guest->type == "M" && Work::where('institution_id', auth()->id())->where('teacher_id', $guest->id)->first()) || ($guest->type == "N" && Study::where('institution_id', auth()->id())->where('study_id', $guest->id)->first())) {
			if (User::whereEmail($in->email)->first()) {
				return ['status' => 0, 'message' => "O email " . Input::get("email") . " já está cadastrado."];
			}
			//try
			//{
				$guest->email = $in->email;
				$password = substr(md5(microtime()), 1, rand(4, 7));
				$guest->password = Hash::make($password);
				Mail::send('email.invite', [
					"institution" => auth()->user()->name,
					"name" => $guest->name,
					"email" => $guest->email,
					"password" => $password,
				], function ($message) use ($guest) {
					$message->to(Input::get("email"), $guest->name)
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
