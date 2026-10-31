<?php

use App\Models\Quote;
use App\Models\User;

describe('index', function () {
    it('shows guests each quote with its text, publisher and date', function () {
        $publisher = User::factory()->create(['name' => 'نورة المطوع']);
        Quote::factory()->for($publisher)->create([
            'content' => 'Words that deserve to last.',
            'created_at' => '2026-10-15 10:00:00',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('Words that deserve to last.')
            ->assertSee('نورة المطوع')
            ->assertSee('15 أكتوبر 2026');
    });

    it('features a quote of the day above the collection', function () {
        Quote::factory()->create(['content' => 'The only quote in the notebook.']);

        $response = $this->get(route('home'));

        $response->assertOk()->assertSeeInOrder([
            'اقتباس اليوم',
            'The only quote in the notebook.',
            'من الكُنّاشة',
            'The only quote in the notebook.',
        ]);
    });

    it('hides the quote of the day while the notebook is empty', function () {
        $response = $this->get(route('home'));

        $response->assertOk()->assertDontSee('اقتباس اليوم');
    });

    it('links each quote to the public page of the member who added it', function () {
        $member = User::factory()->create(['username' => 'nora']);
        Quote::factory()->for($member)->create();

        $response = $this->get(route('home'));

        $response->assertOk()->assertSee(route('users.show', 'nora'));
    });
});

describe('explore', function () {
    it('counts the quotes with the correct Arabic plural', function (int $count, string $label) {
        Quote::factory()->count($count)->create();

        $response = $this->get(route('explore'));

        $response->assertOk()->assertSee($label);
    })->with([
        'one' => [1, 'اقتباس واحد'],
        'two' => [2, 'اقتباسان'],
        'three to ten' => [3, '3 اقتباسات'],
        'eleven to ninety-nine' => [11, '11 اقتباسًا'],
    ]);
});

describe('store', function () {
    it('publishes a quote of exactly 155 characters', function () {
        $user = User::factory()->create();
        $content = str_repeat('ب', 155);

        $response = $this->actingAs($user)
            ->from(route('home'))
            ->post(route('quotes.store'), ['content' => $content, 'color' => '#7c0221']);

        $response->assertRedirect(route('home'))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('quotes', ['user_id' => $user->id, 'content' => $content]);
    });

    it('rejects a quote longer than 155 characters with an Arabic message', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('home'))
            ->post(route('quotes.store'), ['content' => str_repeat('ب', 156), 'color' => '#7c0221']);

        $response->assertRedirect(route('home'))
            ->assertInvalid(['content' => 'يجب ألا يتجاوز عدد أحرف نص الاقتباس 155.']);
        $this->assertDatabaseEmpty('quotes');
    });

    it('rejects a quote shorter than 3 characters', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('home'))
            ->post(route('quotes.store'), ['content' => 'ab', 'color' => '#7c0221']);

        $response->assertRedirect(route('home'))
            ->assertInvalid(['content' => 'يجب ألا يقل عدد أحرف نص الاقتباس عن 3.']);
        $this->assertDatabaseEmpty('quotes');
    });

    it('sends guests to the login page without saving the quote', function () {
        $response = $this->post(route('quotes.store'), ['content' => 'Words from a guest.', 'color' => '#7c0221']);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseEmpty('quotes');
    });
});

describe('update', function () {
    it("updates the owner's own quote", function () {
        $user = User::factory()->create();
        $quote = Quote::factory()->for($user)->create(['content' => 'Original words.']);

        $response = $this->actingAs($user)
            ->from(route('profile'))
            ->put(route('quotes.update', $quote), ['content' => 'Edited words.', 'color' => '#292929']);

        $response->assertRedirect(route('profile'))->assertSessionHasNoErrors();
        expect($quote->fresh()->content)->toBe('Edited words.');
    });

    it('forbids editing a quote that belongs to someone else', function () {
        $quote = Quote::factory()->create(['content' => 'Original words.']);

        $response = $this->actingAs(User::factory()->create())
            ->put(route('quotes.update', $quote), ['content' => 'Hijacked words.', 'color' => '#292929']);

        $response->assertForbidden();
        expect($quote->fresh()->content)->toBe('Original words.');
    });

    it('reopens the edit panel with the error inside it when the new text is too short', function () {
        $user = User::factory()->create();
        $quote = Quote::factory()->for($user)->create(['content' => 'Original words.']);

        $response = $this->actingAs($user)
            ->from(route('profile'))
            ->followingRedirects()
            ->put(route('quotes.update', $quote), [
                '_form' => 'edit-'.$quote->id,
                'content' => 'ab',
                'color' => '#7c0221',
            ]);

        $response->assertOk()->assertSeeInOrder([
            'data-edit-panel',
            'يجب ألا يقل عدد أحرف نص الاقتباس عن 3.',
        ]);
        expect($quote->fresh()->content)->toBe('Original words.');
    });
});

describe('destroy', function () {
    it("deletes the owner's own quote", function () {
        $user = User::factory()->create();
        $quote = Quote::factory()->for($user)->create();

        $response = $this->actingAs($user)->from(route('profile'))->delete(route('quotes.destroy', $quote));

        $response->assertRedirect(route('profile'));
        $this->assertModelMissing($quote);
    });

    it('forbids deleting a quote that belongs to someone else', function () {
        $quote = Quote::factory()->create();

        $response = $this->actingAs(User::factory()->create())->delete(route('quotes.destroy', $quote));

        $response->assertForbidden();
        $this->assertModelExists($quote);
    });
});
