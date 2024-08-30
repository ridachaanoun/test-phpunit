<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Create roles for testing
        Role::create(['name' => 'Admin', 'description' => 'Administrator role']);
        Role::create(['name' => 'User', 'description' => 'Regular user role']);
    }

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'username' => 'testuser',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'User registered successfully',
                 ]);

        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
        ]);
    }

    public function test_user_cannot_register_with_existing_username()
    {
        User::factory()->create(['username' => 'testuser']);

        $response = $this->postJson('/api/auth/register', [
            'username' => 'testuser',
            'password' => 'password',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => false,
                     'message' => 'Validation error',
                 ]);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'username' => 'testuser',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'User logged in successfully',
                 ]);

        $this->assertNotNull($response->json('access_token'));
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'username' => 'wronguser',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => false,
                     'message' => 'Wrong username and/or password',
                 ]);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password'),
        ]);

        $token = $user->createToken('token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/auth/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'User logged out successfully',
                 ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }
}
