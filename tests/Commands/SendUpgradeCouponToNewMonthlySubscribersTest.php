<?php

namespace Tests\Commands;

use App\Billing\Gateway;
use App\Mail\UpgradeToYearly;
use Illuminate\Auth\Access\Gate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendUpgradeCouponToNewMonthlySubscribersTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_sends_an_upgrade_coupon_to_new_monthly_subscribers()
    {
        Mail::fake();
        // Given we have a user who signed up 3 months ago
        //this person should receive an upgrade coupon
        User::factory()->create(['created_at' => today()->subMonths(3)]);

        //this person should not
        User::factory()->create(['created_at' => today()->subMonths()]);
        //user 3 months ago but has a yealy plan
        User::factory()->create(['created_at' => today()->subMonths(3), 'stripe_plan' => 'yearly']);

        // will allow us to test if calls to methods within the given class are made
        $gatewaySpy =  Mockery::spy(Gateway::class);
        //swap our instance of the gateway class with our gateway mockery
        //this tells laravel if we have to resolve the di during a test, we want the mockery instance instead
        $this->swap(Gateway::class, $gatewaySpy);

        // When I run this artisan command
        $this->artisan('laracasts:send-upgrade-coupon-to-new-monthly-subscribers');

        // we should generate a coupon for 10% off
        $gatewaySpy->shouldHaveReceived()->createCoupon(Mockery::on(function($coupon) {
            //we pass the fake coupon to the make sure that 10 percent is being passed
            return ($coupon['percentage_discount'] === 10);
        }))->once();
        // Then they should receive an email
        Mail::assertSent(UpgradeToYearly::class);
    }
}
