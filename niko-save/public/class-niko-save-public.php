<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       mybestdev.com
 * @since      1.0.0
 *
 * @package    Niko_Save
 * @subpackage Niko_Save/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Niko_Save
 * @subpackage Niko_Save/public
 * @author     nikol <kirilova@mail.bg>
 */
class Niko_Save_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Niko_Save_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Niko_Save_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$options = get_option( $this->plugin_name . '-settings' );

		if ( ! empty( $options['toggle-css-override'] ) && $options['toggle-css-override'] == 1 )
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/niko-save-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Niko_Save_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Niko_Save_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/niko-save-public.js', array( 'jquery' ), $this->version, false );
		// Get our options
	$options = get_option( $this->plugin_name . '-settings' );

	// Get our text
	$item_save_text = $options['text-save'];
	$item_unsave_text = $options['text-unsave'];
	$item_saved_text = $options['text-saved'];
	$item_no_saved = $options['text-no-saved'];

	$saved_page_id = get_option( 'niko_save_saved_page_id' );
	$saved_page_url = get_permalink( $saved_page_id );

	wp_localize_script(
		$this->plugin_name,
		'niko_save_ajax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'item_save_text' => $item_save_text,
			'item_unsave_text' => $item_unsave_text,
			'item_saved_text' => $item_saved_text,
			'item_no_saved' => $item_no_saved,
			'saved_page_url' => $saved_page_url
		)
	);
	}

/**
 * Generate unique cookie name for the current site and return it
 *
 * @since    1.0.0
 */
public function get_unique_cookie_name() {

	$site_url = get_bloginfo( 'url' );
	$site_name = get_bloginfo( 'name' );
	$suffix = '-niko-saved-items';

	$cookie_name = $site_url . $site_name . $suffix;

	// Now let's strip everything
	$cookie_name = str_replace( array( '[\', \']' ), '', $cookie_name );
	$cookie_name = preg_replace( '/\[.*\]/U', '', $cookie_name );
	$cookie_name = preg_replace( '/&(amp;)?#?[a-z0-9]+;/i', '-', $cookie_name );
	$cookie_name = htmlentities( $cookie_name, ENT_COMPAT, 'utf-8' );
	$cookie_name = preg_replace( '/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $cookie_name );
	$cookie_name = preg_replace( array( '/[^a-z0-9]/i', '/[-]+/' ) , '-', $cookie_name );
	$cookie_name = strtolower( trim( $cookie_name, '-' ) );

	return $cookie_name;
}

/**
 * Set cookie
 *
 * @since    1.0.4
 */
public function niko_set_cookie( $name, $value = array(), $time = null ) {

	$time = $time != null ? $time : time() + apply_filters( 'toptal_cookie_expiration', 60 * 60 * 24 * 30 );
	$value = base64_encode( json_encode( stripslashes_deep( $value ) ) );
	$expiration = apply_filters( 'niko_cookie_expiration_time', $time );

	$_COOKIE[ $name ] = $value;
	setcookie( $name, $value, $expiration, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, false );

}

/**
 * Get cookie
 *
 * @since    1.0.4
 */
public function niko_get_cookie( $name ) {

	if ( isset( $_COOKIE[$name] ) ) {
		return json_decode( base64_decode( stripslashes( $_COOKIE[$name] ) ), true );
	}

	return array();

}

/**
 * Get if enabled only for logged in users
 *
 * @since    1.0.2
 */
public function get_user_status() {

	$options = get_option( $this->plugin_name . '-settings' );
	if ( ! empty( $options['toggle-status-override'] ) ) {
		$status = $options['toggle-status-override'];
	} else {
		$status = 0;
	}

	return $status;

}

/**
 * Show save button.
 *
 * @since    1.0.0
 */
public function show_save_button( $item_id = '' ) {

	// Get our item ID
	if ( empty( $item_id ) ) {

		$item_id = get_queried_object_id();

	}

	// Get text for the button states
	$options = get_option( $this->plugin_name . '-settings' );
	$item_save_text = $options['text-save'];
	$item_unsave_text = $options['text-unsave'];

	// Get membership status option
	$status = $this->get_user_status();

	// Check if the user is logged in or not, and then check if the item is saved or not
	if ( is_user_logged_in() ) {

		// Get our saved items
		$saved_items = get_user_meta( get_current_user_id(), 'niko_saved_items', true );

		// If nothing is saved make $saved_items variable an array
		if ( empty( $saved_items ) ) {
			
			$saved_items = array();

		}

		// Check if the current item is saved or not
		if ( in_array( $item_id, $saved_items ) ) {

			$is_saved = true;

		} else {

			$is_saved = false;

		}

	} else {

		// Get our saved items
		$saved_items = $this->niko_get_cookie( $this->get_unique_cookie_name() );

		// Check if the current item is saved or not
		if ( in_array( $item_id, $saved_items ) ) {

			$is_saved = true;

		} else {

			$is_saved = false;

		}

	}

	if ( $status == 1 && is_user_logged_in() && is_singular() || $status == 0 && is_singular() ) {

		// Depending on the item status (saved or not), display different things.
		if ( $is_saved == false ) {

			return '<a href="#" class="niko-save-button" data-nonce="' . wp_create_nonce( 'niko_save_nonce' ) . '" data-item-id="' . esc_attr( $item_id ) . '"><span class="niko-save-icon"></span><span class="niko-save-text">' . esc_html( $item_save_text ) . '</span></a>';

		} else {

			return '<a href="#" class="niko-save-button saved" data-nonce="' . wp_create_nonce( 'niko_save_nonce' ) . '" data-item-id="' . esc_attr( $item_id ) . '"><span class="niko-save-icon"></span><span class="niko-save-text">' . esc_html( $item_unsave_text ) . '</span></a>';

		}

	}

}
/**
 * Append the button to the end of the content.
 *
 * @since    1.0.0
 */
public function append_the_button( $content ) {

	// Get our item ID
	$item_id = get_queried_object_id();

	// Get current item post type
	$current_post_type = get_post_type( $item_id );

	// Get our saved page ID, so we can make sure that this button isn't being shown there
	$saved_page_id = get_option( 'niko_save_saved_page_id' );

	// Set default values for options that we are going to call below
	$post_types = array();
	$override = 0;

	// Get our options
	$options = get_option( $this->plugin_name . '-settings' );
	if ( ! empty( $options['post-types'] ) ) {
		$post_types = $options['post-types'];
	}
	if ( ! empty( $options['toggle-content-override'] ) ) {
		$override = $options['toggle-content-override'];
	}

	// Let's check if all conditions are ok
	if ( $override == 1 && ! empty( $post_types ) && ! is_page( $saved_page_id ) && in_array( $current_post_type, $post_types ) ) {

		// Append the button
		$custom_content = '';
		ob_start();
		echo $this->show_save_button();
		$custom_content .= ob_get_contents();
		ob_end_clean();
		$content = $content . $custom_content;

	}

	return $content;

}


/**
 * Save or unsave the item.
 *
 * @since    1.0.0
 */
public function save_unsave_item() {

	// Check the nonce, if ok, proceed.
	if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'niko_save_nonce' ) ) {
		die;
	}

	// Get the item ID from AJAX
	if ( isset( $_REQUEST['item_id'] ) ) {

		$item_id = intval( $_REQUEST['item_id'] );

	} else {
		
		$item_id = 0;

	}

	$is_saved = false;

	// Check if user is logged in
	if ( is_user_logged_in() ) {

		// Get all saved items for this user
		$saved_items = get_user_meta( get_current_user_id(), 'niko_saved_items', true );

		// Check if this user doesn't have any saved items
		if ( empty( $saved_items ) ) {

			$saved_items = array();

		}

		// Check if this item is saved or not, if it is - unsave it, if it's not - save it
		if ( in_array( $item_id, $saved_items ) ) {

			$is_saved = true;

			// Remove the item
			unset( $saved_items[array_search( $item_id, $saved_items )] );

		} else {

			$is_saved = false;

			// Add the item
			array_push( $saved_items, $item_id );

		}

		// Save the changes to the user meta field
		update_user_meta( get_current_user_id(), 'niko_saved_items', $saved_items );

	} else {

		// Get all saved items from the cookie
		$saved_items = $this->niko_get_cookie( $this->get_unique_cookie_name() );

		// Check if this item is saved or not
		if ( in_array( $item_id, $saved_items ) ) {

			$is_saved = true;

			// Remove the item
			unset( $saved_items[array_search( $item_id, $saved_items )] );

		} else {

			$is_saved = false;

			// Add the item
			array_push( $saved_items, $item_id );

		}

		// Save the changes to the cookie
		$this->niko_set_cookie( $this->get_unique_cookie_name(), $saved_items );

	}

	// Create an array of data that we will return back to our AJAX
	$return = array(
		'is_saved' => $is_saved
	);

	// Return the data
	return wp_send_json( $return );

}
/**
 * Create Shortcode for Users to add the button.
 *
 * @since    1.0.0
 */
public function register_save_unsave_shortcode() {

	return $this->show_save_button();

}
/**
 * Create Shortcode for displaying all saved items.
 *
 * @since    1.0.0
 */
public function register_saved_shortcode() {

	// Get our options
	$options = get_option( $this->plugin_name . '-settings' );

	// Default Values
	$post_types = array();
	$saved_items = array();
	$text_no_saved = '';

	// Get our text when there are no saved items.
	if ( ! empty( $options['text-no-saved'] ) ) {
		$text_no_saved = $options['text-no-saved'];
	}

	// Check if user is logged in and get his saved items.
	if ( is_user_logged_in() ) {

		$saved_items = get_user_meta( get_current_user_id(), 'niko_saved_items', true );

	} else {

		$saved_items = $this->niko_get_cookie( $this->get_unique_cookie_name() );

	}

	// Wrap our content
	$html_to_return = '<div class="niko-saved-items">';

	// Check if there are any saved post types as well as selected post types
	if ( ! empty( $options['post-types'] ) && ! empty( $saved_items ) ) {

		$post_types = $options['post-types'];

		// Let's create our query
		$saved_args = array(
			'post_type'      => $post_types,
			'posts_per_page' => -1,
			'post__in'       => $saved_items
		);
		$saved_query = new WP_Query( $saved_args );

		if ( $saved_query->have_posts() ) : while ( $saved_query->have_posts() ) : $saved_query->the_post();

			// Our structure for individual saved items.
			$html_to_return .= '<div id="niko-saved-' . get_the_ID() . '" class="niko-saved-item">';

				// Show our save/unsave button.
				$html_to_return .= $this->show_save_button( get_the_ID() );

				// Check if this item has featured image, and defined class.
				if ( has_post_thumbnail() ) {

					$inner_class = 'niko-saved-dp-table';

				} else {

					$inner_class = 'niko-saved-dp-table niko-saved-no-thumbnail';

				}

				$inner_html_to_return = '<div class="' . esc_attr( $inner_class ) . '">';

					// Let's check if our class has featured image, and then show it.
					if ( $inner_class == 'niko-saved-dp-table' ) {

						$inner_html_to_return .= '<div class="niko-saved-dp-table-cell cell-thumbnail">';
							$inner_html_to_return .= '<a href="' . esc_url( get_permalink() ) . '">';
								$item_image = wp_get_attachment_image( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' );
								$inner_html_to_return .= $item_image;
							$inner_html_to_return .= '</a>';
						$inner_html_to_return .= '</div>';

					}

					$inner_html_to_return .= '<div class="niko-saved-dp-table-cell cell-content">';
						$inner_html_to_return .= '<div class="niko-saved-item-title">';
							$inner_html_to_return .= '<a href="' . esc_url( get_permalink() ) . '">';
								$inner_html_to_return .= get_the_title();
							$inner_html_to_return .= '</a>';
						$inner_html_to_return .= '</div>';

						if ( has_excerpt() ) {

							$inner_html_to_return .= '<div class="niko-saved-item-excerpt">' . get_the_excerpt() . '</div>';

						}
					$inner_html_to_return .= '</div>';

				$inner_html_to_return .= '</div>';

				$html_to_return .= apply_filters( 'niko_saved_item_html', $inner_html_to_return );

			$html_to_return .= '</div>';

		endwhile; wp_reset_postdata(); endif;

	} else {

		$html_to_return .= '<div class="niko-saved-nothing">' . esc_html( $text_no_saved ) . '</div>';

	}

	$html_to_return .= '</div>';

	return $html_to_return;

}

}
