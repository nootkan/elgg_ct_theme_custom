<?php

use CtThemeCustom\ValidateEmailHandler;
use CtThemeCustom\Bootstrap; // <-- NEW: needed so 'bootstrap' key below can reference the class

return [

    // NEW: tells Elgg this plugin has a bootstrap class, and to call its boot() method
    // during plugin initialization.
    'bootstrap' => Bootstrap::class,

    'plugin' => [
        'name' => 'CT Theme Custom',
        'version' => '1.0.0',

        'settings' => [
            'website_logo_guid' => '',
            'email_site_name' => '',
            'email_subject'   => '',
            'email_body'      => '',
            'email_logo_guid' => '',
            'use_global_email_logo' => '',
            'global_email_site_name' => '',
            'show_registration_message' => '',
            'registration_message' => '',
            'registration_message_classes' => '',
            'registration_message_style' => '',
            'show_login_intro' => '',
            'login_intro_message' => '',
        ],

        'settings_view' => 'plugins/ct_theme_custom/settings',
    ],

    'actions' => [
        // Override core plugin settings save action so we can
        // handle logo uploads for ct_theme_custom, but still
        // save settings for all other plugins normally.
        'plugins/settings/save' => [
            'access' => 'admin',
        ],
    ],

    // Register our custom validation email handler
    'notifications' => [
        'user' => [
            'user' => [
                'uservalidationbyemail' => ValidateEmailHandler::class,
            ],
        ],
    ],

    'view_extensions' => [
        'elgg.css' => [
            'css/ct_theme_custom.css' => ['priority' => 500],
        ],

        'admin.css' => [
            'css/ct_admin_custom.css' => ['priority' => 500],
        ],

        'page/elements/header' => [
            'ct_theme_custom/logo' => ['priority' => 100],
        ],
		'page/elements/topbar' => [
            'ct_theme_custom/admin_logo' => ['priority' => 100],
        ],
    
        'admin/header' => [
            'ct_theme_custom/admin_logo' => ['priority' => 100],
        ],

        // Adds the admin-editable introduction text above the username
        // field on the Walled Garden login page. The view itself contains
        // checks so it only prints on the actual Walled Garden login page
        // (see views/default/ct_theme_custom/login_intro.php).
        'forms/login' => [
            'ct_theme_custom/login_intro' => ['priority' => 200],
        ],
    ],

    'view_options' => [
        'ct_theme_custom/graphics/camping-buddy-logo.png' => ['simplecache' => true],
    ],
];