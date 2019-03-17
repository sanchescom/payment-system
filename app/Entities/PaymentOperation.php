<?php

namespace App\Entities;

use App\Collections\PaymentsCollection;
use App\Payment;
use App\User;

/**
 * Class PaymentOperation
 * @package App\Entities
 */
class PaymentOperation
{
    protected $user;
    protected $payments;
    protected $summaries;

    /**
     * @param User $user
     * @param PaymentsCollection $payments
     * @param PaymentsCollection $summaries
     * @return PaymentOperation
     */
    public static function make(
        User $user,
        PaymentsCollection $payments,
        PaymentsCollection $summaries
    ) {
        return new static($user, $payments, $summaries);
    }

    public function __construct(
        User $user,
        PaymentsCollection $payments,
        PaymentsCollection $summaries
    ) {
        $this->user = $user;
        $this->payments = $payments;
        $this->summaries = $summaries;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return PaymentsCollection|Payment[]
     */
    public function getPayments(): PaymentsCollection
    {
        return $this->payments;
    }

    /**
     * @return PaymentsCollection|Payment[
     */
    public function getSummaries(): PaymentsCollection
    {
        return $this->summaries;
    }
}