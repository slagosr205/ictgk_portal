<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Active Brand (selected at deploy)
     |--------------------------------------------------------------------------
     |
     | This app is branded for ONE company per deployment.
     | Switch branding by setting APP_BRAND in the environment.
     |
     */
    'active' => env('APP_BRAND', 'altia'),

    /*
     |--------------------------------------------------------------------------
     | Admin Access
     |--------------------------------------------------------------------------
     |
     | Perfil IDs allowed to manage branding in-app.
     | Example: BRANDING_ADMIN_PERFIL_IDS=1,2
     |
     */
    'admin_perfil_ids' => array_values(array_filter(array_map(
        static fn ($v) => (int) trim($v),
        explode(',', (string) env('BRANDING_ADMIN_PERFIL_IDS', '1'))
    ))),

    /*
     |--------------------------------------------------------------------------
     | Storage
     |--------------------------------------------------------------------------
     |
     | Uploaded assets are stored on this disk under this directory.
     |
     */
    'upload_disk' => env('BRANDING_UPLOAD_DISK', 'public'),
    'upload_dir' => env('BRANDING_UPLOAD_DIR', 'branding'),

    // If enabled, admin can switch the active brand at runtime (DB-driven).
    // If disabled, the active brand is locked to APP_BRAND (deploy-time).
    'runtime_switch' => filter_var(env('BRANDING_RUNTIME_SWITCH', false), FILTER_VALIDATE_BOOL),

    /*
     |--------------------------------------------------------------------------
     | Brand Catalog
     |--------------------------------------------------------------------------
     |
     | Assets are paths relative to /public.
     | Example: public/branding/altia/logo.svg => asset('branding/altia/logo.svg')
     |
     */
    'brands' => [
        'green_valley' => [
            'key' => 'green_valley',
            'name' => 'Green Valley Hub',
            'assets' => [
                'logo' => 'branding/green_valley/logo.svg',
                'logo_light' => 'branding/green_valley/logo_light.svg',
                'background' => 'branding/green_valley/background.jpg',
                'favicon' => 'branding/green_valley/favicon.svg',
            ],
            'texts' => [
                'portal_title' => 'Portal de Reclutamiento',
            ],
            'palette' => [
                // Adjust these values to match the final GVH branding.
                'primary' => '#1F8A70',
                'secondary' => '#10403B',
                'accent' => '#D4E157',
                'light' => '#F6F7F8',
                'dark' => '#1A2A36',
                'danger' => '#E74C3C',
                'success' => '#32C36C',
                'warning' => '#DCE442',
                'text_primary' => '#2C3E50',
                'text_secondary' => '#6C757D',
                'border' => '#E1E8ED',
            ],
        ],

        'altia' => [
            'key' => 'altia',
            'name' => 'Altia Business Park',
            'assets' => [
                // Map these to your real files.
                'logo' => 'branding/altia/logo.svg',
                'logo_light' => 'branding/altia/logo_light.png',
                'background' => 'branding/altia/background.jpg',
                'favicon' => 'branding/altia/favicon.svg',
            ],
            'texts' => [
                'portal_title' => 'Portal de Reclutamiento',
            ],
            'palette' => [
                'primary' => '#32C36C',
                'secondary' => '#1A2A36',
                'accent' => '#DCE442',
                'light' => '#F6F7F8',
                'dark' => '#1A2A36',
                'danger' => '#E74C3C',
                'success' => '#32C36C',
                'warning' => '#DCE442',
                'text_primary' => '#2C3E50',
                'text_secondary' => '#6C757D',
                'border' => '#E1E8ED',
            ],
        ],
    ],
];
