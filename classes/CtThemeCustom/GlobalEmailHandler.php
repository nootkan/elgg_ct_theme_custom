<?php

namespace CtThemeCustom;

class GlobalEmailHandler {
    
    /**
     * Prepare email notification - add logo and custom site name to all emails
     * EXCEPT validation emails (which have their own handler)
     */
    public static function prepareEmailNotification(\Elgg\Event $hook) {
        
        $notification = $hook->getValue();
        
        if (!$notification instanceof \Elgg\Notifications\Notification) {
            return;
        }
        
        // Only process email notifications
        if ($hook->getParam('method') !== 'email') {
            return;
        }
        
        $plugin = \elgg_get_plugin_from_id('ct_theme_custom');
        
        // Check if global email logo is enabled
        if ($plugin->use_global_email_logo !== '1') {
            return; // Feature not enabled
        }
        
        // Don't modify validation emails - they have their own system
        if (strpos($notification->subject, 'confirm your email') !== false ||
            strpos($notification->body, 'validation') !== false) {
            return;
        }
        
        // Get the email logo (reuse the same logo as validation email)
        $logo_url = '';
        if ($plugin->email_logo_guid) {
            $file = \get_entity((int)$plugin->email_logo_guid);
            if ($file instanceof \ElggFile && $file->exists()) {
                $logo_url = \elgg_get_inline_url($file);
            }
        }
        
        // Get custom site name or use default
        $site = \elgg_get_site_entity();
        $site_name = trim($plugin->global_email_site_name);
        if ($site_name === '') {
            $site_name = $site->getDisplayName();
        }
        
        // Add logo to the top of the email body if we have one
        if ($logo_url) {
            $logo_html = '<div style="text-align:center; margin-bottom:20px;">
                <img src="' . $logo_url . '" alt="' . htmlspecialchars($site_name) . '" style="max-width:200px; height:auto;">
            </div>';
            
            $notification->body = $logo_html . $notification->body;
        }
        
        // Replace site name in subject and body
        $default_site_name = $site->getDisplayName();
        if ($site_name !== $default_site_name) {
            $notification->subject = str_replace($default_site_name, $site_name, $notification->subject);
            $notification->body = str_replace($default_site_name, $site_name, $notification->body);
        }
        
        return $notification;
    }
}