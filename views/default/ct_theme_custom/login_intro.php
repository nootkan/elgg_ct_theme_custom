<?php
/**
 * Walled Garden login introduction text
 *
 * Displays a short, admin-editable introduction above the username field
 * on the Walled Garden login page. Registered as a view extension of
 * 'forms/login' (see elgg-plugin.php) rather than replacing any core view.
 *
 * SAFETY CHECK:
 * The 'forms/login' view is reused by Elgg core in several places besides
 * the Walled Garden page - for example the topbar login dropdown, the
 * logged-out front page, and the "please log in" prompt under comments.
 * We only want our text to appear on the actual Walled Garden login page,
 * so we check that:
 *   1) Walled Garden mode is actually turned on for this site, AND
 *   2) we are on the root ("/") page or the direct "/login" page - the
 *      only two routes that render the Walled Garden panel.
 * If either check fails, this view prints nothing and quietly gets out
 * of the way.
 */

if (!elgg_get_config('walled_garden')) {
    return;
}

$current_route = elgg_get_current_route_name();
if (!in_array($current_route, ['index', 'account:login'], true)) {
    return;
}

$plugin = elgg_get_plugin_from_id('ct_theme_custom');

// Show/hide checkbox setting.
// Convention used elsewhere in this plugin (see registration_message):
// treat "not explicitly set to '0'" as shown, so the intro appears by
// default even before an admin has visited the settings page.
$show_intro = $plugin->show_login_intro !== '0';

if (!$show_intro) {
    return;
}

// Admin-editable message, with a sensible fallback if the admin hasn't
// entered anything yet.
$message = trim((string) $plugin->login_intro_message);

if ($message === '') {
    $message = '<p>Camping Buddy connects adult campers with potential camping companions.</p>'
        . '<p>Meet people with similar camping interests, share local campground knowledge '
        . 'and connect with campers familiar with the places you want to explore.</p>';
}

echo '<div class="ct-login-intro">' . $message . '</div>';
