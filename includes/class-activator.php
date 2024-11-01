<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    Wp_Signups
 * @subpackage Wp_Signups/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Signups
 * @subpackage Wp_Signups/includes
 * @author     WP Signups team
 */
class Wp_Signups_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

	    $options = get_option('wp_signups_settings');

		$admin = new Wp_Signups_Admin(null, null);
		$admin->setup_posttypes();
		flush_rewrite_rules();

	    if (empty($options)) {
            $options = array(
                'confirmation_subject' => __('Thank you for signing up!', 'wp-signups'),
                'confirmation_from_address' => get_site_option('admin_email')
            );
            update_option('wp_signups_settings', $options);
        }

	}

}
