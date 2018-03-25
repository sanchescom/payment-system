<?php

namespace App\Collections;

class PaymentsCollection extends BaseCollection
{
    public function getNativeAndDefaultSum()
    {
        $native_sum   = $this->first()['native_sum'];
        $default_sum = $this->first()['default_sum'];


        return [
            'native' => $native_sum > 0 ? $native_sum / 100 : 0,
            'default' => $default_sum > 0 ? $default_sum / 100 : 0,
        ];
    }
}