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

class UsersController extends Controller
{

	public function __construct()
	{
		$id = Session::get("user");
		if ($id == null || $id == "") {
			$this->idUser = false;
		} else {
			$this->idUser = Crypt::decrypt($id);
		}
	}

	//public function postSearchTeacher()
	public function searchTeacher(Request $in)
	{
		$teacher = User::where('email', $in->email))->first();
		\Log::info('post search teacher', [$teacher]);
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

	//public function anyTeachersFriends()
	public function teachersWorking()
	{
		/*$teachers = DB::select("SELECT Users.id, Users.name, Users.photo, Relationships.enrollment as 'comment'"
			. "FROM Users, Relationships "
			. "WHERE Relationships.idUser=? AND Relationships.type='2' "
			. "AND Relationships.idFriend=Users.id "
			. "AND Relationships.status='E'"
			. " ORDER BY name",
			[auth()->user()->id]);*/
		$teachers = User::where('user_id',auth()->id())->orderBy('name')->get(['id','name','photo']);
		$teachers_ids = auth()->user()->works()->get(['teacher_id'])->pluck('teacher_id');
		$teachers = Users::whereIn('_id', $teachers_ids)->get(['_id', 'name', 'photo']);
		foreach ($teachers as $teacher) {
			$teacher->comment = $teacher->works()->where('status','E')->where('institution_id',auth()->id())->first(['enrollment'])->enrollment;
			//$teacher->selected = Lecture::where('user_id', $teacher->id)->where('offer_id', $offer)->count();
			$teacher->id = Crypt::encrypt($teacher->id);
		}

		return ['status'=>1, 'teachers'=>$teachers];
	}

	public function getTeacher(Request $in)
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
				else {
					$teachers_ids = auth()->user()->works()->get(['teacher_id'])->where('register',$in->register)->pluck('teacher_id');
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
				]
			);

		} else {
			return ['status'=>0,'message'=>'Usuario não logado.'];
		}
	}

	/*public function postTeacher()*/
	public function addTeachers(Request $in)
	{
		// Verifica se o número de matrícula já existe

		if (isset($in->teacher)) {
			$user = User::find(Crypt::decrypt($in->teacher));
			if (isset($in->registered)) {
				$work = auth()->user()->works()->where('teacher_id', $user->id)->first();
				if (!$work) {
					$work = new Work;
					$work->institution_id = auth()->id();
					$work->teacher_id = $user->id;
					$work->register = $in->register;
					$work->status = "E";
					$work->save();
				}
				return Redirect::guest("/user/teacher")->with("success", "Professor vinculado com sucesso!");
			}

			// Tipo P é professor com conta liberada. Ele mesmo deve atualizar as suas informações e não a instituição.
			if ($user->type == "P") {
				return Redirect::guest("/user/teacher")->with("error", "Professor não pode ser editado!");
			}
			$user->email = $in->email;
			// $user->enrollment = Input::get("enrollment");
			$user->name = $in->name;
			$user->formation = $in->formation;
			$user->gender = $in->gender;
			$user->save();
			return Redirect::guest("/user/teacher")->with("success", "Professor editado com sucesso!");
		} else {
			$verify = Work::where('register',$in->enrollment)->where('user_id', auth()->id())->first();
			if (isset($verify) || $verify != null) {
				return Redirect::guest("/user/teacher")->with("error", "Este número de inscrição já está cadastrado!");
			}
			$user = new User;
			$user->type = "M";
			// $user->email = Input::get("email");
			// $user->enrollment = Input::get("enrollment");
			$user->name = $in->name;
			$user->formation = $in->formation;
			$user->gender = $in->gender;
			if ($in->has("year")) {
				$user->birthdate = $in->year . "-"
				. $in->month . "-"
				. $in->day;
			}
			$user->save();

			$work = new Work;
			$work->institution_id = auth()->id();
			$work->teacher_id = $user->id;
			$work->enrollment = $in->enrollment;
			$work->status = "E";
			$work->save();

			$this->postInvite($user->id);

			return Redirect::guest("/user/teacher")->with("success", "Professor cadastrado com sucesso!");
		}
	}

	public function updateRegister(Request $in)
	{
		$user = User::find(Crypt::decrypt($in->teacher));
		Work::where('institution_id', auth()->id())->where('teacher_id', $user->id)->update(['register' => $in->register]);
		return ['status' => 1];
	}

	//public function getProfileStudent()
	public function profileStudent(Request $in)
	{
		$in->student_id = Crypt::decrypt($in->student_id);
		/*$classes = DB::select("SELECT Classes.id, Classes.name, Classes.class FROM Classes, Periods, Courses "
			. "WHERE Courses.idInstitution=? AND Courses.id=Periods.idCourse AND Periods.id=Classes.idPeriod AND Classes.status='E'",
			[$user->id]);*/
		$courses_ids = auth()->user()->courses()->get(['_id'])->pluck('_id');
		$periods_ids = Period::whereIn('course_id',$courses_ids)->get(['_ids'])->pluck('_id');
		$classes = Classe::whereIn('period_id',$periods_ids)->get(['_id','name','class']);
		/*$listclasses = [];
		$listidsclasses = [];
		foreach ($classes as $class) {
			$listclasses[$class->class] = $class->class;
			$listidsclasses[Crypt::encrypt($class->id)] = "[$class->class] $class->name";

		}*/
		if ($profile) {
			//$student = User::where('_id',$in->student_id);
			$attests = Attest::where('student_id', $in->student_id)->where('institution_id', $user->id)->orderBy("date", "desc")->get();
			return view("modules.profilestudent", ["user" => $user, "profile" => $profile, "listclasses" => $listclasses, "attests" => $attests, "classes" => $classes]);
		} else {
			return Redirect::guest("/");
		}
	}

	//public function anyReporterStudentClass()
	public function reporterStudentClass(Request $in)
	{
		if (!isset($in->student_id)) {
			return ['status'=>0,'message'=>"Dados incompletos"];
		}
		if (!isset($in->class)) {
			return ['status'=>0,'message'=>"Dados incompletos"];
		}
		$in->student_id = Crypt::decrypt($in->student_id);
		$disciplines = DB::select("SELECT	Courses.id as course, Disciplines.name, Offers.id as offer, Attends.id as attend, Classes.status as statusclasse "
			. "FROM Classes, Periods, Courses, Disciplines, Offers, Units, Attends "
			. "WHERE Courses.idInstitution=? AND Courses.id=Periods.idCourse AND Periods.id=Classes.idPeriod AND Classes.class=? AND Classes.id=Offers.idClass AND Offers.idDiscipline=Disciplines.id AND Offers.id=Units.idOffer AND Units.id=Attends.idUnit AND Attends.idUser=? "
			. "group by Offers.id",
			[auth()->user()->id, Input::get("class"), $student]);
		/*$courses_ids = auth()->user()->courses()->pluck('_id');
		$periods_ids = Period::whereIn('course_id',$courses_ids)->get(['_ids'])->pluck('_id');
		$classes_ids = Classe::whereIn('period_id',$periods_ids)->where('class',$in->class)->get(['_id'])->pluck('_id');
		$offers_ids = Offer::whereIn('classe_id',$classes_ids)->get(['_id'])->unique()->pluck('_id');
		$units_ids = Unit::whereIn('offer_id',$offers_ids)->get(['_id'])->pluck('_id');
		$student = User::where('_id',$in->student_id)->attends();*/

		foreach ($disciplines as $discipline) {
			$sum = 0;
			$discipline->units = Unit::where('offer_id', $discipline->offer)->get();
			foreach ($discipline->units as $unit) {
				$unit->exams = Exam::where('unit_id', $unit->id)->orderBy("aval")->get();
				foreach ($unit->exams as $exam) {
					$exam->value = ExamsValue::where('exam_id', $exam->id)->where('attend_id', $discipline->attend)->first();
				}

				$value = $unit->getAverage($student);
				// return $value;
				$sum += isset($value[1]) ? $value[1] : $value[0];
			}
			$discipline->average = sprintf("%.2f", ($sum + .0) / count($discipline->units));
			$discipline->final = FinalExam::where('user_id', $student)->where('offer_id', $discipline->offer)->first();
			$offer = Offer::find($discipline->offer);
			$discipline->absencese = sprintf("%.1f", (100. * ($offer->maxlessons - $offer->qtdAbsences($student))) / $offer->maxlessons);

			$course = Course::find($discipline->course);
			$discipline->course = $course;
			$discipline->aproved = "-";
			if ($discipline->statusclasse == "C") {
				$discipline->aproved = "Aprovado";
				if ($discipline->absencese + $course->absentPercent < 100) {
					$discipline->aproved = "Reprovado";
				}

				if ($discipline->average < $course->average and (!$discipline->final or $discipline->final->value < $course->averageFinal)) {
					$discipline->aproved = "Reprovado";
				}

			}
		}
		return view("institution.reportStudentDetail", ["disciplines" => $disciplines]);
	}

	public function getReporterStudentOffer()
	{
		return Input::all();
	}

	//public function postProfileStudent()
	public function profileStudent(Request $in)
	{
		//try {
			if (!isset($in->student_id) || !isset($in->offers_ids)) {
				return ['status'=>0,'message'=>"Dados incompletos"];
			}
			if (!is_array($in->offers_ids)) {
				return ['status'=>0,'message'=>"Dados no formato incorreto"];
			}
			$in->student_id = Crypt::decrypt($in->student_id);

			foreach ($in->offers_ids as $offer_id) {
				$units = Unit::where('offer_id', Crypt::decrypt($offer_id))->get();
				$attends = [];
				foreach ($units as $unit) {
					$attend = Attend::where('user_id', $in->student_id)->where('unit_id', $unit->id)->first();
					if ($attend) {
						$disc = Offer::find(Crypt::decrypt($offer))->getDiscipline();
						//$status = ["E" => "Cursando", "D" => "Disabilitado"];
						return ['status'=>0,"message" => "O aluno não pode ser inserido. O aluno já está matriculado na oferta da disciplina " . $disc->name]; //. " com o status " . $attend->status . ".");
					}
					$attend = new Attend;
					$attend->user_id = $in->student_id;
					$attend->unit_id = $unit->id;
					$attends[] = $attend;
				}
				foreach ($attends as $attend) {
					$attend->save();
					$exams = Exam::where('unit_id', $attend->unit_id)->get();
					foreach ($exams as $exam) {
						$value = new ExamsValue;
						$value->exam_id = $exam->id;
						$value->attend_id = $attend->id;
						$value->save();
					}
					$lessons = Lesson::where('unit_id', $attend->unit_id)->get();
					foreach ($lessons as $lesson) {
						$value = new Frequency;
						$value->lesson_id = $lesson->id;
						$value->attend_id = $attend->id;
						$value->save();
					}
				}
			}

			/*foreach (Input::get("offers") as $offer) {
				$units = Unit::where('offer_id', Crypt::decrypt($offer))->get();
				foreach ($units as $unit) {
					$attend = new Attend;
					$attend->idUser = $idUser;
					$attend->idUnit = $unit->id;
					$attend->save();
					$exams = Exam::where('unit_id', $unit->id)->get();
					foreach ($exams as $exam) {
						$value = new ExamsValue;
						$value->idExam = $exam->id;
						$value->idAttend = $attend->id;
						$value->save();
					}
					$lessons = Lesson::where('unit_id', $unit->id)->get();
					foreach ($lessons as $lesson) {
						$value = new Frequency;
						$value->idLesson = $lesson->id;
						$value->idAttend = $attend->id;
						$value->save();
					}
				}
			}*/
			return ['status'=>1];
		/*} catch (Exception $ex) {
			return Redirect::back()->with("error", "Ocorreu algum erro inesperado.<br>Informe o suporte.");
		}*/
	}

	/**
	 * Cadastra um atestada e retorna para a página anterior
	 */
	//public function postAttest()
	public function addAttest(Request $in)
	{
		if (!isset($in->student_id) || !isset($in->year) || !isset($in->month) || !isset($in->day) || !isset($in->days)) {
			return ['status'=>0,'message'=>"Dados incompletos"];
		}
		$student_id = Crypt::decrypt($in->student_id));
		$relation = Study::where('user_id', auth()->id())->where('student_id', $in->student_id)->whereStatus("E")->first();

		if ($relation) {
			$attest = new Attest;
			$attest->institution_id = auth()->id();
			$attest->student_id = $in->student_id;
			$attest->date = $in->year . "-" . $in->month . "-" . $in->day;
			$attest->days = $in->days;
			$attest->description = $in->description;
			$attest->save();

			return ['status' => 1];
		} else {
			return ['status'=>0,'message'=>"Essa operação não pode ser realizado. Consulte o suporte."];
		}

	}

	public function profileTeacher()
	{
		$user = User::find(auth()->id());
		if (!isset($in->teacher_id)) {
			return ['status'=>0,'message'=>"Dados incompletos"];
		}
		$teacher_id = Crypt::decrypt($in->teacher_id);
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
		return ["status"=>1 ,"user" => $user, "profile" => $profile];
	}

	//public function postInvite($id = null)
	public function invite(Request $in)
	{
		$user = User::find(auth()->id());
		if (isset($in->id)) {
			$guest = User::find($in->id);
		} else {
			$guest = User::find(Crypt::decrypt($in->has("teacher") ? $in->teacher : $in->guest));
		}

		if (($guest->type == "M" && Work::where('institution_id', auth()->id())->where('teacher_id', $guest->id)->first()) || ($guest->type == "N" && Study::where('institution_id', auth()->id())->where('study_id', $guest->id)->first())) {
			if (User::whereEmail($in->email)->first()) {
				return ['status' => 0, 'message' => "O email " . Input::get("email") . " já está cadastrado."];
			}
			//try
			//{
				$guest->email = $in->email);
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
			return ['status' => 1, 'message' => "Operação inválida")];
		}
	}

	//public function getStudent()
	public function getStudent()
	{
		if (auth()->user()) {
			$block = 30;
			$in->search = $in->has("search") ? $in->search : "";
			$in->current = (int) $in->has("current") ? $in->current : 0;
			$user = User::find(auth()->id());
			$courses = Course::where('institution_id', auth()->id())
				->whereStatus("E")
				->orderBy("name")
				->get();

			/*$listCourses = ["" => ""];
			foreach ($courses as $course) {
				$listCourses[$course->name] = $course->name;
			}*/

			$relationships = DB::select("SELECT Users.id, Users.name, Users.enrollment "
				. "FROM Users, Relationships "
				. "WHERE Relationships.idUser=? AND Relationships.type='1' AND Relationships.idFriend=Users.id "
				. "AND (Users.name LIKE ? OR Users.enrollment=?) "
				. " ORDER BY name LIMIT ? OFFSET ?",
				[auth()->user()->id, "%$search%", $search, $block, $current * $block]);

			if ($in->search) {
				$works = auth()->user()->works()->where('name','regexp',"/$in->search/i")->skip($in->current * $block)->take($block)->orderBy('name')->get(['_id','name','register']);
				$length = auth()->user()->works()->where('name','regexp',"/$in->search/i")->count();
			}
			else {
				$works = auth()->user()->works()->where('register',$in->register)->skip($in->current * $block)->take($block)->orderBy('name')->get(['_id','name','register']);
				$length = auth()->user()->works()->where('register',$in->register)->count();
			}

			/*$length = DB::select("SELECT count(*) as 'length' "
				. "FROM Users, Relationships "
				. "WHERE Relationships.idUser=? AND Relationships.type='1' AND Relationships.idFriend=Users.id "
				. "AND (Users.name LIKE ? OR Users.enrollment=?) ", [auth()->user()->id, "%$search%", $search]);*/


			return
				[
					'status' => 0,
					"courses" => $listCourses,
					"user" => $user,
					"relationships" => $relationships,
					"length" => (int) $length[0]->length,
					"block" => (int) $block,
					"current" => (int) $current,
				];
		} else {
			return ['status'=>0,'message'=>"Usuario não logado."];
		}
	}

	//public function anyFindUser($search)
	public function findUser(Request $in)
	{
		if (!isset($in->search)) {
			return ['status'=>0, 'message'=>"Dados incompletos"];
		}
		$users = User::where("name", "regexp", "/" . $search . "/i")->orWhere("email", $search)->get();
		return	['status'=>1, "users" => $users, "i" => 0]);
	}

	//public function postStudent()
	public function addStudent()
	{
		$user = new User;
		$user->enrollment = $in->enrollment;
		$user->name = $in->name;
		$user->email = $in->has("email") ? $in->email : null;
		$user->course = $in->course;
		$user->birthdate = $in->year . "-" . $in->month . "-" . $in->day;
		$user->type = "N";
		$user->save();

		$relationship = new Study;
		$relationship->institution_id = auth()->id();
		$relationship->student_id = $user->id;
		$relationship->status = "E";
		$relationship->save();

		return ['status' => 1,'user'=>$user];
	}

	//public function postUnlink()
	public function unlink(Request $in)
	{
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

	//public function getInfouser()
	public function infoUser(Request $in)
	{
		if(!isset($in->user_id)) {
			return ['status'=>0, 'message'=>'Dados incompletos.']
		}
		$user = User::find(Crypt::decrypt($in->user_id));
		if ($user) {
			$user->register = Work::where('user_id', auth()->user()->id)->where('teacher_id', $user->id)->pluck('register');
			unset($user->password);
			return ['status'=>1,'user'=>$user];
		}
		return ['status'=>0,'message'=>"Usuário não encontrado"];
	}

	//public function anyLink($type, $user)
	public function link(Request $in)
	{
		if (!isset($in->type) || !isset($in->user_id)) {
			return ['status'=>0,'message'=>'Dados incompletos.']
		}
		switch ($in->type) {
			case "student":
				$type = 1;
				break;
			default:
				return Redirect::back()->with("error", "Cadastro errado.");
		}
		$user = Crypt::decrypt($user);

		//$r = Relationship::where('user_id', auth()->user()->id)->where('friend_id', $user)->whereType($type)->first();
		$r = Study::where('institution_id', auth()->id())->where('student_id', $in->user_id)->first();
		if ($r and $r->status == "E") {
			return ['status'=>0, 'message'=>"Usuário já possui esse relacionamento."];
		} elseif ($r) {
			$r->status = "E";
		} else {
			$r = new Relationship;
			$r->idUser = auth()->user()->id;
			$r->idFriend = $user;
			$r->type = $type;
		}
		$r->save();

		//return Redirect::back()->with("success", "Relacionamento criado com sucesso.");
		return ['status' => 1,'message'=>"Relacionamento criado com sucesso."]
	}

	public function printScholarReport(Request $in)
	{
		$data = [];

		// Obtém dados da instituição
		$data['institution'] = User::find(auth()->id());

		// Obtém dados do aluno
		$data['student'] = User::find(Crypt::decrypt($in->student_id));

		// Obtém número de matrícula do aluno na instituição
		$e = Study::where('user_id', auth()->user()->id)->where('student_id', $data['student']->id)->first();
		$data['student']['enrollment'] = $e['enrollment'];

		/*$disciplines = DB::select("
			SELECT
				Courses.id as course,
				Disciplines.name,
				Offers.id as offer,
				Attends.id as attend,
				Classes.status as statusclasse
			FROM
				Classes, Periods, Courses, Disciplines, Offers, Units, Attends
			WHERE
				Courses.idInstitution =	?
				and Courses.id = Periods.idCourse
				and Periods.id = Classes.idPeriod
				and Classes.class =	?
				and Classes.id = Offers.idClass
				and Offers.idDiscipline = Disciplines.id
				and Offers.id = Units.idOffer
				and Units.id = Attends.idUnit and Attends.idUser =	?
			GROUP BY Offers.id",
			[auth()->user()->id, Input::get('c'), $data['student']->id]
		);*/

		$courses_ids = auth()->user()->courses()->get(['_id'])->pluck('_id');
		$periods_ids = Period::whereIn('course_id',$courses_ids)->get(['_id'])->pluck('_id');
		$classes_ids = Classe::where('class',$in->class)->whereIn('period_id',$periods_ids)->get(['_id'])->pluck('_id');
		//$offers_ids = Offer::whereIn('classe_id',$classes_ids)->get(['_id'])->pluck('_id');
		$units_ids = Attend::whereIn('user_id',$data['student']->id)->get(['unit_id'])->pluck('unit_id');
		$offers_ids = Unit::whereIn('_id',$units_ids)->get(['offer_id'])->pluck('offer_id');
		$disciplines = Offer::whereIn('_id',$offers_ids)->whereIn('classe_id',$classes_ids)->disciplines;

		// $offer = Offer::find($disciplines[0]->offer);
		// dd($offer->qtdLessons());

		if (!$disciplines) {
			return "Aluno não possui disciplinas.";
		}

		//Variável para acumular os pareceres
		$pareceres = new StdClass;
		$pareceres->disciplines = [];
		foreach ($disciplines as $key => $discipline) {

			// Obtém informações da disciplinas
			$data['disciplines'][$key] = (array) $discipline;

			$pareceres->disciplines[] = $discipline;
			$pareceres->disciplines[$key]->units = [];
			$pareceres->disciplines[$key]->hasParecer = false;

			// Obtém unidades
			$units = Offer::find($data['disciplines'][$key]['offer'])->units()->orderBy('created_at')->get();

			foreach ($units as $key2 => $unit) {
				var_dump($key2, $key);
				var_dump('<br />');
				var_dump('<br />');
				$pareceres->disciplines[$key]->units[] = $unit;

				// Obtém quantidade de aulas realizadas
				$data['disciplines'][$key][$unit->value]['lessons'] = Offer::find($unit->offer_id)->qtdUnitLessons($unit->value);

				// Obtém quantidade de faltas
				$data['disciplines'][$key][$unit->value]['absenceses'] = Offer::find($unit->offer_id)->qtdUnitAbsences($data['student']['id'], $unit->value);

				// Obtém a média do alunos por disciplina por unidade
				$average = number_format($unit->getAverage($data['student']['id'])[0], 0);

				if ($unit->calculation != 'P') {
					$data['disciplines'][$key][$unit->value]['average'] = ($average > 10) ? number_format($average, 0) : number_format($average, 2);
				} else {
					$pareceres->disciplines[$key]->units[$key2]->pareceres = [];
					//Obtém os pareceres
					$attend = Attend::where('unit_id', $unit->id)->where('user_id', $data['student']->id)->first();
					$pareceresTmp = DescriptiveExam::where('attend_id', $attend->id)->get();

					foreach ($pareceresTmp as $parecer) {
						$parecer->exam = Exam::where('id', $parecer->exam_id)->first(['title', 'type', 'date']);
						$parecer->exam->type = $this->typesExams($parecer->exam->type);
					}
					if (!empty($pareceresTmp)) {
						$pareceres->disciplines[$key]->hasParecer = true;
					}

					//Guarda os pareceres para enviar para view
					$pareceres->disciplines[$key]->units[$key2]->pareceres = $pareceresTmp;

					$data['disciplines'][$key][$unit->value]['average'] = '<small>Parecer<br>descritivo</small>';
				}

				$examRecovery = $unit->getRecovery();

				// Verifica se há prova de recuperação
				if ($examRecovery) {
					$attend = Attend::where('unit_id', $unit->id)->where('user_id', $data['student']['id'])->first();
					$recovery = ExamsValue::where('attend_id', $attend->id)->where('exam_id', $examRecovery->id)->first();
					$data['disciplines'][$key][$unit->value]['recovery'] = isset($recovery) && $recovery->value ? $recovery->value : '--';
				}
			}
		}
		//Guarda pareceres
		$data['pareceres'] = $pareceres;

		// Obtém dados do curso
		$data['course'] = Course::find($disciplines[0]->course);

		// Obtém dados da turma
		$data['classe'] = Offer::find($disciplines[0]->offer)->classe;

		$pdf = PDF::loadView('reports.student-bulletin', ['data' => $data]);
		return $pdf->stream();
		// return $pareceres->disciplines;
	}

	public function typesExams($type)
	{
		$typesExams = [
			"Prova Dissertativa Individual",
			"Prova Dissertativa em Grupo",
			"Prova Objetiva Individual",
			"Prova Objetiva em Grupo",
			"Trabalho Dissertativo Individual",
			"Trabalho Dissertativo em Grupo",
			"Apresentação de Seminário",
			"Projeto",
			"Produção Visual",
			"Pesquisa de Campo",
			"Texto Dissertativo",
			"Avaliação Prática",
			"Outros",
		];
		return $typesExams[$type];
	}
}
