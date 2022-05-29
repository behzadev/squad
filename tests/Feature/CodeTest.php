<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CodeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_should_be_able_to_add_a_new_code()
    {
        // when we send a new request to add a new code
        $response = $this->json(
            'POST',
            route('api.v1.codes.store'),
            [
                'value' => 'test',
                'count_init' => 1000,
            ]
        );

        // then it must be available in db
        $this->assertDatabaseHas('codes', [
            'value' => 'test',
            'count_init' => 1000,
            'count_left' => 1000,
        ]);

        // and response code should be 201 with code details in return
        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'status' => 'success',
                'code' => [
                    'id' => 1,
                    'value' => 'test',
                    'count_init' => 1000,
                    'count_left' => 1000,
                ],
            ]);
    }

    /**
     * @test
     */
    public function it_should_not_allow_to_add_multiple_codes_with_same_string()
    {
        // given we already have a code with value of test
        \App\Models\Code::factory(1)->create([
            'value' => 'test',
        ]);

        // when we try to add the same code
        $response = $this->json(
            'POST',
            route('api.v1.codes.store'),
            [
                'value' => 'test',
                'count_init' => 1000,
            ]
        );

        // response code must be 422
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function it_should_not_allow_to_add_a_code_with_wrong_count_init()
    {
        // when we try to add a code with wrong count_init
        $response = $this->json(
            'POST',
            route('api.v1.codes.store'),
            [
                'value' => 'test',
                'count_init' => -1,
            ]
        );

        // response code must be 422
        $response->assertStatus(422);
    }
}
