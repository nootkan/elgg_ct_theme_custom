<?php
/**
 * Override the default email sign-off
 * Removes "Regards," for validation emails since our custom template includes it
 */
$notification = elgg_extract('notification', $vars);
if (!$notification instanceof \Elgg\Notifications\Notification) {
    return;
}
// Check if this is a validation email by looking at the body content
$body = $notification->body ?? '';
// If the body contains our custom validation content, don't add sign-off
if (stripos($body, 'confirm your email address') !== false || 
    stripos($body, 'validation') !== false ||
    stripos($body, 'Regards,') !== false) {
    // This is a validation email with custom sign-off, skip the default one
    return;
}
// For other emails, show the default sign-off
$plugin = elgg_get_plugin_from_id('ct_theme_custom');
$site = elgg_get_site_entity();
$site_name = $plugin->email_site_name ?: $site->getDisplayName();
echo elgg_echo('notification:default:sign-off', [$site_name]);
