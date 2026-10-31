<?php

return [
    'home' => 'Back to home',
    'logo_label' => 'Kunnasha, home',

    '403' => [
        'title' => 'Not allowed',
        'message' => "You don't have permission to access this page.",
    ],
    '404' => [
        'title' => 'Page not found',
        'message' => 'It seems this page was never written in the notebook, or it has moved elsewhere.',
        'action' => 'Explore quotes',
    ],
    '419' => [
        'title' => 'Page expired',
        'message' => 'This page was open for a long time. Go back, refresh it and try again.',
        'action' => 'Go back',
    ],
    '429' => [
        'title' => 'Too many requests',
        'message' => 'You sent too many requests in a short time. Wait a moment and try again.',
    ],
    '500' => [
        'title' => 'Something went wrong',
        'message' => 'The pen slipped this time. Please try again shortly.',
    ],
    '503' => [
        'title' => 'Under maintenance',
        'message' => "We're rearranging the pages and refilling the ink. We'll be back shortly.",
    ],
];
