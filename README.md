## Dependencies

- PHP 8.3

- Postgresql 16

- Node 21

- GPT Organization ID and API KEY

## Installation
 
##### Install dependencies:  
  
```bash  
# Install PHP dependencies  
$ composer install
  
# Install Node dependencies 
$ npm install
``` 

##### Setup FrontEnd assets:  
  
```bash  
# Compile assets
$ npm run build
```

##### Configure environment:  

 - Setup all ***DB_*** environment with the database connection.
 - Setup **OPENAI_ORGANIZATION** and **OPENAI_API_KEY** from your OpenAI dashboard, at [https://openai.com](https://openai.com/)
 - Setup all others environments such APP_URL and any other from your environment

###### Configure storage:

```bash  
# Setup storage link
$ php artisan storage:link
``` 

## Run

###### Run tests:

```bash  
# Run PHPUnit tests  
$ ./vendor/bin/phpunit --testdox
``` 

###### Run project in dev mode:

```bash  
# Run laravel
$ php artisan serve
``` 
 
###### Seed with OpenAI in the CLI:

```bash  
# Seed all models
$ php artisan db:seed

or

# Seed individually
$ php artisan db:seed --class=GPTUserSeeder
$ php artisan db:seed --class=GPTChallengeSeeder
$ php artisan db:seed --class=GPTCompanySeeder
$ php artisan db:seed --class=GPTProgramSeeder
``` 

## Resources

##### Routes:

Example: Challenge

- Fill challenges with GPT: `POST /api/challenges/gpt`
- List challenges: `GET /api/challenges`
- Create challenge: `POST /api/challenges/{user_id}`
- Read challenge: `GET /api/challenges/{id}`
- Update challenge: `PATCH /api/challenges/{id}`
- Delete challenge: `DELETE /api/challenges/{id}`

![Routes](./storage/app/doc/routes.png "Routes")

##### Fill with GPT:

After fill with GPT, every entity list will show 10 new records.