<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class userTest extends TestCase
{
    use RefreshDatabase;

     /** @test */
    public function user_can_get_their_info()
    {
         $user = User::factory()->create();
         $this->actingAs($user);
 
         $response = $this->getJson('/api/user/info');
 
         $response->assertStatus(200)
                  ->assertJsonStructure([
                      'success',
                      'content' => [
                          'id',
                          'name',
                          'email',
                          'created_at',
                          'updated_at',
                          'balance',
                      ],
                  ]);
     }
}
