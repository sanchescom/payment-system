<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $country
 * @property string $city
 * @property string $purse
 * @property int $currency
 *
 * @package App
 */
class User extends Model
{
	protected $fillable = [
		'country',
		'city',
	];

	protected $guarded = [
		'name',
		'currency',
		'amount',
		'purse',
	];

	private $name;
	private $currency;
	private $purse;


	public function create(array $attributes = []): User
	{
		$instance = new self();
		$instance->fill($attributes);
		$instance->setAttribute('name', $attributes['name']);
		$instance->setAttribute('currency', $attributes['currency']);
		$instance->save();

		return $this;
	}


	public function generatePurse(string $secret): User
	{
		$this->purse = encrypt(implode('-', [
			$this->id,
			$this->name,
			$this->currency,
			$secret,
		]));

		return $this;
	}



}
