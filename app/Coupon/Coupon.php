<?php

namespace App\Coupon;

use App\Billing\Gateway;

class Coupon
{
    /**
     * @var array
     */
    private $couponPayload;
    /**
     * @var Gateway
     */
    private $gateway;

    public function __construct(array $couponPayload, Gateway $gateway)
    {
        $this->gateway = $gateway;
        $this->couponPayload = $couponPayload;
        $this->generate();
    }

    private function generate()
    {
       return $this->gateway->createCoupon($this->couponPayload);
    }
}
