<?php

namespace App\Http\Controllers;

use App\MySql\User;
use Session;
use Redirect;
use Mail;

class SocialController extends Controller
{
  private $idUser;

  public function __construct()
  {
    // Log::info('SocialController', []);
    if (Auth::check()) {
      $this->idUser = false;
    } else {
      $user = Auth::user();
      $this->idUser = $user->id;
    }

  }

  public function getIndex()
  {
    if (Session::has("redirect")) {
      return Redirect::to(Session::get("redirect"));
    }

    $user = Auth::user();
    Session::put("type", $user->type);

    return view("social.home", ["user" => $user]);
  }

  public function postQuestion()
  {
    //~ print_r(Input::all());
    foreach (Input::all() as $key => $value) {
      return User::whereId($this->idUser)->update([$key => $value]);
    }

  }

  public function postSuggestion()
  {
    $suggestion = new Suggestion;
    $suggestion->idUser = $this->idUser;
    $suggestion->title = Input::get("title");
    $suggestion->value = Input::get("value");
    $suggestion->description = Input::get("description");
    $suggestion->save();

    $user = User::find($this->idUser);

    Mail::send('email.suporte', ["descricao" => Input::get("description"), "email" => $user->email, "title" => Input::get("title")], function ($message) {
      $op = ["B" => "Bugson", "O" => "Outros", "S" => "Sugestão"];
      $message->to("modeon.co@gmail.com", "Suporte")
        ->subject("LibreClass Suporte - " . $op[Input::get("value")]);
    });

    return Redirect::back()->with("success", "Obrigado pela sua mensagem. Nossa equipe irá analisar e responderá o mais breve possível.");
  }

}
