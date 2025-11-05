<?php

namespace App\Jobs;

use App\Services\ParsGreenService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendPass implements ShouldQueue
{
    use Queueable;
    public function __construct(
        public string $mobile_nu,
        public string $n_code,
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
        $sms->sendPassword($this->mobile_nu, $this->n_code, $this->pass);
    }
}
