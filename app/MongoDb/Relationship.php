<?php

namespace App\MongoDb;

class Relationship extends \Moloquent
{
	protected $fillable = ['user_id', 'friend_id', 'type'];

	public function getUser()
	{
		return User::find($this->idUser);
	}

	public function getFriend()
	{
		return User::find($this->idFriend);
	}
}
