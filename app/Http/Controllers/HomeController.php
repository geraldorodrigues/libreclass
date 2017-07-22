<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{

	public function index(){
		return view('layouts.institution');
	}

  public function showWelcome()
  {
    return view('hello');
  }

}
