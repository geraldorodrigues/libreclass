<?php

namespace App\MySql;

use Illuminate\Database\Eloquent\Model;

class Adminer extends Model
{
	protected $table = "Adminers";
	protected $connection = 'mysql';
}
