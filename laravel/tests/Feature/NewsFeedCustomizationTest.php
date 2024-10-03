<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class NewsFeedCustomizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_customize_their_news_feed_preferences()
    {

        $user = User::factory()->create();

        $categories = ['technology', 'health'];
        $sources    = ["newsapi", "guardian", "nytimes"];
        $authors    = ['Author 1', 'Author 2'];

        $response = $this->actingAs($user)->postJson('/api/user/preferences', [
            'categories' => $categories,
            'sources'    => $sources,
            'authors'    => $authors
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $user->id,
        ]);

        $userPreference = \App\Models\UserPreference::where('user_id', $user->id)->first();

        $this->assertEquals($categories, $userPreference->categories);
        $this->assertEquals($sources, $userPreference->sources);
        $this->assertEquals($authors, $userPreference->authors);
    }

    #[Test]
    public function a_user_can_get_their_news_feed_preferences()
    {

        $user = User::factory()->create();

        $categories = ['technology', 'health'];
        $sources    = ["newsapi", "guardian", "nytimes"];
        $authors    = ['Author 1', 'Author 2'];

        $response = $this->actingAs($user)->postJson('/api/user/preferences', [
            'categories' => $categories,
            'sources'    => $sources,
            'authors'    => $authors
        ]);

        $response = $this->actingAs($user)->getJson('/api/user/preferences');

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Preferences getted successfully.',
            'data' => [
                [
                    'categories' => $categories,
                    'sources'    => $sources,
                    'authors'    => $authors
                ]
            ]
        ]);
    }
}
