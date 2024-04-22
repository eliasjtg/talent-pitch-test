<?php

namespace Database\Seeders;

use App\Models\Challenge;
use App\Models\User;
use Illuminate\Database\Seeder;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * Seed challenges using GPT API
 */
class GPTChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $query = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => 'Generate a json file that has 10 fake challenges. Create some fake attributes about the challenge like title, description and difficulty (from 1 to 10)'],
            ],
        ]);
        
        $result = json_decode($query->choices[0]->message->content, true);

        \Log::info($query->choices[0]->message->content);

        $challenges = $result['challenges'];

        foreach ($challenges as $challenge) {
            $newChallenge = Challenge::make($challenge);
            $newChallenge->user()->associate(User::factory()->create());
            $newChallenge->save();
        }
    }
}
