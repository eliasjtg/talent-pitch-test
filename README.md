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
``` 