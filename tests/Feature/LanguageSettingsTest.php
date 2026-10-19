<?php

use App\Models\User;

test('a user can select French as their language preference', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('settings.update'), [
            'timezone' => 'Africa/Nairobi',
            'language' => 'fr',
            'currency' => 'KES',
            'appearance' => 'light',
        ])
        ->assertRedirect(route('settings.index'))
        ->assertSessionHas('language', 'fr');

    expect($user->fresh()->language)->toBe('fr');
});

test('a user can save a dark appearance preference', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('settings.update'), [
            'timezone' => 'Africa/Nairobi',
            'language' => 'en',
            'currency' => 'KES',
            'appearance' => 'dark',
        ])
        ->assertRedirect(route('settings.index'));

    expect($user->fresh()->appearance)->toBe('dark');

    $this->actingAs($user)
        ->get('/settings')
        ->assertOk()
        ->assertSee('data-theme="dark"', false)
        ->assertSee('value="dark" selected', false);
});

test('the saved French preference is applied on subsequent requests', function () {
    $user = User::factory()->create(['language' => 'fr']);

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertSee('Informations du profil');
});

test('legacy page labels are translated when French is selected', function () {
    $user = User::factory()->create(['language' => 'fr']);

    $this->actingAs($user)
        ->get('/settings')
        ->assertOk()
        ->assertSee('Langue et devise')
        ->assertSee('Enregistrer les paramètres');
});
