<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MySql\Unit;
use App\MySql\User;
use App\MySql\Lesson;
use App\MySql\Exam;
use App\MySql\Attend;
use App\MySql\Frequency;
use App\MySql\Offer;
use App\MySql\ExamsValue;
use Session;
use Redirect;
use Crypt;


class UnitController extends Controller
{

	public function save(Request $in)
	{
		$offer = Offer::find(Crypt::decrypt($in->offer_id));
		if (!$offer){
			return ['status' => 0, 'message' => "Oferta não encontrada"];
		}
		if (auth()->id() != $offer->class->period->course->institution_id) {
			return ['status' => 0, 'message' => "Operação não permitida"];
		}

		$old = Unit::where("offer_id", $offer->id)->orderBy("value", "desc")->first();

		if (isset($in->unit_id)){//Edição
			$unit->calculation = $in->calculation;//Apenas o cálculo da nota pode ser editado
			$unit->save();
		} else {
			$unit = new Unit;
			$unit->offer_id = $old->offer_id;
			$unit->value = $old->value + 1;
			$unit->calculation = $old->calculation;
			$unit->save();

			$attends = Attend::where("unit_id", $old->id)->get();

			foreach ($attends as $attend) {
				$new = new Attend;
				$new->unit_id = $unit->id;
				$new->student_id = $attend->student_id;
				$new->save();
			}
		}

		unset($unit->created_at);
		unset($unit->updated_at);
		$unit->id = Crypt::encrypt($unit->id);

		return ['status'=>1, 'unit'=>$unit];
	}

	//Remove aluno de uma unidade (deleta attend)
	public function removeStudent(Request $in)
	{
		if (!isset($in->attend_id)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$attend = Crypt::find(decrypt($in->attend_id));
		if (!$attend){
			return ['status'=>0, 'message'=>'Matrícula não encontrada'];
		}

		$attend->delete();

		return ['status'=>1];
	}

	//Matricula aluno em uma unidade (cria attend)
	public function addStudent(Request $in)
	{
		if (!isset($in->unit_id) || !isset($in->student_id)){
			return ['status'=>0, 'message'=>'Dados incompletos'];
		}

		$unit = Crypt::find(decrypt($in->unit_id));
		if (!$unit){
			return ['status'=>0, 'message'=>'Unidade não encontrada'];
		}

		$student = Crypt::find(decrypt($in->student_id));
		if (!$student){
			return ['status'=>0, 'message'=>'Estudante não encontrado'];
		}

		$attend = Attend::where("unit_id", $unit->id)->where("student_id", $student->id)->first();
		if ($attend) {
			return ['status'=>0, 'message'=>'Aluno já cadastrado nesta turma'];
		}

		$attend = Attend::create(['unit_id'=>$unit->id, 'student_id'=>$student->id]);

		unset($attend->created_at);
		unset($attend->updated_at);
		$attend->id = Crypt::encrypt($attend->id);

		return ['status'=>1, 'attend'=>$attend];
	}

	// public function getStudent()
	// {
	// 	$students = User::whereType("N")->orderby("name")->get();
	// 	$list_students = [];
	// 	foreach ($students as $student) {
	// 		$list_students[Crypt::encrypt($student->id)] = $student->name;
	// 	}
	//
	// 	$students = DB::select("SELECT Users.name as name, Users.id as id FROM Users, Attends WHERE Users.id=Attends.idUser AND Attends.idUnit = " . $this->unit->id . " ORDER BY Users.name");
	//
	// 	return view("modules.units", ["user" => $user, "list_students" => $list_students, "students" => $students]);
	// }

	// public function getReportunitz()
	// {
	// 	if ($this->idUser) {
	// 		$user = User::find($this->idUser);
	//
	// 		$students = DB::select("select Users.id, Users.name "
	// 			. "from Users, Attends, Units "
	// 			. "where Units.id=? and Attends.idUnit=Units.id and Attends.idUser=Users.id "
	// 			. "group by Users.id order by Users.name", [Crypt::decrypt(Input::get("u"))]);
	//
	// 		return $students;
	//
	// 	}
	// }

	/**
	 * Retorna um PDF com o relatório de frequência e notas
	 * @param int $idUnit - Id da unidade (unidade da disciplina ofertada)
	 * @return file
	 */
	public function getReportUnit($idUnit)
	{
		try {
			$unit = Unit::find(Crypt::decrypt($idUnit));
			switch ($unit->calculation) {
				case 'S':
				case 'A':
				case 'W':
					return $this->printDefaultReport($unit);
					break;
				case 'P':
					return $this->printDescriptiveReport($unit);
					break;
				default:
					throw new Exception('Error: Unknown report type');
					break;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	} //--- Imprimir PDF

	/**
	 * Imprime o relatório da oferta, acessível pelo perfil de professor e instituição, quando o método de
	 * avaliação for somatório, média aritmética ou média ponderada.
	 *
	 * @param	Unit	 $unit [Unidade a gerar relatório]
	 * @return [File]			 [PDF com o relatório preparado para impressão]
	 */
	private function printDefaultReport(Unit $unit)
	{
		try {
			$data = [];
			$institution = $unit->offer->classe->period->course->institution()->first();
			$institution->local = $institution->printCityState();
			$data['institution'] = $institution;
			$data['classe'] = $unit->offer->getClass();
			$data['period'] = $unit->offer->classe->getPeriod();
			$data['course'] = $unit->offer->classe->period->getCourse();

			$offer = Offer::find($unit->idOffer);

			$students = DB::select(""
				. " SELECT Users.id, Users.name "
				. " FROM Users, Attends, Units "
				. " WHERE Units.idOffer=? AND Attends.idUnit=Units.id AND Attends.idUser=Users.id "
				. " GROUP BY Users.id "
				. " ORDER BY Users.name ASC", [$offer->id]
			);
			$data['students'] = [];
			foreach ($students as $student) {
				$data['students'][] = $student;
			}

			$lessons = $unit->getLessonsToPdf();

			// Prepara o nome das aulas com a data de realização das mesmas
			$data['lessons'] = [];
			$data['lessons_notes'] = [];
			foreach ($lessons as $key => $lesson) {
				$date = explode('-', $lesson->date)[2] . '/' . explode('-', $lesson->date)[1] . '/' . explode('-', $lesson->date)[0];
				$data['lessons'][$key] = 'Aula ' . (string) ($key + 1) . ' - ' . $date;
				$data['lessons_notes'][$key] = [
					'description' => 'Aula ' . (string) ($key + 1) . ' - ' . $date,
					'title' => isset($lesson->title) && !empty($lesson->title) ? $lesson->title : 'Sem título',
					'note' => isset($lesson->notes) && !empty($lesson->notes) ? $lesson->notes : 'Sem nota de aula',
				];
				// dd($data['lessons'][$key]);
			}

			// Percorre a lista de todos os alunos
			foreach ($data['students'] as $key => $student) {
				$absences = 0;
				$data['students'][$key]->number = $key + 1;

				// Obtém frequência escolar do aluno
				$data['students'][$key]->absences = [];
				for ($i = 0; $i < count($lessons); $i++) {
					if (isset($lessons[$i])) {
						$value = Frequency::getValue($student->id, $lessons[$i]->id);
						if ($value == "F") {
							$absences++;
						}
						$data['students'][$key]->absences[$i] = ($value == "P") ? "." : $value;
					} else {
						$data['students'][$key]->absences[$i] = ".";
					}
				}

				// Quantidade total de faltas
				$data['students'][$key]->countAbsences = (string) $absences;

				$exams = $unit->getExams();
				$data['exams'] = [];
				foreach ($exams as $_key => $exam) {
					$data['exams'][$_key] = $exam;
					$data['exams'][$_key]['number'] = $_key + 1;
					$date = explode('-', $exam->date)[2] . '/' . explode('-', $exam->date)[1] . '/' . explode('-', $exam->date)[0];
					$data['exams'][$_key]['date'] = $date;
				}

				$data['students'][$key]->exams = [];

				// Inclui as avaliações realizadas pelo anulo
				foreach ($exams as $exam) {
					$data['students'][$key]->exams[] = ExamsValue::getValue($student->id, $exam->id) ? ExamsValue::getValue($student->id, $exam->id) : '-';
				}

				// Registra a média e a média final após prova de recuperação
				$average = $unit->getAverage($student->id);
				$data['students'][$key]->average = empty($average[0]) ? "-" : sprintf("%.2f", $average[0]);
				$data['students'][$key]->finalAverage = empty($average[1]) ? "-" : sprintf("%.2f", $average[1]);
			}
			// dd($data['students']);

			// return view('reports.diary', ['data' => $data]);

			// PDF::setOptions(['tempDir', storage_path()]);
			$pdf = PDF::loadView('reports.diary', ['data' => $data])
				->setPaper('a4')
				->setOrientation('landscape')
				->setOption('margin-top', 5)
				->setOption('margin-right', 5)
				->setOption('margin-bottom', 5)
				->setOption('margin-left', 5);
			return $pdf->stream();

		} catch (Exception $e) {
			return view("reports.report_error", [
				"message" => $e->getMessage() . ' ' . $e->getLine() . ', file: (' . $e->getFile() . ').',
			]);
		}
	}

	/**
	 * Imprime o relatório da oferta, acessível pelo perfil de professor e instituição, quando o método de
	 * avaliação for Parecer Descritivo.
	 *
	 * @param	Unit	 $unit [Unidade a gerar relatório]
	 * @return [File]			 [PDF com o relatório preparado para impressão]
	 */
	private function printDescriptiveReport(Unit $unit)
	{
		try {
			$data = [];
			$institution = $unit->offer->classe->period->course->institution()->first();
			$institution->local = $institution->printCityState();
			$data['institution'] = $institution;
			$data['classe'] = $unit->offer->getClass();
			$data['period'] = $unit->offer->classe->getPeriod();
			$data['course'] = $unit->offer->classe->period->getCourse();

			$offer = Offer::find($unit->idOffer);

			$students = DB::select(""
				. " SELECT Users.id, Users.name "
				. " FROM Users, Attends, Units "
				. " WHERE Units.idOffer=? AND Attends.idUnit=Units.id AND Attends.idUser=Users.id "
				. " GROUP BY Users.id "
				. " ORDER BY Users.name ASC", [$offer->id]
			);
			$data['students'] = [];
			foreach ($students as $student) {
				$data['students'][] = $student;
			}

			$lessons = $unit->getLessonsToPdf();

			// Prepara o nome das aulas com a data de realização das mesmas
			$data['lessons'] = [];
			$data['lessons_notes'] = [];
			foreach ($lessons as $key => $lesson) {
				$date = explode('-', $lesson->date)[2] . '/' . explode('-', $lesson->date)[1] . '/' . explode('-', $lesson->date)[0];
				$data['lessons'][$key] = 'Aula ' . (string) ($key + 1) . ' - ' . $date;
				$data['lessons_notes'][$key] = [
					'description' => 'Aula ' . (string) ($key + 1) . ' - ' . $date,
					'title' => isset($lesson->title) && !empty($lesson->title) ? $lesson->title : 'Sem título',
					'note' => isset($lesson->notes) && !empty($lesson->notes) ? $lesson->notes : 'Sem nota de aula.',
				];
				// dd($data['lessons'][$key]);
			}

			// Percorre a lista de todos os alunos
			foreach ($data['students'] as $key => $student) {
				$absences = 0;
				$data['students'][$key]->number = $key + 1;

				// Obtém frequência escolar do aluno
				$data['students'][$key]->absences = [];
				for ($i = 0; $i < count($lessons); $i++) {
					if (isset($lessons[$i])) {
						$value = Frequency::getValue($student->id, $lessons[$i]->id);
						if ($value == "F") {
							$absences++;
						}
						$data['students'][$key]->absences[$i] = ($value == "P") ? "." : $value;
					} else {
						$data['students'][$key]->absences[$i] = ".";
					}
				}

				// Quantidade total de faltas
				$data['students'][$key]->countAbsences = (string) $absences;
			}

			$unit->count_lessons = $unit->countLessons();
			$lessons = $unit->getLessons();

			$institution = $unit->offer->classe->period->course->institution()->first();
			$institution->local = $institution->printCityState();

			if (!isset($institution->photo) || empty($institution->photo)) {
				throw new Exception('A Instituição não concluiu o cadastro, pois não identificamos a <b>foto de perfil</b> que é utilizada para construir o relatório.');
			}

			$exams = $unit->getExams();
			if (count($exams) == 0) {
				$data['exams'] = null;
				// throw new Exception('É necessário criar pelo menos uma <b>avaliação</b> para gerar o relatório de parecer descritivo.');
			} else {
				foreach ($exams as $exam) {
					$descriptions = $exam->descriptive_exams();
					foreach ($descriptions as $description) {
						$description->student->absence = 0;
						foreach ($lessons as $lesson) {
							$value = Frequency::getValue($description->student->id, $lesson->id);
							if ($value == 'F') {
								$description->student->absence++;
							}
						}
					}
					$data['exams'][] = ['data' => $exam, 'descriptions' => $descriptions];
				}
			}

			$data['unit'] = $unit;
			$data['discipline'] = $unit->offer->discipline->name;
			$data['teachers'] = $unit->offer->getTeachers();

			$pdf = PDF::loadView('reports.diary-descriptive', ['data' => $data])
				->setPaper('a4')
				->setOrientation('landscape')
				->setOption('margin-top', 5)
				->setOption('margin-right', 5)
				->setOption('margin-bottom', 5)
				->setOption('margin-left', 5);
			return $pdf->stream();
		} catch (Exception $e) {
			return view("reports.report_error", [
				"message" => $e->getMessage() . ' ' . $e->getLine() . '.',
			]);
		}
	}

}
