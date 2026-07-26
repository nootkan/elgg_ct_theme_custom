<?php
return [
    // ===========================
    // Email Validation Overrides
    // ===========================
    //
    // Admin can edit these in plugin settings.
    // We keep language strings simple and static,
    // and insert dynamic content inside body.php.
    //
    // SUBJECT:
    // %s = display name
    'email:validate:subject' => "Please confirm your email address, %s",
    // BODY TEMPLATE:
    // Placeholders replaced in body.php:
    // {display_name}, {site_name}, {validation_link}, {logo_url}
    //
    // NOTE: This is only the fallback. If the admin edits the text in
    // plugin settings, that version overrides this.
    //
    'email:validate:body' => "
<table style='width:100%; max-width:600px; margin:auto;'>
  {logo_url}
  <tr>
    <td style='padding:20px; background:#ffffff; border:1px solid #c8ffc8; border-radius:8px;'>
    
      <p>Dear {display_name},</p>
      <p>Thank you for signing up at <strong>{site_name}</strong>!</p>
      <p>Please confirm your email address by clicking the link below:</p>
      <p><a href=\"{validation_link}\">{validation_link}</a></p>
      <p>If the link does not work, copy and paste it into your browser.</p>
      <p>Regards,<br>
      The {site_name} Team</p>
    </td>
  </tr>
</table>
",

    // ===========================
    // Account Validated Notification Override
    // ===========================
    // Overrides the core Elgg subject line for the "your account
    // has been validated" email, so it shows our site name instead
    // of the (intentionally blank) global Site Name setting.
    'user:notification:validate:subject' => 'Your account on Camping Buddy is ready for use',
];