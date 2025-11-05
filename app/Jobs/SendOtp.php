<?php

namespace App\Jobs;

use App\Services\ParsGreenService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendOtp implements ShouldQueue
{
    use Queueable;
    public function __construct(
        public string $mobile_nu,
        public string $otp
    )
    {
       //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $sms = new ParsGreenService();
        $sms->sendOtp(mobile: $this->mobile_nu, otp: $this->otp);
    }
}
