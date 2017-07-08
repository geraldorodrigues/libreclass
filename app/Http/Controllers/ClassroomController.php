<?php

namespace App\Http\Controllers;

use App\MySql\User;
use Session;
use Redirect;

class ClassroomController extends Controller
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

	public function getIndex()
	{
		if (Session::has("redirect")) {
			return Redirect::to(Session::get("redirect"));
		}
		$user = User::find($this->idUser);
		Session::put("type", $user->type);
		return view("classrooms.home", ["user" => $user]);
	}

	public function getCampus()
	{
		$user = User::find($this->idUser);
		return view("classrooms.campus", ["user" => $user]);
	}

}
