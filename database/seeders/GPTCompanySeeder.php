<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * Seed company using GPT API
 */
class GPTCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $query = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => 'Generate a json file with key companies that has 10 fake companies. Create some fake attributes about the company like name, image_path (url), location and industry'],
            ],
        ]);
        
        $result = json_decode($query->choices[0]->message->content, true);

        $companies = $result['companies'];

        foreach ($companies as $company) {
            $newCompany = Company::make($company);
            $newCompany->user()->associate(User::factory()->create());
            $newCompany->save();
        }
    }
}
