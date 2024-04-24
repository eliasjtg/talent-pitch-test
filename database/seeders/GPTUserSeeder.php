<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * Seed users using GPT API
 */
class GPTUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $query = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => 'Generate a json file with key users that has 10 fake users. Create some fake attributes about the users like their name, email and image_path (url)'],
            ],
        ]);
        
        $result = json_decode($query->choices[0]->message->content, true);

        $users = $result['users'];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
