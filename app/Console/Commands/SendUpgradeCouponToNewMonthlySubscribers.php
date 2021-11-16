<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Coupon\CouponFactory;
use App\Models\User;
use App\Billing\Gateway;
use App\Traits\Subscribers;
use Illuminate\Console\Command;
use App\Mail\UpgradeToYearly;
use Illuminate\Support\Facades\Mail;
use App\Coupon\Coupon;
use Illuminate\Support\Str;

class SendUpgradeCouponToNewMonthlySubscribers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracasts:send-upgrade-coupon-to-new-monthly-subscribers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a couple to monthly subscribers to upgrade';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        //whereDay() changed this too WhereDate() as the date the user was created and if 3 months subs
        //whereDay is today onwards
       $emails = User::activeSubscribers()
            ->subscribedOn(today()->subMonths(3))
            ->onPlan('monthly-15')
            ->get()
            ->each
            ->email(function ($user) {
                   return new UpgradeToYearly($user, $this->makeCoupon());
            })->pluck('email');

        $this->info(
            "Finished sent upgrade discounts to:  " .
            $emails->implode(', ')
        );
    }

    private function makeCoupon(): Coupon
    {
        return CouponFactory::generate([
           'code' => Str::random(25),
           'percentage_discount' => 10,
           'description' => 'Upgrade to yearly',
           'duration' => 'once',
        ], resolve(Gateway::class));
    }
}
