<?php

namespace App\Models;

use App\Support\MentionFormatter;
use Database\Factories\QuoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\HtmlString;

class Quote extends Model
{
    /** @use HasFactory<QuoteFactory> */
    use HasFactory;

    /** Card background colours users can pick from (one list for the whole app). */
    public const COLORS = [
        '#7c0221', // عودي غامق جدًا
        '#8c1126', // عودي غامق
        '#b6233e', // عودي متوسط
        '#f9e0b0', // ذهبي فاتح
        '#fff8cf', // كريمي فاتح

        '#292929', // رصاصي فحمي
        '#484848', // رصاصي غامق
        '#707070', // رصاصي متوسط
        '#A6A6A6', // رصاصي فاتح
        '#D4D4D4', // رصاصي فاتح جدًا
        '#F2F2F2', // رصاصي شبه أبيض
        '#FFFFFF', // أبيض
    ];

    // Their names (accessible labels) live in lang/{ar,en}/quotes.php under "colors".

    public const MAX_LENGTH = 155;

    protected $fillable = [
        'content',
        'author',
        'user_id',
        'color',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mentionedUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'quote_mentions',
            'quote_id',
            'mentioned_user_id'
        )->withTimestamps();
    }

    /**
     * Quote text as safe HTML, with mentions of real users highlighted.
     * Eager-load `mentionedUsers` to avoid one query per card.
     */
    public function formattedContent(): HtmlString
    {
        $usernames = $this->mentionedUsers->pluck('username')->all();

        return new HtmlString(
            MentionFormatter::format((string) $this->content, $usernames)
        );
    }

    /**
     * Extract usernames such as @ahmed or @user_1 and sync them.
     *
     * @return array<int,int> ids of users newly mentioned (to notify once).
     */
    public function syncMentions(): array
    {
        preg_match_all(MentionFormatter::PATTERN, $this->content, $matches);

        $usernames = array_unique($matches[1] ?? []);

        $userIds = User::query()
            ->whereIn('username', $usernames)
            ->where('id', '!=', $this->user_id)
            ->pluck('id')
            ->all();

        $changes = $this->mentionedUsers()->sync($userIds);

        return $changes['attached'];
    }
}
