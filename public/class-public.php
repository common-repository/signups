<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Wp_Signups
 * @subpackage Wp_Signups/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Signups
 * @subpackage Wp_Signups/public
 * @author     WP Signups team
 */
class Wp_Signups_Public {

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
	 * Add shortcode support.
	 */
	public function register_shortcodes(){
		add_shortcode('signups', array($this, 'clipboard_shortcode'));
	}

	/**
	 * Shortcodes.
	 *
	 * @param $atts
	 */
	function clipboard_shortcode($atts) {

		global $post;

		extract( shortcode_atts( array(
			'list_title' => 'CLipboards'
		), $atts ) );

		$html = '<h2>' . $list_title . '</h2>';
		$clipboards = $this->get_clipboards(false, true);
		$clipboards = array_reverse($clipboards);

		if (empty($clipboards)) {
			$html .= '<p>' . __('No clipboards currently available at this time.', $this->plugin_name) . '</p>';
		} else {
			$html .= '<table class="wp-signups-clipboards" cellspacing="0">';
				$html .= '<thead>';
					$html .= '<tr>';
						$html .= '<th class="column-title">Title</th>';
						$html .= '<th class="column-date">Date</th>';
						$html .= '<th class="column-open_spots">Open Spots</th>';
						$html .= '<th class="column-view_link">&nbsp;</th>';
					$html .= '</tr>';
				$html .= '</thead>';
				$html .= '<tbody>';

				foreach ( $clipboards as $clipboard ) {
					$clipboardDate = $this->get_clipboard_event_date($clipboard->ID);
					$open_spots = ($this->get_clipboard_total_spots($clipboard->ID) - $this->get_clipboard_signup_count($clipboard->ID));

					$html .= '<tr' . (($open_spots === 0) ? ' class="filled"' : '') . '>';
						$html .= '<td class="column-title"><a href="' . esc_url(get_the_permalink($clipboard->ID)) . '">' . $clipboard->post_title . '</a></td>';
						$html .= '<td class="column-date">'
					          . (($clipboard->post_date == '0000-00-00') ? 'N/A' : date(get_option('date_format'), strtotime($clipboardDate)))
					          . '</td>';
					    $html .= '<td class="column-open_spots">' . $open_spots . '</td>';
						$html .= '<td class="column-view_link">'
					          . (($open_spots > 0) ? '<a href="'. esc_url(get_the_permalink($clipboard->ID)) . '">View &amp; sign-up &raquo;</a>' : '&#10004; Filled')
					          . '</td>';
					$html .= '</tr>';
				}

				$html .= '</tbody>';
			$html .= '</table>';
		}

		echo $html;
	}

	/**
	 * Check before submitting form.
	 *
	 * @return bool
	 */
	public function maybe_process_signup_form() {
		if (empty($_POST['slot_id'])) {
			return false;
		}
		//$slot = $this->get_slot($slot_id);
		//$sheet = $this->data->get_sheet($task->post_parent);
		//$is_valid_sheet = !empty($task) && $sheet->{$this->prefix . '_is_active'};
		//$is_valid_task = !empty($sheet) && $task->{$this->prefix . '_is_active'};

		//if ($is_valid_sheet && $is_valid_task) {
		$this->process_signup_form();
		//}
	}

	/**
	 * Process signup form.
	 */
	public function process_signup_form() {
		//Error Handling
		if (
				empty($_POST['signup_firstname'])
				|| empty($_POST['signup_lastname'])
				|| empty($_POST['signup_email'])
				//|| empty($_POST['signup_phone'])
		) {
			$err = true;
			add_filter('before_display_form', array(&$this, 'handle_missing_field_error'));
		} elseif (empty($_POST['simple_captcha']) || (!empty($_POST['simple_captcha']) && trim($_POST['simple_captcha']) != '18')) {
		    $err = true;
			add_filter('before_display_form', array(&$this, 'handle_missing_captcha_error'));
		}

		// Add Signup
		if (empty($err)) {

			global $post;

			try {
				$this->add_signup($_POST, $_GET['slot_id']);
				if ($this->send_mail($post->ID, $_POST['signup_email'], $_GET['slot_id']) === false) $return .= 'ERROR SENDING EMAIL';
			} catch (Exception $e) {
				$err = true;
				error_log($e->getMessage());
			}

			wp_redirect(esc_url(esc_url($_POST['referral'])));
			exit;
		}

	}

	/**
	 * Display signup form and list.
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function add_clipboard_to_the_content($content) {

		global $post;

		if ($post->post_type == 'wp_signups_clipboard') {

			if (!empty($_GET['slot_id'])) {
				$this->display_signup_form($post->ID, $_GET['slot_id']);
			} else {

				$clipboardDate = $this->get_clipboard_event_date( $post->ID );
				$content .= '<div class="wp-signups-clipboard"><h2>' . $post->post_title . '</h2>';
				$content .= ( ( $clipboardDate && $clipboardDate != '0000-00-00' ) ? '<p>Date: ' . date( get_option( 'date_format' ), strtotime( $clipboardDate ) ) . '</p>' : '' );
				$content .= '<p><a href="' . get_option('wp_signups_settings')['shortcode_page'] . '">' . __( 'View all Clipboards', $this->plugin_name ) . ' &laquo;</a></p>';
				$content .= '<h3>' . __( 'Sign up below', $this->plugin_name ) . '</h3>';

				if ( empty( $slots = $this->get_slots( $post->ID ) ) ) {
					$content .= '<p>' . __( 'No Signup Slots were found.', $this->plugin_name ) . '</p>';
				} else {
					$content .= '<table class="wp-signups-slots" cellspacing="0">';
					$content .= '<thead>';
					$content .= '<tr><th>' . __( 'What', $this->plugin_name )
					            . '</th><th>' . __( 'Available Spots', $this->plugin_name )
					            . '</th></tr>';
					$content .= '</thead>';
					$content .= '<tbody>';

					foreach ( $slots as $slot ) {
						$content .= '<tr>';
						$content .= '<td>';
						$content .= $slot['title'];
						$content .= '</td>';
						$content .= '<td>';

						$i       = 1;
						$signups = $this->get_signups( $slot['id'] );
						foreach ( $signups as $signup ) {
							if ( $i != 1 ) {
								$content .= '<br />';
							}
							$content .= '#'
							            . $i
							            . ': <em>'
							            . $signup->firstname
							            . ' '
							            . substr( $signup->lastname, 0, 1 )
							            . '.</em>';
							$i ++;
						}
						for ( $i = $i; $i <= $slot['qty']; $i ++ ) {
							if ( $i != 1 ) {
								$content .= '<br />';
							}
							$content .= '#'
							            . $i
							            . ': <a href="'
							            . add_query_arg(array('slot_id' => $slot['id'], 'slot_title' => $slot['title']))
							            . '">Sign up &raquo;</a>';
						}

						$content .= '</td>';
						$content .= '</tr>';
					}
					$content .= '</tbody>';
					$content .= '</table>';

				}
			}
		}
		return $content;
	}

	/**
	 * Display form inputs.
	 *
	 * @param $post_id
	 * @param $slot_id
	 */
	public function display_signup_form($post_id, $slot_id) { ?>

		<h3><?php _e( 'Sign-up below', $this->plugin_name ); ?></h3>
		<p>
			<?php _e( 'You are signing up for ' . get_the_title($post_id), $this->plugin_name); ?>
		</p>

		<?php echo apply_filters('before_display_form', ''); ?>

		<form name="wp-signup-form" method="post" action="" class="wp-signup-form">
			<p>
				<label for="signup_firstname" class="signup_firstname">
					<?php _e('First Name', $this->plugin_name); ?>
					<span class="wp-signups-required">*</span>
				</label>
				<input type="text" id="signup_firstname" class="signup_firstname" name="signup_firstname" maxlength="100" value=""/>
			</p>
			<p>
				<label for="signup_lastname" class="signup_lastname">
					<?php _e('Last Name', $this->plugin_name); ?>
					<span class="wp-signups-required">*</span>
				</label>
				<input type="text" id="signup_lastname" class="signup_lastname" name="signup_lastname" maxlength="100" value=""/>
			</p>

			<p>
				<label for="signup_email" class="signup_email">
					<?php _e( 'E-mail', $this->plugin_name); ?>
					<span class="wp-signups-required">*</span>
				</label>
				<input type="text" id="signup_email" class="signup_email" name="signup_email" maxlength="100" value=""/>
			</p>

			<p>
				<label for="simple_captcha"><?php _e('What is  17 + 1 = ?', $this->plugin_name); ?> </label>
				<input type="text" id="simple_captcha" name="simple_captcha" size="4" value="" />
			</p>

			<p class="submit">
				<input type="hidden" name="mode" value="submitted" />
				<input type="hidden" name="slot_id" value="<?php echo esc_attr($slot_id); ?>" />
				<input type="hidden" name="post_parent" value="<?php echo intval($post_id); ?>" />
				<input type="hidden" name="slot_title" value="<?php echo esc_attr($_GET['slot_title']); ?>" />
				<input type="hidden" name="referral" value="<?php echo esc_url( remove_query_arg( array( 'slot_id', 'slot_title' ) ) ); ?>" />
				<input type="submit" name="Submit" class="button-primary"
				       value="<?php _e( 'Sign me up!', '' ); ?>"/> or
				<a href="<?php echo get_option('wp_signups_settings')['shortcode_page']; ?>" class="dls-sus-backlink-from-task">
					<?php _e( '&laquo; go back to the Sign-Up Sheet', $this->plugin_name ); ?>
				</a>
			</p>

			<p><span class="wp-signups-required">*</span> = <?php _e('required', $this->plugin_name); ?></p>

		</form><!-- .wp-signup-form -->

	<?php }

	/**
	 * Show standard form error.
	 *
	 * @return string
	 */
	public function handle_missing_field_error() {
		return '<p class="wp-signups error" style="color: #8a6d3b;background-color: #fcf8e3;border-color: #faebcc;padding:10px">' . __('Please complete all fields.', $this->plugin_name) . '</p>';
	}

	/**
	 * Show captcha error.
	 *
	 * @return string
	 */
	public function handle_missing_captcha_error() {
		return '<p class="wp-signups error" style="color: #8a6d3b;background-color: #fcf8e3;border-color: #faebcc;padding:10px">' . __('Oh, snap! Incorrect Captcha.', $this->plugin_name) . '</p>';
	}

	/**
	 * Include all metadata for a clipboard.
	 *
	 * @param $clipboard_id
	 *
	 * @return mixed
	 */
	public function include_fields($clipboard_id) {
		return get_metadata('post', $clipboard_id);
	}

	/**
	 * Get a single clipboard post
	 *
	 * @param $id
	 * @param $by
	 *
	 * @return bool
	 */
	public function get_clipboard($id, $by = 'id') {

		switch ($by) {
			case 'id' :
				$clipboard             = get_post($id);
				$clipboard->start_time = get_post_meta( $clipboard->ID, 'wp_signups_event_start_time', true );
				$clipboard->end_time   = get_post_meta( $clipboard->ID, 'wp_signups_event_end_time', true );
				break;

			case 'slot' :
				break;
		}

		return $clipboard;
	}

	/**
	 * Get all clipboard posts
	 *
	 * @param     bool     get just trash
	 * @param     bool     get only active sheets or those without a set date
	 * @return    mixed    array of sheets
	 */
	public function get_clipboards($trash = false, $active_only = false) {

		$args = array(
			'posts_per_page' => -1,
			'post_type' => 'wp_signups_clipboard',
			'post_status' => 'publish',
			'order' => 'DESC'
		);
		$clipboards = get_posts($args);
		return $clipboards;

	}

	/**
	 * @param $clipboard_id
	 *
	 * @return number
	 */
	public static function get_clipboard_total_spots($clipboard_id) {

		$clipboardMeta = get_post_meta($clipboard_id, 'wp_signups_slots', true);
		$spotQtys = array();
		foreach ( $clipboardMeta as $item) {
			$spotQtys[] = intval($item['qty']);
		}
		return array_sum($spotQtys);

	}

	/**
	 * @param $clipboard_id
	 *
	 * @return int
	 */
	public static function get_clipboard_signup_count($clipboard_id) {

		$args = array(
				'post_parent' => $clipboard_id,
				'posts_per_page' => -1,
				'post_type' => 'wp_signups_people',
				'post_status' => 'publish',
				'order' => 'DESC'
		);
		$posts= get_posts($args);
		return intval(count($posts));

	}

	/**
	 * Get clipboard event date.
	 *
	 * @param $clipboard_id
	 *
	 * @return mixed
	 */
	public function get_clipboard_event_date($clipboard_id, $get_time = false) {
		if ($get_time === false) {
			return get_post_meta( $clipboard_id, 'wp_signups_event_date', true );
		}
	}

	/**
	 * Get all slots by clipboard id.
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function get_slots($id) {
		return get_post_meta($id, 'wp_signups_slots', true);
	}

	/**
	 * Get all signups for a given slot.
	 *
	 * @param $slot_id
	 *
	 * @return array
	 */
	public static function get_signups($slot_id) {

		$args = array(
				'posts_per_page' => -1,
				'meta_key' => 'wp_signups_slot_id',
				'meta_value' => $slot_id,
				'post_type' => 'wp_signups_people',
				'order' => 'DESC',
				'post_status' => 'publish',
		);
		$signups = get_posts($args);

		foreach ($signups as $signup) {
			//$signup->slot_id = get_post_meta($signup->ID, 'wp_signups_slot_id', true);
			$signup->firstname = get_post_meta($signup->ID, 'wp_signups_signup_firstname', true);
			$signup->lastname = get_post_meta($signup->ID, 'wp_signups_signup_lastname', true);
		}

		return $signups;

	}

	/**
	 * Create signup post type and into db.
	 *
	 * @param $fields
	 * @param $slot_id
	 */
	public function add_signup($fields, $slot_id) {

		$result = wp_insert_post(
			array(
					'post_title' => sanitize_email($fields['signup_email']),
					'post_parent' => intval($_POST['post_parent']),
					'post_status' => 'publish',
					'post_type' => 'wp_signups_people',
					'comment_status' => 'closed',
					'ping_status' => 'closed',
			)
		);

		foreach ($fields as $key => $field){
			switch ($key) {
				case 'mode':
				case 'Submit':
				case 'submit':
					continue;
					break;

				default:
					$field = sanitize_text_field($field);
					update_post_meta($result, 'wp_signups_'.$key, $field);
					break;
			}
		}
		update_post_meta($result, 'wp_signups_slot_id', $slot_id);

	}

	/**
	 * Send signup confirmation emails.
	 *
	 * @param $clipboard_id
	 * @param $to
	 * @param $slot_id
	 *
	 * @return bool
	 */
	public function send_mail($clipboard_id, $to, $slot_id) {

		$clipboard = $this->get_clipboard($clipboard_id);

		$plugin_settings = get_option('wp_signups_settings');

		(!empty($plugin_settings['confirmation_from_address'])) ? $from = $plugin_settings['confirmation_from_address'] : $from = get_bloginfo('admin_email');
		(!empty($plugin_settings['confirmation_subject'])) ? $subject = $plugin_settings['confirmation_subject'] :$subject = __('Thank you for signing up!', $this->plugin_name);

		$start = (!empty($clipboard->start_time)) ? __('Start Time: ', $this->plugin_name) . $clipboard->start_time : '';
		$end = (!empty($clipboard->end_time)) ? __('End Time: ', $this->plugin_name). $clipboard->end_time : '';

		$headers = "From: " . get_bloginfo('name')." <$from>\n" .
		           "Reply-To: $from\n" .
		           "Content-Type: text/plain; charset=iso-8859-1\n";

		$message = __('This message was sent to confirm that you signed up for:', $this->plugin_name) . "\n\n".
		           (($clipboard->wp_signups_event_date != '0000-00-00') ? "Date: ".date(get_option('date_format'), strtotime($clipboard->wp_signups_event_date))."\n" : "") .
		           $start ."\n" .
		           $end  ."\n" .
		           __('Event: ', $this->plugin_name) . $clipboard->post_title . "\n".
		           //"What: $slot->title \n\n".
		           __('If you need to change this or if this was sent in error, please contact us at ', $this->plugin_name) .  $from . "\n\n".
		           __('Thanks', $this->plugin_name) . ", \n" .
		           get_bloginfo('name') . "\n" .
		           get_bloginfo('url');

		return wp_mail($to, $subject, $message, $headers);

	}

}