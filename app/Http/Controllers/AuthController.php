<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:50'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[A-Za-z0-9_]+$/',
                'unique:users,username',
            ],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create($data);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('profile');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('profile');
    }

    public function profile()
    {
        $user = auth()->user();

        $quotes = $user->quotes()
            ->with(['user', 'mentionedUsers'])
            ->latest()
            ->latest('id')
            ->simplePaginate(12);

        return view('profile', [
            'user' => $user,
            'quotes' => $quotes,
            'quotesCount' => $user->quotes()->count(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:50'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[A-Za-z0-9_]+$/',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];

        if ($request->hasFile('profile_photo')) {
            $newPath = $request->file('profile_photo')
                ->store('profile-photos', 'public');

            if ($user->profile_photo_path) {
                Storage::disk('public')->delete(
                    $user->profile_photo_path
                );
            }

            $user->profile_photo_path = $newPath;
        }

        $user->save();

        return redirect()->route('profile')
            ->with('success', __('messages.profile_updated'));
    }

    /** Remove the profile photo so the user's initial is shown in its place. */
    public function destroyProfilePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);

            $user->profile_photo_path = null;
            $user->save();
        }

        return redirect()->route('profile')
            ->with('success', __('messages.profile_photo_deleted'));
    }

    public function logout(Request $request)
    {
        $locale = $request->session()->get('locale', 'ar');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $request->session()->put('locale', $locale);

        return redirect()->route('login');
    }
}
