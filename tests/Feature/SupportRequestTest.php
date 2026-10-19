<?php

use App\Models\SupportRequest;
use App\Models\User;

test('an authenticated user can submit a support request', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('support.store'), [
            'category' => 'technical',
            'subject' => 'Unable to save a property',
            'message' => 'The save button does not complete the property form submission.',
        ])
        ->assertRedirect(route('support.index'));

    $this->assertDatabaseHas('support_requests', [
        'user_id' => $user->id,
        'category' => 'technical',
        'subject' => 'Unable to save a property',
        'status' => 'open',
    ]);
});
