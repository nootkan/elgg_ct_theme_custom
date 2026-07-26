<?php

/**
 * Save settings and handle single logo uploads
 */

elgg_admin_gatekeeper();

$plugin_id = get_input('plugin_id');

if ($plugin_id !== 'ct_theme_custom') {
    return elgg_error_response("Invalid plugin.");
}

$plugin = elgg_get_plugin_from_id('ct_theme_custom');

// Maximum allowed logo file size: 5MB
define('CT_THEME_CUSTOM_MAX_LOGO_SIZE', 5 * 1024 * 1024);

// Only these setting keys are allowed to be saved.
// This matches the settings declared in elgg-plugin.php.
$allowed_setting_keys = [
    'email_site_name',
    'email_subject',
    'email_body',
    'use_global_email_logo',
    'global_email_site_name',
    'show_registration_message',
    'registration_message',
    'registration_message_classes',
    'registration_message_style',
    'show_login_intro',
    'login_intro_message',
];

// These two fields are allowed to contain admin-entered HTML
// (formatting, links, etc.), so we explicitly run them through
// Elgg's sanitizer as defense-in-depth, regardless of site-wide
// filtering settings.
$html_allowed_keys = [
    'registration_message',
    'email_body',
    'login_intro_message',
];


// =====================================================
// SAVE NORMAL TEXT SETTINGS FIRST
// =====================================================

$params = get_input('params', []);
if (is_array($params)) {
    foreach ($params as $k => $v) {
        if (in_array($k, $allowed_setting_keys)) {
            if (in_array($k, $html_allowed_keys) && function_exists('elgg_sanitize_input')) {
                $v = elgg_sanitize_input($v);
            }
            $plugin->setSetting($k, $v);
        }
    }
}


// =====================================================
// HANDLE WEBSITE LOGO UPLOAD
// =====================================================

// Upload new website logo
if (!empty($_FILES['website_logo']['name'])) {
    
    $file = $_FILES['website_logo'];
    $allowed = ['png','jpg','jpeg','gif','webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Verify the file is actually a real image (not just named like one)
    $image_info = @getimagesize($file['tmp_name']);
    $allowed_mimes = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'];
    $is_real_image = ($image_info !== false) && in_array($image_info['mime'], $allowed_mimes);
    
    // Check file size
    $is_valid_size = $file['size'] <= CT_THEME_CUSTOM_MAX_LOGO_SIZE;
    
    if (in_array($ext, $allowed) && $is_real_image && $is_valid_size) {
        
        // Delete old website logo if exists
        $old_guid = (int) $plugin->website_logo_guid;
        
        if ($old_guid) {
            $old_file = get_entity($old_guid);
            if ($old_file instanceof \ElggFile) {
                $old_file->delete();
            }
        }
        
        // Create new website logo file
        $logo = new \ElggFile();
        $logo->owner_guid = elgg_get_site_entity()->guid;
        $logo->access_id = ACCESS_PUBLIC; // CRITICAL: Make it publicly accessible
        $logo->setFilename("ct_theme_custom/website_logo.$ext");
        
        // CRITICAL: Save entity FIRST before writing file
        $save_result = $logo->save();
        
        if (!$save_result) {
            register_error("Failed to create logo entity");
        } else {
            $logo->open('write');
            $content = file_get_contents($file['tmp_name']);
            $logo->write($content);
            $logo->close();
            
            // Save the GUID to plugin settings
            $plugin->setSetting('website_logo_guid', $logo->guid);
        }
    } elseif (!$is_valid_size) {
        register_error("Website logo file is too large. Maximum allowed size is 5MB.");
    } else {
        register_error("Invalid website logo file. Please upload a genuine PNG, JPG, GIF, or WEBP image.");
    }
}

// Remove website logo
if (get_input('remove_website_logo') === '1') {
    
    $old_guid = (int) $plugin->website_logo_guid;
    if ($old_guid) {
        $old_file = get_entity($old_guid);
        if ($old_file instanceof \ElggFile) {
            $old_file->delete();
        }
    }
    $plugin->setSetting('website_logo_guid', '');
}


// =====================================================
// HANDLE EMAIL LOGO UPLOAD
// =====================================================

// Upload new email logo
if (!empty($_FILES['email_logo']['name'])) {
    
    $file = $_FILES['email_logo'];
    $allowed = ['png','jpg','jpeg','gif','webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Verify the file is actually a real image (not just named like one)
    $image_info = @getimagesize($file['tmp_name']);
    $allowed_mimes = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'];
    $is_real_image = ($image_info !== false) && in_array($image_info['mime'], $allowed_mimes);
    
    // Check file size
    $is_valid_size = $file['size'] <= CT_THEME_CUSTOM_MAX_LOGO_SIZE;
    
    if (in_array($ext, $allowed) && $is_real_image && $is_valid_size) {
        
        // Delete old email logo if exists
        $old_guid = (int) $plugin->email_logo_guid;
        
        if ($old_guid) {
            $old_file = get_entity($old_guid);
            if ($old_file instanceof \ElggFile) {
                $old_file->delete();
            }
        }
        
        // Create new email logo file
        $logo = new \ElggFile();
        $logo->owner_guid = elgg_get_site_entity()->guid;
        $logo->access_id = ACCESS_PUBLIC; // Make it publicly accessible
        $logo->setFilename("ct_theme_custom/email_logo.$ext");
        
        // Save entity FIRST before writing file
        $save_result = $logo->save();
        
        if (!$save_result) {
            register_error("Failed to create email logo entity");
        } else {
            $logo->open('write');
            $content = file_get_contents($file['tmp_name']);
            $logo->write($content);
            $logo->close();
            
            // Save the GUID to plugin settings
            $plugin->setSetting('email_logo_guid', $logo->guid);
        }
    } elseif (!$is_valid_size) {
        register_error("Email logo file is too large. Maximum allowed size is 5MB.");
    } else {
        register_error("Invalid email logo file. Please upload a genuine PNG, JPG, GIF, or WEBP image.");
    }
}

// Remove email logo
if (get_input('remove_email_logo') === '1') {
    $old_guid = (int) $plugin->email_logo_guid;
    if ($old_guid) {
        $old_file = get_entity($old_guid);
        if ($old_file instanceof \ElggFile) {
            $old_file->delete();
        }
    }
    $plugin->setSetting('email_logo_guid', '');
}


// =====================================================
// DONE
// =====================================================

return elgg_ok_response([], "Settings saved successfully.");