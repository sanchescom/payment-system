<?php

namespace App;

use App\Traits\PrivateAttributes;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends \Eloquent
{
    use PrivateAttributes;


    /**
     * Mutator for converting from float to integer
     * It is saving resources of date base to save information in integer instead of double
     *
     * @return float|int
     */
    public function getAmountAttribute()
    {
        return $this->attributeGetterConverterIntegerToDouble('amount');
    }


    /**
     * Mutator for converting from integer to float
     *
     * @param $value
     * @return void
     */
    public function setAmountAttribute($value)
    {
        $this->attributeSetterConverterDoubleToInteger('amount', $value);
    }


    protected function attributeGetterConverterIntegerToDouble($attribute)
    {
        $amount = $this->attributes[$attribute];

        if ($amount > 0)
        {
            return $amount / 100;
        }
        else
        {
            return $amount;
        }
    }


    protected function attributeSetterConverterDoubleToInteger($attribute, $value)
    {
        $attributes = $this->attributes;

        $attributes[$attribute] = $value * 100;

        $this->attributes = $attributes;
    }
}