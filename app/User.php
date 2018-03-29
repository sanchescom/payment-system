<?php

namespace App;

use App\Exceptions\UnchangeableProperty;
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
 * @property string $secret
 * @property string $account
 *
 * @package App
 */
class User extends BaseModel
{
	protected $fillable = [
		'name',
		'email',
		'currency',
		'country',
		'city',
	];

	protected $guarded = [
		'amount',
		'account',
	];

	protected $hidden = [
		'secret',
	];

	protected $private = [
		'account',
		'secret',
	];


	/**
	 * @param $email
	 * @return User|\Illuminate\Database\Eloquent\Builder|Model
	 */
	public static function findByEmail($email)
	{
		return self::query()->where('email', $email)->first();
	}


	/**
	 * @param $account
	 * @return User|\Illuminate\Database\Eloquent\Builder|Model
	 */
	public static function findByAccount($account)
	{
		return self::query()->where('account', $account)->first();
	}


	public function increaseAmount($amount)
	{
		$this->amount = $this->amount + $amount;
		$this->save();
	}


	public function decreaseAmount($amount)
	{
		$this->amount = $this->amount - $amount;
		$this->save();
	}


	/**
	 * We can set account only if wasn't set before
	 *
	 * @param $account
	 * @throws \Exception
	 */
	public function setAccount($account)
	{
		if ($this->getAccount() != null)
		{
			throw new UnchangeableProperty('It is impossible to change account if it was set before');
		}

		$this->setAttribute('account', $account);
	}


	/**
	 * We use getter of attributes for access to account because it private attribute
	 *
	 * @return mixed
	 */
	public function getAccount()
	{
		return $this->getAttribute('account');
	}


	public function setSecret($account)
	{
		$this->setAttribute('secret', $account);
	}


	public function getSecret()
	{
		return $this->getAttribute('secret');
	}


	public function getData()
	{
		return [
			'id'       => $this->id,
			'name'     => $this->name,
			'currency' => $this->currency,
			'account'  => $this->getAccount(),
		];
	}
}
