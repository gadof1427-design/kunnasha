<?php

namespace App\Support;

/**
 * Picks readable text colours for a quote card from its background colour.
 *
 * One place decides this for every page (home, explore, profile, permalink),
 * so a card can never end up with dark text on a dark background again.
 */
final class QuoteStyle
{
    public const DEFAULT_BG = '#FFFFFF';

    private const LIGHT_TEXT = '#FFFFFF';
    private const DARK_TEXT = '#292929';

    /** Accent candidates, tried in order until one is readable on the card. */
    private const ACCENTS = ['#f9e0b0', '#7c0221', '#580016', '#fff8cf'];

    /**
     * @return array{bg:string,isDark:bool,fg:string,muted:string,accent:string,line:string,chip:string}
     */
    public static function for(?string $hex): array
    {
        $bg = self::normalize($hex);

        $isDark = self::contrast($bg, self::LIGHT_TEXT) > self::contrast($bg, self::DARK_TEXT);
        $fg = $isDark ? self::LIGHT_TEXT : self::DARK_TEXT;

        $accent = $fg;
        foreach (self::ACCENTS as $candidate) {
            if (self::contrast($bg, $candidate) >= 4.5) {
                $accent = $candidate;
                break;
            }
        }

        return [
            'bg' => $bg,
            'isDark' => $isDark,
            'fg' => $fg,
            // Secondary text: the main text, softened only as far as it stays readable.
            'muted' => self::softened($fg, $bg),
            'accent' => $accent,
            'line' => $isDark ? 'rgba(255,255,255,0.22)' : 'rgba(41,41,41,0.14)',
            'chip' => $isDark ? 'rgba(255,255,255,0.14)' : 'rgba(41,41,41,0.08)',
        ];
    }

    /**
     * Blend $fg towards $bg to get a softer secondary colour, but never below
     * a 4.5:1 contrast with the background (falls back to $fg itself).
     */
    private static function softened(string $fg, string $bg): string
    {
        foreach ([0.78, 0.86, 0.94] as $alpha) {
            $mixed = self::mix($fg, $bg, $alpha);

            if (self::contrast($bg, $mixed) >= 4.5) {
                return $mixed;
            }
        }

        return $fg;
    }

    /** $alpha of $top over $bottom, as #RRGGBB. */
    private static function mix(string $top, string $bottom, float $alpha): string
    {
        $t = array_map('hexdec', str_split(ltrim($top, '#'), 2));
        $b = array_map('hexdec', str_split(ltrim($bottom, '#'), 2));

        return sprintf(
            '#%02X%02X%02X',
            (int) round($t[0] * $alpha + $b[0] * (1 - $alpha)),
            (int) round($t[1] * $alpha + $b[1] * (1 - $alpha)),
            (int) round($t[2] * $alpha + $b[2] * (1 - $alpha)),
        );
    }

    public static function normalize(?string $hex): string
    {
        $hex = strtoupper(trim((string) $hex));

        if (preg_match('/^#([0-9A-F]{3})$/', $hex, $m)) {
            $hex = '#'.$m[1][0].$m[1][0].$m[1][1].$m[1][1].$m[1][2].$m[1][2];
        }

        return preg_match('/^#[0-9A-F]{6}$/', $hex) ? $hex : self::DEFAULT_BG;
    }

    /** WCAG 2.x contrast ratio between two #RRGGBB colours (1 to 21). */
    public static function contrast(string $a, string $b): float
    {
        $la = self::luminance($a);
        $lb = self::luminance($b);

        if ($la < $lb) {
            [$la, $lb] = [$lb, $la];
        }

        return ($la + 0.05) / ($lb + 0.05);
    }

    public static function luminance(string $hex): float
    {
        $hex = ltrim($hex, '#');

        $channels = array_map(
            static function (string $pair): float {
                $c = hexdec($pair) / 255;

                return $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
            },
            str_split($hex, 2)
        );

        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }
}
