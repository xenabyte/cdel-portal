<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Guardian;
use App\Mail\GuardianOnboardingMail;

use Mail;

class SendGuardianOnboardingMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $guardian;

    public function __construct(Guardian $guardian)
    {
        $this->guardian = $guardian;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::to($this->guardian->email)->send(new GuardianOnboardingMail($this->guardian));
        } catch (\Exception $e) {
            Log::error('Job failed: ' . $e->getMessage());
        }
    }
}
