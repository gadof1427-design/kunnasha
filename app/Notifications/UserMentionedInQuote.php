<?php

namespace App\Notifications;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserMentionedInQuote extends Notification
{
    use Queueable;

    public function __construct(
        public Quote $quote,
        public User $mentionedBy
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'user_mentioned_in_quote',
            'quote_id' => $this->quote->id,
            'quote_content' => $this->quote->content,
            'mentioned_by_name' => $this->mentionedBy->name,
            'mentioned_by_username' => $this->mentionedBy->username,
            // Permalink: works no matter how many pages of quotes exist.
            'url' => route('quotes.show', $this->quote),
        ];
    }
}
