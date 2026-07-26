<?php

namespace CtThemeCustom;

use Elgg\UserValidationByEmail\Notifications\ValidateEmailHandler as CoreHandler;

/**
 * Custom validation email handler that overrides the core one
 * This directly replaces how validation emails are generated
 */
class ValidateEmailHandler extends CoreHandler {
    
    /**
     * Override subject generation with custom content
     */
    protected function getNotificationSubject(\ElggUser $recipient, string $method): string {
        
        $plugin = \elgg_get_plugin_from_id('ct_theme_custom');
        $site = \elgg_get_site_entity();
        
        // Get site name from settings or default
        $site_name = trim($plugin->email_site_name);
        if ($site_name === '') {
            $site_name = $site->getDisplayName();
        }
        
        $display_name = $recipient->getDisplayName();
        
        // Get custom subject from settings
        $subject_template = trim($plugin->email_subject);
        if ($subject_template === '') {
            $subject_template = "{site_name} – Please confirm your email address";
        }
        
        return str_replace(
            ['{display_name}', '{site_name}'],
            [$display_name, $site_name],
            $subject_template
        );
    }
    
    /**
     * Override body generation with custom content
     */
    protected function getNotificationBody(\ElggUser $recipient, string $method): string {
        
        $plugin = \elgg_get_plugin_from_id('ct_theme_custom');
        $site = \elgg_get_site_entity();
        
        // Get site name from settings or default
        $site_name = trim($plugin->email_site_name);
        if ($site_name === '') {
            $site_name = $site->getDisplayName();
        }
        
        // Build validation link
        $link = \elgg_generate_url('account:validation:email:confirm', [
            'u' => $recipient->guid,
        ]);
        $validation_link = \elgg_http_get_signed_url($link);
        
        // Get logo URL
        $logo_url = '';
        if ($plugin->email_logo_guid) {
            $file = \get_entity((int)$plugin->email_logo_guid);
            if ($file instanceof \ElggFile && $file->exists()) {
                $logo_url = \elgg_get_inline_url($file);
            }
        }
        
        // Fallback to bundled logo if no custom logo
        if (!$logo_url && \elgg_view_exists('ct_theme_custom/graphics/camping-buddy-logo.png')) {
            $logo_url = \elgg_get_simplecache_url('ct_theme_custom/graphics/camping-buddy-logo.png');
        }
        
        // Get custom body from settings
        $body_template = trim($plugin->email_body);
        if ($body_template === '') {
            // Fallback body if admin hasn't set one
            $body_template = "Dear {display_name},\n\nPlease confirm your email address by clicking this link:\n\n{validation_link}\n\nThank you,\n{site_name}";
        }
        
        // Replace placeholders in body
        // Support BOTH {validation_link} and {verification_link} for flexibility
        $body = str_replace(
            ['{display_name}', '{site_name}', '{validation_link}', '{verification_link}', '{logo_url}'],
            [$recipient->getDisplayName(), $site_name, $validation_link, $validation_link, $logo_url],
            $body_template
        );
        
        // Add logo HTML if logo exists
        if ($logo_url) {
            $logo_html = '<div style="text-align:center; margin-bottom:20px;"><img src="' . $logo_url . '" alt="' . htmlspecialchars($site_name) . ' Logo" style="max-width:200px; height:auto;"></div>';
            $body = $logo_html . nl2br($body);
        } else {
            $body = nl2br($body);
        }
        
        return $body;
    }
}
