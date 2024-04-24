<?php

namespace Tests\Unit\Jobs;

use App\Jobs\GPTSeeder\ProgramsFill;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\Program;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Laravel\Facades\OpenAI;

class ProgramsFillTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Fill programs with a job
     */
    public function test_fill_programs_with_job(): void
    {
        // Fake OpenAI response
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => "{ \"programs\": [ { \"title\": \"Virtual Reality Workshop\", \"description\": \"Explore the exciting world of virtual reality technology in this hands-on workshop. Learn about VR hardware, software, and applications.\", \"start_date\": \"2024-05-10T09:00:00\", \"end_date\": \"2024-05-12T17:00:00\" }, { \"title\": \"Mobile App Development Bootcamp\", \"description\": \"Join us for an intensive bootcamp where you'll learn to develop mobile apps for iOS and Android platforms. No prior experience required!\", \"start_date\": \"2024-06-15T10:30:00\", \"end_date\": \"2024-06-20T16:00:00\" }, { \"title\": \"Data Science Masterclass\", \"description\": \"Unlock the power of data with our comprehensive masterclass. From data analysis to machine learning, we cover it all!\", \"start_date\": \"2024-07-08T13:00:00\", \"end_date\": \"2024-07-12T18:30:00\" }, { \"title\": \"Photography Workshop\", \"description\": \"Capture the world through your lens! Join our photography workshop to learn techniques for composition, lighting, and editing.\", \"start_date\": \"2024-08-03T11:00:00\", \"end_date\": \"2024-08-05T15:45:00\" }, { \"title\": \"Web Development Crash Course\", \"description\": \"Get up to speed with web development in this intensive crash course. Learn HTML, CSS, JavaScript, and more in just one week!\", \"start_date\": \"2024-09-20T09:30:00\", \"end_date\": \"2024-09-24T16:15:00\" }, { \"title\": \"Digital Marketing Seminar\", \"description\": \"Stay ahead in the digital age with our seminar on digital marketing strategies. From SEO to social media, we've got you covered!\", \"start_date\": \"2024-10-12T14:00:00\", \"end_date\": \"2024-10-13T18:00:00\" }, { \"title\": \"Creative Writing Workshop\", \"description\": \"Unleash your creativity with our interactive writing workshop. Explore various genres and techniques to craft compelling stories.\", \"start_date\": \"2024-11-05T10:00:00\", \"end_date\": \"2024-11-08T16:30:00\" }, { \"title\": \"UI/UX Design Bootcamp\", \"description\": \"Design intuitive and user-friendly interfaces in our UI/UX design bootcamp. Learn industry-standard tools and best practices.\", \"start_date\": \"2024-12-01T09:30:00\", \"end_date\": \"2024-12-05T17:00:00\" }, { \"title\": \"Blockchain Fundamentals Workshop\", \"description\": \"Dive into the world of blockchain technology and cryptocurrencies in this introductory workshop. Understand the basics and potential applications.\", \"start_date\": \"2025-01-10T13:30:00\", \"end_date\": \"2025-01-12T16:45:00\" }, { \"title\": \"Artificial Intelligence Summit\", \"description\": \"Join industry experts and researchers at our AI summit. Explore the latest advancements, trends, and ethical considerations in AI.\", \"start_date\": \"2025-02-20T10:00:00\", \"end_date\": \"2025-02-22T17:30:00\" } ] }",
                        ]
                    ],
                ],
            ]),
        ]);

        // Ensure that no programs are registered
        $this->assertEquals(0, Program::count());

        // Dispatch job
        ProgramsFill::dispatchSync();

        // Assert 10 programs registered
        $this->assertEquals(10, Program::count());
    }
}
