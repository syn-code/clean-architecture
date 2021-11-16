<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Coupon\Coupon;

class UpgradeToYearly extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Subject of the email
     * @var string
     */
    public $subject = 'Upgrade Your Laracast Account for 10% Off';
    /**
     * The user in question
     * @var User
     */
    public $user;
    /**
     * @var Coupon
     */
    private $coupon;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Coupon $coupon)
    {
        $this->user = $user;
        $this->coupon = $coupon;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): UpgradeToYearly
    {
        return $this->markdown('emails.upgrade-to-yearly');
    }
}
