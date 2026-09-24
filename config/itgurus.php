<?php

return [
    'tagline' => 'Verified information for Africans living in Germany.',

    'contact_email' => env('ITG_CONTACT_EMAIL', 'info@itgurusgermany.com'),

    // Where contact-form and volunteer notifications are sent.
    'notify_email' => env('ITG_NOTIFY_EMAIL', env('ITG_CONTACT_EMAIL', 'info@itgurusgermany.com')),

    // One-time browser setup for hosting without SSH/cron (see SetupController). Keep empty normally.
    'setup_token' => env('ITG_SETUP_TOKEN'),

    // Leave empty to hide a link.
    'social' => [
        'whatsapp' => env('ITG_SOCIAL_WHATSAPP'),
        'telegram' => env('ITG_SOCIAL_TELEGRAM'),
        'linkedin' => env('ITG_SOCIAL_LINKEDIN'),
        'instagram' => env('ITG_SOCIAL_INSTAGRAM'),
        'github' => env('ITG_SOCIAL_GITHUB', 'https://github.com/yomofo2s/itguruswiki'),
    ],
];
