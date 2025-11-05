<?php

namespace App\Jobs;

use App\Services\ParsGreenService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendResetPass implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $mobile_nu,
        public string $user_name,
        public string $pass
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
        $sms->sendResetPassword($this->mobile_nu, $this->user_name, $this->pass);
    }
}
