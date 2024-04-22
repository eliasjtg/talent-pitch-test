<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;
use Symfony\Component\DomCrawler\Crawler;
use PHPUnit\Framework\Assert as PHPUnit;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        TestResponse::macro('crawl', function(?callable $callback = null): Crawler {
            if (empty($content = $this->getContent())) {
                PHPUnit::fail('The HTTP response is empty.');
            }
     
            $callback ??= fn ($c): Crawler => $c;
     
            return call_user_func($callback, new Crawler($content));
        });
    }
}
