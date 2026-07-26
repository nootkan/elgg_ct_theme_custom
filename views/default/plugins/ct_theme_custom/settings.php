<?php
/**
 * Plugin settings for CT Theme Custom
 * Simple single-logo upload interface
 */

$plugin = elgg_get_plugin_from_id('ct_theme_custom');

/**  
 * WEBSITE LOGO SECTION
 */
echo "<h3 style='margin-top:0; color:#256a19;'>Website Logo</h3>";

$website_logo_guid = (int) $plugin->website_logo_guid;

// Preview current website logo
if ($website_logo_guid) {
    $file = get_entity($website_logo_guid);
    if ($file instanceof \ElggFile && $file->exists()) {
        $url = elgg_get_inline_url($file);
        echo "<p><strong>Current Website Logo:</strong></p>";
        echo "<p><img src='{$url}' style='max-width:250px; border:1px solid #ccc; padding:6px; background:transparent;'></p>";
    }
} else {
    echo "<p><em>No custom website logo uploaded.</em></p>";
}

// Upload new website logo
echo elgg_view_field([
    '#type' => 'file',
    '#label' => 'Upload New Website Logo',
    '#help' => 'PNG, JPG, GIF, WEBP allowed. Uploading will replace the current logo.',
    'name' => 'website_logo',
]);

// Delete website logo checkbox
echo elgg_view_field([
    '#type' => 'checkbox',
    '#label' => 'Remove website logo',
    'name' => 'remove_website_logo',
    'value' => '1',
]);

echo "<hr style='margin:3rem 0; border-color:#ddd;'>";



/**
 * WALLED GARDEN LOGIN PAGE INTRODUCTION
 */

echo "<h3 style='color:#256a19;'>Walled Garden Login Page</h3>";

echo elgg_view_field([
    '#type' => 'checkbox',
    '#label' => 'Show introduction text on the login page',
    'name' => 'params[show_login_intro]',
    'value' => '1',
    'checked' => $plugin->show_login_intro !== '0',
]);

echo elgg_view_field([
    '#type' => 'longtext',
    '#label' => 'Login Page Introduction Text (HTML allowed)',
    '#help' => 'Displayed below the "Log in" heading and above the username field on the Walled Garden login page. Leave empty to use the default Camping Buddy introduction text.',
    'name' => 'params[login_intro_message]',
    'value' => $plugin->login_intro_message,
]);

echo "<hr style='margin:3rem 0; border-color:#ddd;'>";



/**
 * EMAIL VALIDATION TEMPLATE SETTINGS  
 */

echo "<h3 style='color:#256a19;'>Registration Page Settings</h3>";

echo elgg_view_field([
    '#type' => 'checkbox',
    '#label' => 'Show spam folder reminder on registration page',
    'name' => 'params[show_registration_message]',
    'value' => '1',
    'checked' => $plugin->show_registration_message !== '0',
]);

echo elgg_view_field([
    '#type' => 'longtext',
    '#label' => 'Registration Message Text',
    'name' => 'params[registration_message]',
    'value' => $plugin->registration_message ?: '<p><strong>Important:</strong> After registering, check spam/junk email.</p>',
]);

echo elgg_view_field([
    '#type' => 'text',
    '#label' => 'Message Container CSS Classes',
    'name' => 'params[registration_message_classes]',
    'value' => $plugin->registration_message_classes ?: 'elgg-message elgg-state-notice',
]);

echo elgg_view_field([
    '#type' => 'text',
    '#label' => 'Custom Inline CSS',
    'name' => 'params[registration_message_style]',
    'value' => $plugin->registration_message_style ?: 'margin: 20px 0;',
]);

echo "<hr style='margin:3rem 0; border-color:#ddd;'>";

echo "<h3 style='color:#256a19;'>Email Validation Settings</h3>";

echo elgg_view_field([
    '#type' => 'text',
    '#label' => 'Site Name used in Email Templates',
    'name' => 'params[email_site_name]',
    'value' => $plugin->email_site_name,
]);

echo elgg_view_field([
    '#type' => 'text',
    '#label' => 'Email Subject',
    'name' => 'params[email_subject]',
    'value' => $plugin->email_subject ?: '{site_name} – Please confirm your email address',
]);

echo elgg_view_field([
    '#type' => 'longtext',
    '#label' => 'Email Body (HTML Allowed)',
    'name' => 'params[email_body]',
    'value' => $plugin->email_body,
]);

echo "<hr style='margin:2rem 0;'>";



/**
 * EMAIL LOGO SECTION
 */

echo "<h3 style='color:#256a19;'>Email Logo</h3>";

$email_logo_guid = (int) $plugin->email_logo_guid;

// Preview current email logo
if ($email_logo_guid) {
    $file = get_entity($email_logo_guid);
    if ($file instanceof \ElggFile && $file->exists()) {
        $url = elgg_get_inline_url($file);
        echo "<p><strong>Current Email Logo:</strong></p>";
        echo "<p><img src='{$url}' style='max-width:250px; border:1px solid #ccc; padding:6px;'></p>";
    }
} else {
    echo "<p><em>No custom email logo uploaded.</em></p>";
}

// Upload new email logo
echo elgg_view_field([
    '#type' => 'file',
    '#label' => 'Upload New Email Logo',
    '#help' => 'PNG, JPG, GIF, WEBP allowed. Uploading will replace the current logo.',
    'name' => 'email_logo',
]);

// Remove email logo checkbox
echo elgg_view_field([
    '#type' => 'checkbox',
    '#label' => 'Remove email logo',
    'name' => 'remove_email_logo',
    'value' => '1',
]);

echo "<hr style='margin:3rem 0; border-color:#ddd;'>";

/**
 * GLOBAL EMAIL SETTINGS
 */

echo "<h3 style='color:#256a19;'>Global Email Settings (All Other Emails)</h3>";
echo "<p><em>These settings apply to all system emails EXCEPT the validation email above.</em></p>";

// Enable global email logo
echo elgg_view_field([
    '#type' => 'checkbox',
    '#label' => 'Use logo in all system emails',
    'name' => 'params[use_global_email_logo]',
    'value' => '1',
    'checked' => $plugin->use_global_email_logo === '1',
    '#help' => 'When enabled, your logo will appear in password reset, notifications, and other system emails.',
]);

// Global email site name
echo elgg_view_field([
    '#type' => 'text',
    '#label' => 'Site Name for System Emails',
    'name' => 'params[global_email_site_name]',
    'value' => $plugin->global_email_site_name,
    '#help' => 'Leave empty to use your site\'s default name.',
]);