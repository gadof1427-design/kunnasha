<?php

namespace App\Support;

/**
 * Turns plain quote text into safe HTML and highlights @mentions of real users.
 *
 * The text is escaped BEFORE anything is injected, so user content can never
 * add markup. Only usernames that actually exist (passed in) are highlighted.
 */
final class MentionFormatter
{
    /** Same pattern Quote::syncMentions() uses to detect mentions. */
    public const PATTERN = '/(?<![\w@])@([A-Za-z0-9_]{3,30})/';

    /**
     * @param  array<int,string>  $knownUsernames
     */
    public static function format(string $content, array $knownUsernames): string
    {
        $escaped = htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        if ($knownUsernames === []) {
            return $escaped;
        }

        $known = array_flip(array_map('strtolower', $knownUsernames));

        return preg_replace_callback(
            self::PATTERN,
            static function (array $m) use ($known): string {
                if (! isset($known[strtolower($m[1])])) {
                    return $m[0];
                }

                return '<span class="kn-mention" dir="ltr">@'.$m[1].'</span>';
            },
            $escaped
        ) ?? $escaped;
    }
}
