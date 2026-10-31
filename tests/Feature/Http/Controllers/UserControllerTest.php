<?php

use App\Models\Quote;
use App\Models\User;

it("lists only the member's own quotes on their public page", function () {
    $member = User::factory()->create(['username' => 'nora']);
    Quote::factory()->for($member)->create(['content' => 'Words kept by Nora.']);
    Quote::factory()->create(['content' => 'Words kept by someone else.']);

    $response = $this->get(route('users.show', 'nora'));

    $response->assertOk()
        ->assertSee('Words kept by Nora.')
        ->assertDontSee('Words kept by someone else.');
});

it('returns 404 for a username that does not exist', function () {
    $response = $this->get('/u/nobody_here');

    $response->assertNotFound();
});
