<?php

namespace CtThemeCustom;

use Elgg\Notifications\InstantNotificationEventHandler;
use Elgg\Email;
use Elgg\EmailParams;

class EmailValidationHandler extends InstantNotificationEventHandler {

    /**
     * Subject override
     */
    protected function getNotificationSubject(\ElggUser $recipient, string $method): string {

        $plugin = elgg_get_plugin_from_id('ct_theme_custom');

        $site = elgg_get_site_entity();
        $site_name = $plugin->email_site_name ?: $site->getDisplayName();

        $display_name = $recipient->getDisplayName();

        // Admin-customizable subject
        $subject_template = $plugin->email_subject ?: "{site_name} – Please confirm your email address";

        $subject = str_replace(
            ['{display_name}', '{site_name}'],
            [$display_name, $site_name],
            $subject_template
        );

        return $subject;
    }



    /**
     * Body override
     */
    protected function getNotificationBody(\ElggUser $recipient, string $method): string {

        $plugin = elgg_get_plugin_from_id('ct_theme_custom');
        $site = elgg_get_site_entity();
        $site_name = $plugin->email_site_name ?: $site->getDisplayName();

        // Build validation link
        $link = elgg_generate_url('account:validation:email:confirm', [
            'u' => $recipient->guid,
        ]);
        $validation_link = elgg_http_get_signed_url($link);

        // Optional email logo file
        $logo_url = '';
        if ($plugin->email_logo_guid) {
            $file = get_entity((int)$plugin->email_logo_guid);
            if ($file instanceof \ElggFile && $file->exists()) {
                $logo_url = elgg_get_inline_url($file);
            }
        }

        // Admin-customizable body
        $body_template = $plugin->email_body;

        if (!$body_template) {
            // Final fallback to language file, if admin leaves the settings empty
            $body_template = elgg_echo('email:validate:body');
        }

        // Insert variables
        $body = str_replace(
            ['{display_name}', '{site_name}', '{validation_link}', '{logo_url}'],
            [$recipient->getDisplayName(), $site_name, $validation_link, $logo_url],
            $body_template
        );

        // Wrap in simple pattern (we do not replace Elgg core email shell)
        return $body;
    }


    /**
     * Email only
     */
    protected function getNotificationMethods(): array {
        return ['email'];
    }
}