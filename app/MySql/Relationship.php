<?php

namespace App\MySql;

use Illuminate\Database\Eloquent\Model;

class Relationship extends Model
{
  protected $table = "Relationships";
  protected $connection = 'mysql';
  protected $fillable = ['idUser', 'idFriend', 'type'];

  public function getUser()
  {
    return User::find($this->idUser);
  }

  public function getFriend()
  {
    return User::find($this->idFriend);
  }
}
