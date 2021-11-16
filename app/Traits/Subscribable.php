<?php
declare(strict_types=1);
    namespace App\Traits;

    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Support\Facades\Mail;

    trait Subscribable
    {
        /**
         * get active members from Users
         * @param  Builder  $query
         */
        public function scopeActiveSubscribers(Builder $query)
        {
            $query->where('active', true);
        }

        /**
         * get monthly 15 subscribers
         * @param  Builder  $query
         */
        public function scopeOnPlan(Builder $query, string $plan)
        {
            $query->where('stripe_plan', $plan);
        }

        public function scopeSubscribedOn($query, $date = null)
        {
            $date = $date ?? today()->subWeek();

            $query->where('created_at', $date);
        }

        public function email($callback)
        {
            return Mail::to($this)->send($callback($this));
        }
    }
