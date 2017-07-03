<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
  protected $table = "Lectures";
  protected $fillable = ['idUser', 'idOffer'];

  public function getUser()
  {
    return User::find($this->idUser);
  }

  public function getOffer()
  {
    return Offer::find($this->idOffer);
  }

  public function offer()
  {
    return $this->belongsTo('Offer', 'idOffer');
  }

}
