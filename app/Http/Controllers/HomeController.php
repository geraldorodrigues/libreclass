<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index() {
		switch (auth()->user()->type) {
			case 'I':
				return view('layouts.institution');
				# code...
				break;

			case 'T':
				return view('layouts.teacher');

			default:
				return view('layouts.institution');
				break;
		}
	}

  public function showWelcome()
  {
    return view('hello');
  }

}
