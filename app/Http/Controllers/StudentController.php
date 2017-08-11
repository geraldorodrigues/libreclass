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
use App\MongoDb\Attest;
use App\MongoDb\DescriptiveExam;
use Crypt;
use PDF;
use StdClass;
use Session;
use Illuminate\Http\Request;

class StudentController extends Controller
{
	public function save(Request $in)
	{
		if (isset($in->student_id))) {//Edição
			$student = User::find(Crypt::decrypt($in->student_id));
			if (!$student) {
				return ['status' => 0, 'message' => "Aluno não encontrado"];
			}
		} else {//Criação
			$student = new User;

			$relationship = new Study;
			$relationship->institution_id = auth()->id();
			$relationship->status = "E";
		}
		//$student->enrollment = $in->enrollment;
		$student->name = $in->name;
		$student->email = $in->has("email") ? $in->email : null;
		$student->course = $in->course;
		$student->birthdate = $in->birthdate;
		$student->type = "N";
		$student->save();

		if (isset($relationship)) {
			$relationship->student_id = $student->id;
			$relationship->save();
		}
		unset($student->created_at);
		unset($student->updated_at);
		unset($student->password);
		$student->id = Crypt::encrypt($student->id);

		return ['status' => 1, 'student'=>$student];
	}

	public function list(Request $in)
	{
		$block = 30;
		$in->search = $in->has("search") ? $in->search : "";
		$in->current = (int) $in->has("current") ? $in->current : 0;
		// $user = User::find(auth()->id());
		$courses = Course::where('institution_id', auth()->id())->whereStatus("E")->orderBy("name")->get();

		/*$listCourses = ["" => ""];
		foreach ($courses as $course) {
			$listCourses[$course->name] = $course->name;
		}*/

		/*$relationships = DB::select("SELECT Users.id, Users.name, Users.enrollment "
			. "FROM Users, Relationships "
			. "WHERE Relationships.idUser=? AND Relationships.type='1' AND Relationships.idFriend=Users.id "
			. "AND (Users.name LIKE ? OR Users.enrollment=?) "
			. " ORDER BY name LIMIT ? OFFSET ?",
			[auth()->user()->id, "%$search%", $search, $block, $current * $block]);*/

		if ($in->search) {
			$students_ids = auth()->user()->contains()->get(['student_id'])->pluck('student_id');
			$students = User::whereIn('_id',$students_ids)->where('name','regexp',"/$in->search/i");
			$length = clone $students;
			$length = $length->count();
			$students = $students->skip($in->current * $block)->take($block)->orderBy('name')->get(['_id','name']);
		}
		else if ($in->register) {
			$students_ids = auth()->user()->contains()->get(['student_id'])->pluck('student_id');
			$students = User::whereIn('_id',$students_ids)->where('name','regexp',"/$in->search/i");
			$length = clone $students;
			$length = $length->count();
			$students = $students->skip($in->current * $block)->take($block)->orderBy('name')->get(['_id','name']);
		}
		else {
			$students_ids = auth()->user()->contains()->get(['student_id'])->pluck('student_id');
			$students = User::whereIn('_id',$students_ids);
			$length = clone $students;
			$length = $length->count();
			$students = $students->skip($in->current * $block)->take($block)->orderBy('name')->get(['_id','name']);
		}

		foreach ($students as $student) {
			$student->id = Crypt::encrypt($student->id);
		}

		/*$length = DB::select("SELECT count(*) as 'length' "
			. "FROM Users, Relationships "
			. "WHERE Relationships.idUser=? AND Relationships.type='1' AND Relationships.idFriend=Users.id "
			. "AND (Users.name LIKE ? OR Users.enrollment=?) ", [auth()->user()->id, "%$search%", $search]);*/

		return [
			'status' => 1,
			"students" => $students,
			"length" => (int) $length,
			"block" => (int) $block,
			"current" => (int) $in->current,
		];
	}

	public function read(Request $in)
	{
		if (!isset($in->student_id)) {
			return ['status'=>0,'message'=>"Dados incompletos"];
		}
		$in->student_id = Crypt::decrypt($in->student_id);
		/*$classes = DB::select("SELECT Classes.id, Classes.name, Classes.class FROM Classes, Periods, Courses "
			. "WHERE Courses.idInstitution=? AND Courses.id=Periods.idCourse AND Periods.id=Classes.idPeriod AND Classes.status='E'",
			[$user->id]);*/
		//$courses_ids = auth()->user()->courses()->get(['_id'])->pluck('_id');
		//$periods_ids = Period::whereIn('course_id',$courses_ids)->get(['_ids'])->pluck('_id');
		//$classes = Classe::whereIn('period_id',$periods_ids)->get(['_id','name','class']);
		/*$listclasses = [];
		$listidsclasses = [];
		foreach ($classes as $class) {
			$listclasses[$class->class] = $class->class;
			$listidsclasses[Crypt::encrypt($class->id)] = "[$class->class] $class->name";

		}*/
		$student = User::find($in->student_id);
		$student->id = Crypt::encrypt($student->id);
		unset($student->created_at);
		unset($student->updated_at);
		unset($student->password);
		$student->attests = Attest::where('student_id', $in->student_id)->where('institution_id', auth()->id())->orderBy("date", "desc")->get(['_id'])->pluck('_id');
		return ['status'=> 1, "student" => $student];
	}

	public function addAttest(Request $in)
	{
		if (!isset($in->student_id) || !isset($in->date) || !isset($in->days)) {
			return ['status'=>0,'message'=>"Dados incompletos"];
		}

		$student =

		if (!Relationship::where('institution_id', auth()->id())->where('student_id', Crypt::decrypt($in->student_id))->whereStatus("E")->count()){
			return ['status'=>0,'message'=>"Estudante não está vinculado a esta instituição"];
		}

		$attest = new Attest;
		$attest->institution_id = auth()->id();
		$attest->student_id = $in->student_id;
		$attest->date = $in->year . "-" . $in->month . "-" . $in->day;
		$attest->days = $in->days;
		$attest->description = $in->description;
		$attest->save();

		return ['status' => 1];
	}

	public function link(Request $in)
	{
		if (!isset($in->student_id)) {
			return ['status'=>0,'message'=>'Dados incompletos.'];
		}
		$user = Crypt::decrypt($in->student_id);

		//$r = Relationship::where('user_id', auth()->user()->id)->where('friend_id', $user)->whereType($type)->first();
		$r = Study::where('institution_id', auth()->id())->where('student_id', $in->student_id)->first();
		if ($r and $r->status == "E") {
			return ['status'=>0, 'message'=>"Usuário já possui esse relacionamento."];
		} elseif ($r) {
			$r->status = "E";
		} else {
			$r = new Relationship;
			$r->institution_id = auth()->user()->id;
			$r->student_id = $user;
		}
		$r->save();

		//return Redirect::back()->with("success", "Relacionamento criado com sucesso.");
		return ['status' => 1,'message'=>"Relacionamento criado com sucesso."];
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

	public function reportStudentClass(Request $in)
	{
		if (!isset($in->student_id)) {
			return ['status'=>0,'message'=>"Dados incompletos"];
		}
		if (!isset($in->class)) {
			return ['status'=>0,'message'=>"Dados incompletos"];
		}
		$in->student_id = Crypt::decrypt($in->student_id);
		/*$disciplines = DB::select("SELECT	Courses.id as course, Disciplines.name, Offers.id as offer, Attends.id as attend, Classes.status as statusclasse "
			. "FROM Classes, Periods, Courses, Disciplines, Offers, Units, Attends "
			. "WHERE Courses.idInstitution=? AND Courses.id=Periods.idCourse AND Periods.id=Classes.idPeriod AND Classes.class=? AND Classes.id=Offers.idClass AND Offers.idDiscipline=Disciplines.id AND Offers.id=Units.idOffer AND Units.id=Attends.idUnit AND Attends.idUser=? "
			. "group by Offers.id",
			[auth()->user()->id, Input::get("class"), $student]);*/
		$courses_ids = auth()->user()->courses()->pluck('_id');
		$periods_ids = Period::whereIn('course_id',$courses_ids)->get(['_ids'])->pluck('_id');
		$classes_ids = Classe::whereIn('period_id',$periods_ids)->where('class',$in->class)->get(['_id'])->pluck('_id');
		$offers_ids = Offer::whereIn('classe_id',$classes_ids)->get(['_id'])->unique()->pluck('_id');
		$units_ids = Unit::whereIn('offer_id',$offers_ids)->get(['_id'])->pluck('_id');
		$student = User::where('_id',$in->student_id)->attends();

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
		return ['status' => 1, "disciplines" => $disciplines];
	}
}
