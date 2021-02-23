<?php

/**
 * Fired during plugin deactivation
 *
 * @link       mybestdev.com
 * @since      1.0.0
 *
 * @package    Niko_Save
 * @subpackage Niko_Save/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Niko_Save
 * @subpackage Niko_Save/includes
 * @author     nikol <kirilova@mail.bg>
 */
class Niko_Save_Deactivator
{

	/**
	 * On deactivation delete the "Saved" page.
	 *
	 * Get the "Saved" page id, check if it exists and delete the page that has that id.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		// Get Saved page id.
		$saved_page_id = get_option('toptal_save_saved_page_id');

		// Check if the saved page id exists.
		if ($saved_page_id) {

			// Delete saved page.
			wp_delete_post($saved_page_id, true);

			// Delete saved page id record in the database.
			delete_option('toptal_save_saved_page_id');
		}
	}
}
