<?php

namespace CtThemeCustom;

class EmailValidator {
    
    /**
     * Override language translations for validation email strings
     * This hook intercepts when elgg_echo is called for email:validate:subject and email:validate:body
     */
    public static function translateValidationStrings(\Elgg\Hook $hook) {
        
        $language = $hook->getType(); // e.g., 'en'
        $params = $hook->getParams();
        
        $message_key = $params['message_key'] ?? '';
        $args = $params['args'] ?? [];
        
        // Only process validation email keys
        if ($message_key !== 'email:validate:subject' && $message_key !== 'email:validate:body') {
            return;
        }
        
        $plugin = \elgg_get_plugin_from_id('ct_theme_custom');
        $site = \elgg_get_site_entity();
        
        // Get site name from settings or default
        $site_name = trim($plugin->email_site_name);
        if ($site_name === '') {
            $site_name = $site->getDisplayName();
        }
        
        // For the body, args[1] contains the validation link URL
        // We can extract the user GUID from it to get the user
        $user = null;
        if ($message_key === 'email:validate:body' && isset($args[1])) {
            $validation_link = $args[1];
            // Extract user GUID from URL (format: ...?u=123&...)
            if (preg_match('/[?&]u=(\d+)/', $validation_link, $matches)) {
                $user_guid = (int)$matches[1];
                $user = \get_user($user_guid);
            }
        }
        
        // For subject, args[0] is display name, args[1] is site name (but we'll use our override)
        if ($message_key === 'email:validate:subject') {
            $display_name = $args[0] ?? 'User';
            
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
        
        // For body, args[0] is site name, args[1] is validation link
        if ($message_key === 'email:validate:body') {
            $validation_link = $args[1] ?? '';
            $display_name = $user ? $user->getDisplayName() : 'User';
            
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
            $body = str_replace(
                ['{display_name}', '{site_name}', '{validation_link}', '{logo_url}'],
                [$display_name, $site_name, $validation_link, $logo_url],
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
}
