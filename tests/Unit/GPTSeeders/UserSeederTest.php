<?php

namespace Tests\Unit\GPTSeeders;

use App\Models\User;
use Database\Seeders\GPTUserSeeder;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Run gpt user seeder
     */
    public function test_run_gpt_user_seeder(): void
    {
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => "{ \"users\": [ { \"name\": \"John Doe\", \"email\": \"johndoe@example.com\", \"image_path\": \"https://example.com/images/johndoe.jpg\" }, { \"name\": \"Jane Smith\", \"email\": \"janesmith@example.com\", \"image_path\": \"https://example.com/images/janesmith.jpg\" }, { \"name\": \"Mike Johnson\", \"email\": \"mikejohnson@example.com\", \"image_path\": \"https://example.com/images/mikejohnson.jpg\" }, { \"name\": \"Emily Brown\", \"email\": \"emilybrown@example.com\", \"image_path\": \"https://example.com/images/emilybrown.jpg\" }, { \"name\": \"David Wilson\", \"email\": \"davidwilson@example.com\", \"image_path\": \"https://example.com/images/davidwilson.jpg\" }, { \"name\": \"Sarah Miller\", \"email\": \"sarahmiller@example.com\", \"image_path\": \"https://example.com/images/sarahmiller.jpg\" }, { \"name\": \"Chris Martinez\", \"email\": \"chrismartinez@example.com\", \"image_path\": \"https://example.com/images/chrismartinez.jpg\" }, { \"name\": \"Amy Lee\", \"email\": \"amylee@example.com\", \"image_path\": \"https://example.com/images/amylee.jpg\" }, { \"name\": \"Kevin Nguyen\", \"email\": \"kevinnguyen@example.com\", \"image_path\": \"https://example.com/images/kevinnguyen.jpg\" }, { \"name\": \"Jessica Taylor\", \"email\": \"jessicataylor@example.com\", \"image_path\": \"https://example.com/images/jessicataylor.jpg\" } ] }",
                        ]
                    ],
                ],
            ]),
        ]);

        $this->assertEquals(0, User::count());

        $this->seed(GPTUserSeeder::class);

        $this->assertEquals(10, User::count());
    }
}
