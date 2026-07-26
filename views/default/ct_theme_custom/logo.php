<?php
/**
 * Website Logo Output View
 * Used in page/elements/header via elgg-plugin.php
 */

$plugin = elgg_get_plugin_from_id('ct_theme_custom');

$guid = (int) $plugin->website_logo_guid;

if ($guid) {
    $file = get_entity($guid);

    if ($file instanceof \ElggFile) {

        if ($file->exists()) {
            $url = elgg_get_inline_url($file);

            echo "<div class='ct-header-logo' style='padding:10px 10px;'>
                    <img src='{$url}' alt='Site Logo' style='max-width:250px; height:auto;'>
                  </div>";
            return;
        }
    }
}

// No custom logo - show nothing