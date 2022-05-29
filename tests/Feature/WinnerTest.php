<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Jobs\IsWinner;
use Illuminate\Support\Facades\Bus;
use Illuminate\Foundation\Testing\WithFaker;
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
}
