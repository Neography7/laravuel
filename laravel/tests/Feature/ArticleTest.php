<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ArticleTest extends TestCase
{

    #[Test]
    public function test_user_can_search_for_articles()
    {

        $response = $this->getJson('/api/articles/search?query=technology');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'articles' => [
                '*' => [
                    'title',
                    'description',
                    'published_at',
                    'url',
                    'author',
                    'source',
                    'image',
                    'api'
                ]
            ]
        ]);
    }

    #[Test]
    public function test_article_search_pagination_works()
    {

        $response = $this->getJson('/api/articles/search?query=tech&page=2');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'articles' => [
                '*' => [
                    'title',
                    'description',
                    'published_at',
                    'url',
                    'author',
                    'source',
                    'image',
                    'api'
                ]
            ]
        ]);
    }

    #[Test]
    public function test_search_without_query_fails()
    {
        $response = $this->getJson('/api/articles/search');

        $response->assertStatus(400);
    }
}
