<?php
/**
 * Admin Logo Output View
 * Used in admin topbar
 */

// Check if we're on an admin page by checking the URL
$current_url = elgg_get_current_url();
$site_url = elgg_get_site_url();
$relative_url = str_replace($site_url, '', $current_url);

$is_admin_page = (strpos($relative_url, 'admin') === 0);

// Only show on admin pages
if (!$is_admin_page) {
    return;
}

$plugin = elgg_get_plugin_from_id('ct_theme_custom');
$guid = (int) $plugin->website_logo_guid;

if ($guid) {
    $file = get_entity($guid);

    if ($file instanceof \ElggFile && $file->exists()) {
        $url = elgg_get_inline_url($file);

        echo "<div class='ct-admin-logo' style='display: inline-block; margin-right: 10px; vertical-align: middle;'>
                <img src='{$url}' alt='Site Logo' style='max-height: 40px; width: auto;'>
              </div>";
        return;
    }
}