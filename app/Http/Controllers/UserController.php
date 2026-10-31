<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    private const PER_PAGE = 12;

    /** Public page of a member: who they are and the quotes they added. */
    public function show(User $user): View
    {
        $quotes = $user->quotes()
            ->with(['user', 'mentionedUsers'])
            ->latest()
            ->latest('id')
            ->simplePaginate(self::PER_PAGE);

        return view('users.show', [
            'page' => 'User',
            'user' => $user,
            'quotes' => $quotes,
            'quotesCount' => $user->quotes()->count(),
        ]);
    }
}
