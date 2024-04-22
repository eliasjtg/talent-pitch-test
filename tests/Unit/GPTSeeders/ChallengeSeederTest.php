<?php

namespace Tests\Unit\GPTSeeders;

use App\Models\Challenge;
use Database\Seeders\GPTChallengeSeeder;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ChallengeSeederTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Run gpt challenge seeder
     */
    public function test_run_gpt_challenge_seeder(): void
    {
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => "{ \"challenges\": [ { \"title\": \"Mountain Climbing Adventure\", \"description\": \"Conquer the tallest peak in the region\", \"difficulty\": 9 }, { \"title\": \"Survival Escape Room\", \"description\": \"Test your skills in a series of challenging puzzles\", \"difficulty\": 7 }, { \"title\": \"Cross-Country Cycling Challenge\", \"description\": \"Navigate rugged terrain on a long-distance bike ride\", \"difficulty\": 6 }, { \"title\": \"Wilderness Survival Camp\", \"description\": \"Learn to survive in the great outdoors without modern amenities\", \"difficulty\": 8 }, { \"title\": \"Underwater Scuba Adventure\", \"description\": \"Explore the depths of the ocean and encounter marine life\", \"difficulty\": 5 }, { \"title\": \"Parkour Urban Challenge\", \"description\": \"Navigate through urban obstacles with agility and speed\", \"difficulty\": 7 }, { \"title\": \"White Water Rafting Expedition\", \"description\": \"Conquer rapids and rough waters in a thrilling rafting trip\", \"difficulty\": 9 }, { \"title\": \"Extreme Skydiving Experience\", \"description\": \"Leap from a plane and freefall through the clouds\", \"difficulty\": 8 }, { \"title\": \"Rock Climbing Competition\", \"description\": \"Compete against other climbers in a challenging ascent\", \"difficulty\": 7 }, { \"title\": \"Kayaking Adventure Race\", \"description\": \"Race against the clock and other competitors in a fast-paced kayak race\", \"difficulty\": 6 } ] }",
                        ]
                    ],
                ],
            ]),
        ]);

        $this->assertEquals(0, Challenge::count());

        $this->seed(GPTChallengeSeeder::class);

        $this->assertEquals(10, Challenge::count());
    }
}
