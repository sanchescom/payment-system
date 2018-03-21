<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 *
 * @package App
 */
class User extends Model
{
	protected $fillable = [
		'name',
		'country',
		'city',
		'currency',
	];

	protected $hidden = [
		'purse',
		'amount',
	];


	public function create(array $attributes = [])
	{

	}
}
