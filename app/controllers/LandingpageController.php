<?php

class LandingpageController extends \BaseController
{

  // name: $('#inputName').val(),
  // phone: $('#inputPhone').val(),
  // email: $('#inputEmail').val(),
  // subject: $('#inputSubject').val(),
  // message: $('#inputMessage').val()
  public function message()
  {
    try {
      if (!Input::has('name') && !Input::has('phone') && !Input::has('email') && !Input::has('subject') && !Input::has('message')) {
        throw new Exception("Por favor, preencha todos os campos.", 1);
      }
      $data = [
        'name' => Input::get('name'),
        'phone' => Input::get('phone'),
        'email' => Input::get('email'),
        'subject' => Input::get('subject'),
        'message' => Input::get('message'),
      ];
      Mail::send('email.contact', ['data' => $data], function ($message) use ($data) {
        $message->subject("[LibreClass CE - Contato pelo site]");
        $message->from('modeon.co@gmail.com', 'Modeon');
        $message->to($data['email']);
        $message->cc('modeon.co@gmail.com');
      });
      return Response::json(['status' => 1, 'message' => 'Request completed']);
    } catch (Exception $e) {
      if ($e->getCode() == 1) {
        return Response::json([
          'status' => '0',
          'message' => $e->getMessage(),
        ]);
      }
      return Response::json([
        'status' => '0',
        'message' => $e->getMessage(),
        'line' => $e->getLine(),
      ]);
    }
  }

}
