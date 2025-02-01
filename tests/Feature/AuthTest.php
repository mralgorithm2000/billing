<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'messaged' => 'Registered',
                 ]);
    }

    /** @test */
    public function register_requires_valid_data()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
    }

     /** @test */
     public function user_can_login()
     {
         // Create a user first
         $user = User::factory()->create([
             'password' => bcrypt('password123'),
         ]);
 
         $response = $this->postJson('/api/login', [
             'email' => $user->email,
             'password' => 'password123',
         ]);
 
         $response->assertStatus(200)
                  ->assertJsonStructure([
                      'success',
                      'messaged',
                      'token',
                  ]);
     }
 
     /** @test */
     public function login_requires_valid_credentials()
     {
         $response = $this->postJson('/api/login', [
             'email' => 'nonexistent@example.com',
             'password' => 'wrongpassword',
         ]);
 
         $response->assertStatus(401)
                  ->assertJson([
                      'success' => false,
                      'messaged' => 'The email or password is incorrect',
                  ]);
     }
}
