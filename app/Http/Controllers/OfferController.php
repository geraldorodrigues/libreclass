<?php

namespace App\Http\Controllers;

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

  public function getUser()
  {
    $user = Crypt::decrypt(Input::get("u"));
    if ($user) {
      $user = User::find($user);
      return $user;
    } else {
      return Redirect::guest("/");
    }
  }

  public function index(Request $in)
  {
    if ($user = User::find($in->user_id)) {

      $classe = Classe::find(Crypt::decrypt($in->classe_id));
      $period = Period::find($classe->period_id);
      $course = Course::find($period->course_id);
      $offers = Offer::where("classe_id", $classe->id)->get();
      foreach ($offers as $offer) {
        $teachers = [];
        $list = Lecture::where("offer_id", $offer->id)->get();
        foreach ($list as $value) {
          $teachers[] = Crypt::encrypt($value->user_id);
        }

        $offer->teachers = $teachers;
      }

      return ['status' => 1,
          "course" => $course,
          "user" => $user,
          "offers" => $offers,
          "period" => $period,
          "classe" => $classe,
        ];
    } else {
      return ['status' => 0];
    }
  }

  public function unit(Request $in)
  {
    $offer = Offer::find(Crypt::decrypt($in->offer_id));
    if (auth()->id() != $offer->class->period->course->institution_id) {
      ['status' => 0, 'message' => "Você não tem permissão para criar unidade"];
    }

    $old = Unit::where("offer_id", $offer->id)->orderBy("value", "desc")->first();

    $unit = new Unit;
    $unit->offer_id = $old->offer_id;
    $unit->value = $old->value + 1;
    $unit->calculation = $old->calculation;
    $unit->save();

    $attends = Attend::where("unit_id", $old->id)->get();

    foreach ($attends as $attend) {
      $new = new Attend;
      $new->unit_id = $unit->id;
      $new->user_id = $attend->user_id;
      $new->save();
    }

    return ['status' => 1, 'message' => "Unidade criada com sucesso!"];
  }

  public function teacher(Request $in)
  {
    // return Input::all();

    $offer = Offer::find(Crypt::decrypt($in->offer_id));
		if ($offer) {

			$offer->classroom = $in->classroom;
			$offer->day_period = $in->day_period;
			$offer->maxlessons = $in->maxlessons;
			$offer->save();
			$lectures = $offer->getAllLectures();

			$teachers = [];
			if (Input::has("teachers")) {
				$teachers = Input::get("teachers");
				for ($i = 0; $i < count($teachers); $i++) {
					$teachers[$i] = base64_decode($teachers[$i]);
				}

			}
			// return $teachers;
			foreach ($lectures as $lecture) {
				$find = array_search($lecture->idUser, $teachers);
				if ($find === false) {
					Lecture::where('idOffer', $offer->id)->where('idUser', $lecture->idUser)->delete();
				} else {
					unset($teachers[$find]);
				}

			}

			foreach ($teachers as $teacher) {
				$last = Lecture::where("idUser", $teacher)->orderBy("order", "desc")->first();
				$last = $last ? $last->order + 1 : 1;

				$lecture = new Lecture;
				$lecture->idUser = $teacher;
				$lecture->idOffer = $offer->id;
				$lecture->order = $last;
				$lecture->save();
			}

			//   $idTeacher = Crypt::decrypt(Input::get("teacher"));
			//   $last = Lecture::where("idUser", $idTeacher)->orderBy("order", "desc")->first();
			//   $last = $last ? $last->order+1 : 1;
			//
			//   if (!$lecture) {
			//     $lecture = new Lecture;
			//     $lecture->idUser = $idTeacher;
			//     $lecture->idOffer = $offer->id;
			//     $lecture->order = $last;
			//     $lecture->save();
			//   }
			//   else if($lecture->idUser != $idTeacher) {
			//     Lecture::where('idOffer', $offer->id)->where('idUser', $lecture->idUser)->update(["idUser" => $idTeacher, "order" => $last]);
			//   }
			// }
			// else if ($lecture)
			// {
			//   Lecture::where('idOffer', $offer->id)->where('idUser', $lecture->idUser)->delete();
			// }

			return Redirect::guest(Input::get("prev"))->with("success", "Modificado com sucesso!");
		}
		return ['status' => 0, 'message' => 'Oferta não encontrada']
  }

  public function postStatus()
  {
    $status = Input::get("status");
    $id = Crypt::decrypt(Input::get("unit"));

    $unit = Unit::find($id);
    if (!strcmp($status, 'true')) {
      $unit->status = 'E';
    } else {
      $unit->status = 'D';
    }
    $unit->save();

    return "Status changed to " . $status . " / " . $id;
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
