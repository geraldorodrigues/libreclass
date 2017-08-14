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
use Crypt;
use StdClass;
use Session;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
	public function read(Request $in)
	{
		if (!isset($in->institution_id)) {
			return ['status'=>0,'message'=>"Dados incompletos"];
		}
		$in->institution_id = Crypt::decrypt($in->institution_id);
		$institution = User::find($in->institution_id);

		return ['status' => 1, 'institution' => $institution];
	}

	public function save()
	{
		public function save(Request $in) {
			if (isset($in->institution_id)) {
				$institution = User::find(Crypt::decrypt($in->institution_id));

				if (!$institution) {
					return ['status' => 0, 'message' => "Instituição não encontrado!"];
				}

				$institution->email = $in->email;
				$institution->name = $in->name;
				$institution->save();

				unset($institution->created_at);
				unset($institution->updated_at);
				unset($institution->password);

				return ['status' => 1, 'institution' => $institution];
			} else {
				if (User::where('email', $in->email)->count()) {
					return ['status' => 0, 'message' => "Este email já está cadastrado!"];
				}
				$institution = new User;
				$institution->type = "I";
				$institution->name = $in->name;
				$institution->email = $in->email;
				$institution->password = Crypt::encrypt('12345');
				$institution->save();

				unset($institution->created_at);
				unset($institution->updated_at);
				unset($institution->password);

				return ['status' => 1, 'institution' => $institution];
			}
	}
}
