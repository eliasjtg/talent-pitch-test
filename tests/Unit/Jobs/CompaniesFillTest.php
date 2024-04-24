<?php

namespace Tests\Unit\Jobs;

use App\Jobs\GPTSeeder\CompaniesFill;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\Company;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Laravel\Facades\OpenAI;

class CompaniesFillTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Fill companies with a job
     */
    public function test_fill_companies_with_job(): void
    {
        // Fake OpenAI response
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => "{ \"companies\": [ { \"name\": \"Innovatech Solutions\", \"image_path\": \"https://example.com/images/innovatech_logo.png\", \"location\": \"New York, USA\", \"industry\": \"Information Technology\" }, { \"name\": \"GlobeTech Enterprises\", \"image_path\": \"https://example.com/images/globetech_logo.png\", \"location\": \"London, UK\", \"industry\": \"Telecommunications\" }, { \"name\": \"Vivid Dynamics Inc.\", \"image_path\": \"https://example.com/images/vividdynamics_logo.png\", \"location\": \"Los Angeles, USA\", \"industry\": \"Entertainment\" }, { \"name\": \"Apex Innovations Co.\", \"image_path\": \"https://example.com/images/apexinnovations_logo.png\", \"location\": \"Tokyo, Japan\", \"industry\": \"Consumer Electronics\" }, { \"name\": \"Meridian Solutions Ltd.\", \"image_path\": \"https://example.com/images/meridiansolutions_logo.png\", \"location\": \"Sydney, Australia\", \"industry\": \"Financial Services\" }, { \"name\": \"TechNova Industries\", \"image_path\": \"https://example.com/images/technova_logo.png\", \"location\": \"Berlin, Germany\", \"industry\": \"Software Development\" }, { \"name\": \"Fusion Innovations Group\", \"image_path\": \"https://example.com/images/fusioninnovations_logo.png\", \"location\": \"Toronto, Canada\", \"industry\": \"Renewable Energy\" }, { \"name\": \"AlphaWave Technologies\", \"image_path\": \"https://example.com/images/alphawave_logo.png\", \"location\": \"Seoul, South Korea\", \"industry\": \"Artificial Intelligence\" }, { \"name\": \"Astra Global Solutions\", \"image_path\": \"https://example.com/images/astraglobal_logo.png\", \"location\": \"Paris, France\", \"industry\": \"Aerospace\" }, { \"name\": \"NexGen Innovations\", \"image_path\": \"https://example.com/images/nexgen_logo.png\", \"location\": \"Shanghai, China\", \"industry\": \"Biotechnology\" } ] }",
                        ]
                    ],
                ],
            ]),
        ]);

        // Ensure that no companies are registered
        $this->assertEquals(0, Company::count());

        // Dispatch job
        CompaniesFill::dispatchSync();

        // Assert 10 companies registered
        $this->assertEquals(10, Company::count());
    }
}
