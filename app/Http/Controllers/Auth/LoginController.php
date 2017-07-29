<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

		private function dataLog($in, $description = "")
		{
			$data_log = [];
			$data_log['login'] = $in->get('login');
			$data_log['ip'] = $in->ip();
			MongoLog::save($in, $description);

			return $data_log;
		}

		public function authenticate(Request $in)
		{
			Log::useFiles(storage_path('logs/login.log'));

			$user = User::whereEmail($in->login)->orWhere('cpf', preg_replace('/\D/', '', $in->login))->first();
			if ( $user ) {
				if ( $user->status == 'W' ) {
					return response()->json(['status' => 0, 'message' => 'Estamos aguardando a confirmação do email. <a class="text-danger ck" id="resendEmail"><u>Reenviar email de validação da conta</u></a>']);
				}
				if ( $user->status == 'D' ) {
					return response()->json(['status' => 0, 'message' => 'Sua conta está desabilitada.']);
				}
				if ( $user->status == 'E' && Auth::attempt(['_id' => $user->id, 'password' => $in->get('password')], $in->has('remember'))) {
					$user->id = Crypt::encrypt($user->id);
					unset($user->created_at);
					unset($user->updated_at);
					unset($user->investments_access);
					Log::notice('login success', $this->dataLog($in, "Login realizado"));
					return response()->json(['status' => 1, 'user' => $user]);
				}
			}

			return response()->json(['status' => 0, 'message' => 'Usuário e/ou senha inválidos']);
		}

		public function out(Request $in)
		{
			Auth::logout();
			return ($in->ajax() || $in->wantsJson()) ? response()->json(['status' => 1]) : redirect('/login');
		}
}
