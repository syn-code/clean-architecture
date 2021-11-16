<?php

namespace App\Coupon;

use App\Billing\Gateway;

class CouponFactory
{
    public static function generate(array $payload, Gateway $gateway): Coupon
    {
        return new Coupon($payload, $gateway);
    }
}
