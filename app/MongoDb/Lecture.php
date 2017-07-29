<?php

namespace App\MongoDb;

use Illuminate\Database\Eloquent\Model;

class Lecture extends \Moloquent
{
	protected $fillable = ['user_id', 'offer_id'];

	public function offer()
	{
		return $this->belongsTo('App\Offer');
	}

	public function getUser()
	{
		return User::find($this->idUser);
	}

	public function getOffer()
	{
		return Offer::find($this->idOffer);
	}


}
