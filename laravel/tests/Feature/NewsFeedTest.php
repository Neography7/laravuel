<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\UserPreference;
use PHPUnit\Framework\Attributes\Test;

class NewsFeedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_a_user_can_get_personalized_news_feed()
    {
        $user = User::factory()->create();

        UserPreference::create([
            'user_id' => $user->id,
            'categories' => ['technology', 'health']
        ]);

        $response = $this->actingAs($user)->getJson('/api/newsfeed');

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
    public function test_a_user_without_preferences_gets_random_news_feed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/newsfeed');

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
}
