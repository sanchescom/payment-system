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
 * @property int $amount
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
        'reserved',
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


    /**
     * Mutator for converting from float to integer
     * It is saving resources of date base to save information in integer instead of double
     *
     * @return float|int
     */
    public function getAmountAttribute()
    {
        $amount = $this->attributes['amount'];

        if ($amount > 0)
        {
            return $amount / 100;
        }
        else
        {
            return $amount;
        }
    }


    /**
     * Mutator for converting from integer to float
     *
     * @param $value
     * @return void
     */
    public function setAmountAttribute($value)
    {
        $attributes = $this->attributes;

        $attributes['amount'] = $value * 100;

        $this->attributes = $attributes;
    }


    public function increaseReserved($reserved)
    {
        $this->increment('reserved', $reserved * 100);
    }
}
