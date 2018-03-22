<?php

namespace App;

use App\Traits\PrivateAttributes;
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
    use PrivateAttributes;

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

	protected $private = [
	    'name',
        'currency',
        'purse',
    ];


	public function create(array $attributes = []): User
	{
		$this->fill($attributes);
        $this->setAttribute('name', $attributes['name']);
        $this->setAttribute('currency', $attributes['currency']);
        $this->save();

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
