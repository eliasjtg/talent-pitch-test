<?php

namespace Tests\Feature;


use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class WebTest extends TestCase
{
    /**
     * Test for web valid response
     */
    public function test_the_web_returns_a_successful_response(): void
    {
        // Call home page
        $response = $this->get('/');

        // Validate successful response
        $response->assertStatus(200);
    }

    /**
     * Test web for header exists
     */
    public function test_the_web_contains_header(): void
    {
        // Call home page
        $response = $this->get('/');

        // Find the header
        $header = $response->crawl()->filter('header');

        $this->assertNotEmpty($header, 'The header was not found!');
    }

    /**
     * Test web for header frequent questions link
     */
    public function test_the_web_header_contains_frequent_questions_link(): void
    {
        // Call home page
        $response = $this->get('/');

        // Find the header frequent questions link
        $links = $response->crawl()
            ->filterXPath('//header/nav/div/div/a')
            ->reduce(function (Crawler $node) {
                return $node->attr('href') === '#body' && $node->text() === 'Preguntas frecuentes';
            });

        $this->assertNotEmpty($links, 'The header doesn\'t contain frequent questions link!');
    }

    /**
     * Test web for header features link
     */
    public function test_the_web_header_contains_features_link(): void
    {
        // Call home page
        $response = $this->get('/');

        // Find the header features link
        $links = $response->crawl()
            ->filterXPath('//header/nav/div/div/a')
            ->reduce(function (Crawler $node) {
                return $node->attr('href') === '#features' && $node->text() === 'Funcionalidades';
            });

        $this->assertNotEmpty($links, 'The header doesn\'t contain features link!');
    }

    /**
     * Test web for header image
     */
    public function test_the_web_header_image(): void
    {
        // Call home page
        $response = $this->get('/');

        // Find the header image
        $headerImages = $response->crawl()
            ->filterXPath('//header/nav/a/img')
            ->reduce(function (Crawler $node) {
                return $node->attr('src') === asset('assets/favicon/favicon-32x32.png');
            });

        $this->assertNotEmpty($headerImages, 'The header doesn\'t contain image!');
    }

    /**
     * Test web for frequent questions section
     */
    public function test_the_web_frequent_questions_section(): void
    {
        // Call home page
        $response = $this->get('/');

        // Find the frequent questions section
        $sections = $response->crawl()
            ->filter('h2')
            ->reduce(function (Crawler $node) {
                return $node->text() === 'Preguntas frecuentes';
            });

        $this->assertNotEmpty($sections, 'The page doesn\'t contain frequent questions section!');
    }

    /**
     * Test web for features section
     */
    public function test_the_web_features_section(): void
    {
        // Call home page
        $response = $this->get('/');

        // Find the features section
        $sections = $response->crawl()
            ->filter('h2')
            ->reduce(function (Crawler $node) {
                return $node->text() === 'Funcionalidades';
            });

        $this->assertNotEmpty($sections, 'The page doesn\'t contain features section!');
    }

    /**
     * Test web for exact frecuent questions cards
     */
    public function test_the_web_header_contains_exact_frecuent_questions_cards(): void
    {
        // Call home page
        $response = $this->get('/');

        // Find the frecuent questions card
        $cards = $response->crawl()
            ->filterXPath('//div[@id="frequent-questions"]/div/div/dl/div/dt');

        // Asset 9 cards count
        $this->assertCount(9, $cards);
    }

    /**
     * Test web for exact features cards
     */
    public function test_the_web_header_contains_exact_features_cards(): void
    {
        // Call home page
        $response = $this->get('/');

        // Find the features card
        $cards = $response->crawl()
            ->filterXPath('//div[@id="features"]/div/dl/div/dt');

        // Asset 9 cards count
        $this->assertCount(9, $cards);
    }
}
