<?php

namespace App;

use App\Traits\PrivateAttributes;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
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
}