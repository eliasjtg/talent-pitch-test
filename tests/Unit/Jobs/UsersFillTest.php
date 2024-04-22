<?php

namespace Tests\Unit\Jobs;

use App\Jobs\GPTSeeder\UsersFill;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\User;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Laravel\Facades\OpenAI;

class UsersFillTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Fill users with a job
     */
    public function test_fill_users_with_job(): void
    {
        // Fake OpenAI response
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

        // Ensure that no users are registered
        $this->assertEquals(0, User::count());

        // Dispatch job
        UsersFill::dispatchSync();

        // Assert 10 users registered
        $this->assertEquals(10, User::count());
    }
}
