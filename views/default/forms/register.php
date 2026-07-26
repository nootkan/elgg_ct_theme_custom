<?php
/**
 * Elgg register form
 */
$fields = [
	[
		'#type' => 'hidden',
		'name' => 'friend_guid',
		'value' => elgg_extract('friend_guid', $vars),
	],
	[
		'#type' => 'hidden',
		'name' => 'invitecode',
		'value' => elgg_extract('invitecode', $vars),
	],
	[
		'#type' => 'text',
		'#label' => elgg_echo('name'),
		'#class' => 'mtm',
		'name' => 'name',
		'value' => elgg_extract('name', $vars, get_input('n')),
		'autofocus' => true,
		'required' => true,
	],
	[
		'#type' => 'email',
		'#label' => elgg_echo('email'),
		'name' => 'email',
		'value' => elgg_extract('email', $vars, get_input('e')),
		'required' => true,
	],
	[
		'#type' => 'text',
		'#label' => elgg_echo('username'),
		'name' => 'username',
		'value' => elgg_extract('username', $vars, get_input('u')),
		'required' => true,
	],
	[
		'#type' => 'password',
		'#label' => elgg_echo('password'),
		'name' => 'password',
		'required' => true,
		'autocomplete' => 'new-password',
		'add_security_requirements' => true,
	],
	[
		'#type' => 'password',
		'#label' => elgg_echo('passwordagain'),
		'name' => 'password2',
		'required' => true,
		'autocomplete' => 'new-password',
		'add_security_requirements' => true,
	],
];

foreach ($fields as $field) {
	echo elgg_view_field($field);
}

// view to extend to add more fields to the registration form
echo elgg_view('register/extend', $vars);

// Add captcha
echo elgg_view('input/captcha', $vars);

// ===== CUSTOM REGISTRATION MESSAGE FROM PLUGIN SETTINGS =====
$plugin = elgg_get_plugin_from_id('ct_theme_custom');

// Check if message should be displayed (default to true if not set)
$show_message = $plugin->show_registration_message !== '0';

if ($show_message) {
	// Get message text from settings (with default fallback)
	$message_text = $plugin->registration_message;
	if (!$message_text) {
		$message_text = '<p><strong>Important:</strong> After registering, you will receive a validation email. If you don\'t see it in your inbox within a few minutes, please check your spam or junk folder.</p>';
	}
	
	// Get CSS classes from settings (with default fallback)
	$message_classes = $plugin->registration_message_classes;
	if (!$message_classes) {
		$message_classes = 'elgg-message elgg-state-notice';
	}
	
	// Get custom inline styles from settings (with default fallback)
	$message_style = $plugin->registration_message_style;
	if (!$message_style) {
		$message_style = 'margin: 20px 0;';
	}
	
	// Display the message
	echo '<div class="' . htmlspecialchars($message_classes) . '" style="' . htmlspecialchars($message_style) . '">';
	echo $message_text; // Already contains HTML from admin
	echo '</div>';
}
// ===== END OF CUSTOM REGISTRATION MESSAGE =====

$footer = elgg_view_field([
	'#type' => 'submit',
	'text' => elgg_echo('register'),
]);

elgg_set_form_footer($footer);