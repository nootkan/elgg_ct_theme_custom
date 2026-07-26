<?php
/**
 * Override the default email salutation
 * Removes "Dear..." for validation emails since our custom template includes it
 */

$notification = elgg_extract('notification', $vars);

if (!$notification instanceof \Elgg\Notifications\Notification) {
    return;
}

// Check if this is a validation email by looking at the body content
$body = $notification->body ?? '';

// If the body contains our custom validation content, don't add salutation
if (stripos($body, 'confirm your email address') !== false || 
    stripos($body, 'validation') !== false ||
    stripos($body, 'Dear ') === 0) {
    // This is a validation email with custom salutation, skip the default one
    return;
}

// For other emails, show the default salutation
$recipient = $notification->getRecipient();
if ($recipient instanceof \ElggUser) {
    echo elgg_echo('notification:default:salutation', [$recipient->getDisplayName()]);
}
