<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Jobs\IsWinner;
use Illuminate\Support\Facades\Bus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WinnerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_should_dispatch_is_winner_job_on_a_new_message()
    {
        Bus::fake();

        // given our endpoint gets hit by the sms provider with a new message
        $this->json(
            'POST',
            route('api.v1.winners.store'),
            [
                'number' => '09122384604',
                'message' => 'test'
            ]
        );

        // then IsWinner job should be dispatched
        Bus::assertDispatched(IsWinner::class);
    }

    /**
     * @test
     */
    public function it_should_add_winner_if_code_is_available_and_has_count_left_more_than_zero()
    {
        // given we have a working code
        $code = \App\Models\Code::factory(1)->create([
            'value' => 'test',
            'count_init' => 1000,
            'count_left' => 500
        ])->first();

        // when we get hit by sms service provider with a new message
        $response = $this->json(
            'POST',
            route('api.v1.winners.store'),
            [
                'number' => '09122384604',
                'message' => 'test'
            ]
        );

        // then a new winner must be added to db
        $this->assertDatabaseHas('winners', [
            'cell_number' => '09122384604',
            'code_id' => $code->id,
        ]);

        // and we should get a response with status code of 200
        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'status' => 'success',
            ]);
    }

    /**
     * @test
     */
    public function it_should_not_allow_requests_with_invalid_cell_numbers()
    {
        // when we get a new message with wrong number
        $response = $this->json(
            'POST',
            route('api.v1.winners.store'),
            [
                'number' => 'XXX',
                'message' => 'test'
            ]
        );

        // then response code should be 422
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function it_should_not_allow_same_cell_number_to_win_more_than_once()
    {
        // given we already have a winner with cell number of 09122384604 and a working code
        $code = \App\Models\Code::factory(1)->create([
            'value' => 'test',
            'count_init' => 1000,
            'count_left' => 500
        ])->first();

        \App\Models\Winner::factory(1)->create([
            'cell_number' => '09122384604',
            'code_id' => $code->id
        ]);

        // when we get a new message with the same number
        $response = $this->json(
            'POST',
            route('api.v1.winners.store'),
            [
                'number' => '09122384604',
                'message' => 'test'
            ]
        );

        // then we should still have 1 winner with number of 09122384604 in database
        $this->assertEquals(1, \App\Models\Winner::where('cell_number', '09122384604')->count());

        // and the response code should be 200
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function it_should_not_add_a_new_winner_if_code_count_left_is_zero()
    {
        // given we have a code with count_left of 0
        $code = \App\Models\Code::factory(1)->create([
            'value' => 'test',
            'count_init' => 1000,
            'count_left' => 0
        ])->first();

        // when we get a new message
        $response = $this->json(
            'POST',
            route('api.v1.winners.store'),
            [
                'number' => '09122384604',
                'message' => 'test'
            ]
        );

        // then we should still have 0 winners in database
        $this->assertEquals(0, \App\Models\Winner::count());

        // and the response code should be 200
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function it_should_deduct_number_of_count_left_of_code_if_winner_is_added()
    {
        // given we have a code with count_left of 1000
        $code = \App\Models\Code::factory(1)->create([
            'value' => 'test',
            'count_init' => 1000,
            'count_left' => 1000
        ])->first();

        // when we get a new message
        $response = $this->json(
            'POST',
            route('api.v1.winners.store'),
            [
                'number' => '09122384604',
                'message' => 'test'
            ]
        );

        // then we should have count_left of 999
        $this->assertEquals(999, $code->fresh()->count_left);

        // and the response code should be 200
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function it_should_recognize_numbers_which_won()
    {
        // given we have a winner
        $code = \App\Models\Code::factory(1)->create([
            'value' => 'test',
            'count_init' => 1000,
            'count_left' => 500
        ])->first();

        \App\Models\Winner::factory(1)->create([
            'cell_number' => '09122384604',
            'code_id' => $code->id
        ]);

        // when we query database to check if he's a winner
        $response = $this->json(
            'POST',
            route('api.v1.winners.query'),
            [
                'cell_number' => '09122384604',
                'code' => 'test'
            ]
        );

        // then is_winner should be true with response code should be 200 with
        $response
            ->assertJsonFragment([
                'is_winner' => true
            ])
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function it_should_recognize_numbers_which_did_not_win()
    {
        // given we have no winners

        // when we query to check if a random number is winner
        $response = $this->json(
            'POST',
            route('api.v1.winners.query'),
            [
                'cell_number' => '09122384604',
                'code' => 'test'
            ]
        );

        // then is_winner should be false with response code should be 200 with
        $response
            ->assertJsonFragment([
                'is_winner' => false
            ])
            ->assertStatus(200);
    }
}
