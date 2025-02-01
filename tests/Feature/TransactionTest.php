<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_make_a_deposit()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/user/transaction/deposit', [
            'amount' => 100.00,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Your deposit request is being processed.',
                 ]);
    }

    /** @test */
    public function deposit_requires_valid_amount()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/user/transaction/deposit', [
            'amount' => -50.00,
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors',
                 ]);
    }


    /** @test */
    public function user_can_make_a_withdraw()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/user/transaction/withdraw', [
            'amount' => 50.00,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Your withdraw request is being processed.',
                 ]);
    }

    /** @test */
    public function withdraw_requires_valid_amount()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/user/transaction/withdraw', [
            'amount' => -50.00,
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors',
                 ]);
    }

    /** @test */
    public function user_can_get_transaction_history()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/user/transaction/history?page_limit=10');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'content' => [
                         '*' => [
                             'type',
                             'amount',
                             'description',
                             'created_at',
                             'updated_at',
                         ],
                     ],
                 ]);
    }
}
