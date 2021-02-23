<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       mybestdev.com
 * @since      1.0.0
 *
 * @package    Niko_Save
 * @subpackage Niko_Save/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="wrap">
	<form method="post" action="options.php">
		<?php
			settings_fields( 'niko-save-settings' );
			do_settings_sections( 'niko-save-settings' );
			submit_button();
		?>
	</form>
</div>