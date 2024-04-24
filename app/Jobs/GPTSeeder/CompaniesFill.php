<?php

namespace App\Jobs\GPTSeeder;

use Database\Seeders\GPTCompanySeeder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CompaniesFill implements ShouldQueue
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
         * @var GPTCompanySeeder $seeder
         */
        $seeder = app(GPTCompanySeeder::class);

        $seeder->run();
    }
}
