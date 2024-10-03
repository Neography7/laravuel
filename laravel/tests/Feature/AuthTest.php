<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_register(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '123456',
            'password_confirmation' => '123456'
        ];

        $response = $this->postJson('/api/register', $data);

        // Başarılı kayıt olduğunda 201 status döner.
        $response->assertStatus(201);

        // Veritabanına kaydedildiğini kontrol et
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    #[Test]
    public function a_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password'
        ];

        $response = $this->postJson('/api/login', $credentials);

        // Giriş başarılıysa 200 status kodu ve token döner
        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    #[Test]
    public function a_user_can_get_their_profile_data(): void
    {
        $user = User::factory()->create();

        // Kullanıcıyı yetkilendir ve token oluştur
        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/profile');

        // Profil bilgilerini almak için 200 status kodu döner
        $response->assertStatus(200);

        // Yanıt JSON formatında kullanıcı bilgilerini içermeli
        $response->assertJson([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
