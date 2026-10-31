<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

describe('register', function () {
    it('creates the account with a hashed password and signs the user in', function () {
        $response = $this->post(route('register'), [
            'name' => 'نورة',
            'username' => 'noura',
            'email' => 'noura@example.com',
            'password' => 'secret-pass-1',
        ]);

        $response->assertRedirect(route('profile'));
        $this->assertAuthenticated();
        $storedPassword = User::sole()->password;
        expect($storedPassword)->not->toBe('secret-pass-1');
        expect(Hash::check('secret-pass-1', $storedPassword))->toBeTrue();
    });

    it('rejects a name or password outside the allowed length', function (array $input, string $field, string $message) {
        $response = $this->from(route('signup'))->post(route('register'), [
            'name' => 'نورة',
            'username' => 'noura',
            'email' => 'noura@example.com',
            'password' => 'secret-pass-1',
            ...$input,
        ]);

        $response->assertRedirect(route('signup'))->assertInvalid([$field => $message]);
        $this->assertGuest();
        $this->assertDatabaseEmpty('users');
    })->with([
        'name shorter than 2 characters' => [['name' => 'ن'], 'name', 'يجب ألا يقل عدد أحرف الاسم عن 2.'],
        'name longer than 50 characters' => [['name' => str_repeat('ن', 51)], 'name', 'يجب ألا يتجاوز عدد أحرف الاسم 50.'],
        'password shorter than 8 characters' => [['password' => 'short12'], 'password', 'يجب ألا يقل عدد أحرف كلمة المرور عن 8.'],
    ]);
});

describe('login', function () {
    it('signs in with the right email and password', function () {
        $user = User::factory()->create(['password' => 'secret-pass-1']);

        $response = $this->post(route('login.submit'), ['email' => $user->email, 'password' => 'secret-pass-1']);

        $response->assertRedirect(route('profile'));
        $this->assertAuthenticatedAs($user);
    });

    it('rejects a wrong password with an Arabic message', function () {
        $user = User::factory()->create(['password' => 'secret-pass-1']);

        $response = $this->from(route('login'))
            ->post(route('login.submit'), ['email' => $user->email, 'password' => 'wrong-pass-1']);

        $response->assertRedirect(route('login'))
            ->assertInvalid(['email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.']);
        $this->assertGuest();
    });
});

describe('logout', function () {
    it('signs the user out and returns to the login page', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    });
});

describe('destroyProfilePhoto', function () {
    it('deletes the profile photo and its stored file', function () {
        Storage::fake('public');
        Storage::disk('public')->put('profile-photos/avatar.jpg', 'photo');
        $user = User::factory()->create(['profile_photo_path' => 'profile-photos/avatar.jpg']);

        $response = $this->actingAs($user)->delete(route('profile.photo.destroy'));

        $response->assertRedirect(route('profile'))
            ->assertSessionHas('success', 'تم حذف صورتك الشخصية.');
        expect($user->fresh()->profile_photo_path)->toBeNull();
        Storage::disk('public')->assertMissing('profile-photos/avatar.jpg');
    });

    it('redirects to the profile when the user has no photo to delete', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('profile.photo.destroy'));

        $response->assertRedirect(route('profile'));
    });

    it('redirects guests to the login page instead of deleting a photo', function () {
        $response = $this->delete(route('profile.photo.destroy'));

        $response->assertRedirect(route('login'));
    });
});

describe('profile', function () {
    it('shows the delete photo button when the user has a photo', function () {
        $user = User::factory()->create(['profile_photo_path' => 'profile-photos/avatar.jpg']);

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertOk()->assertSee('حذف الصورة');
    });

    it('hides the delete photo button when the user has no photo', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertOk()->assertDontSee('حذف الصورة');
    });
});
