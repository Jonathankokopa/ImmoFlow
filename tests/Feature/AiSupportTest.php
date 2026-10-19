<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

test('the AI support endpoint returns an assistant response', function () {
    config()->set('services.openai.api_key', 'test-key');

    Http::fake([
        'api.openai.com/*' => Http::response([
            'output' => [[
                'content' => [[
                    'type' => 'output_text',
                    'text' => 'Open Properties and select Add Property.',
                ]],
            ]],
        ]),
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('support.ai'), [
            'message' => 'How do I add a property?',
        ])
        ->assertOk()
        ->assertJson(['answer' => 'Open Properties and select Add Property.']);
});
