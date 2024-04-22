<?php

namespace App\Jobs\GPTSeeder;

use Database\Seeders\GPTChallengeSeeder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChallengesFill implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /**
         * @var GPTChallengeSeeder $seeder
         */
        $seeder = app(GPTChallengeSeeder::class);

        $seeder->run();
    }
}
