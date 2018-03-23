<?php

namespace App;

use App\Entities\Secret;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $country
 * @property string $city
 * @property string $email
 * @property string $currency
 * @property string secret
 *
 * @package App
 */
class User extends BaseModel
{
	protected $fillable = [
		'country',
		'city',
	];

	protected $guarded = [
		'name',
		'currency',
		'amount',
		'email',
	];

	protected $hidden = [
	    'secret',
    ];


    /**
     * @param array $attributes
     * @param Secret $secret
     * @return User
     */
    public static function createWithSecret(array $attributes = [], Secret $secret): User
	{
		$instance = new self();
		$instance->secret = $secret->getHash();
		$instance->forceFill($attributes);
		$instance->save();

		return $instance;
	}


    /**
     * @param $email
     * @return User|\Illuminate\Database\Eloquent\Builder|Model
     */
    public static function findByEmail($email)
    {
        return self::query()->where('email', $email)->first();
    }
}
