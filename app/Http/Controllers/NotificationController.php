<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('notifications.index', [
            'page' => 'Notifications',
            'notifications' => $notifications,
        ]);
    }

    public function open(
        Request $request,
        string $notification
    ): RedirectResponse {
        $userNotification = $request->user()
            ->notifications()
            ->findOrFail($notification);

        $userNotification->markAsRead();

        $quoteId = $userNotification->data['quote_id'] ?? null;

        if ($quoteId !== null) {
            // Notifications created before permalinks stored "/#quote-ID";
            // always resolve from quote_id so old ones keep working too.
            $quote = Quote::find($quoteId);

            return $quote
                ? redirect()->route('quotes.show', $quote)
                : redirect()->route('notifications.index')
                    ->with('success', __('notifications.quote_gone'));
        }

        return redirect()->to($userNotification->data['url'] ?? route('home'));
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()
            ->unreadNotifications
            ->markAsRead();

        return redirect()->route('notifications.index')
            ->with('success', __('notifications.all_read'));
    }
}
