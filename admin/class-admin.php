<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Wp_Signups
 * @subpackage Wp_Signups/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Signups
 * @subpackage Wp_Signups/admin
 * @author     WP Signups team
 */
class Wp_Signups_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Signups_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Signups_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        if ($this->is_signups_posttype()) {
            wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-signups-admin.css', array(), $this->version, 'all' );
            wp_enqueue_style( 'jquery-timepicker-css', plugins_url( 'css/jquery.timepicker.min.css', __FILE__ ) );
            wp_enqueue_style( 'jquery-ui-datepicker', plugins_url( 'css/jquery.ui.datepicker.css', __FILE__ ) );
        }
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Signups_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Signups_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ($this->is_signups_posttype()) {
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script( $this->plugin_name . '-jquery-timepicker-js', plugin_dir_url( __FILE__ ) . 'js/jquery.timepicker.min.js', array( 'jquery' ), $this->version, false );
            wp_enqueue_script( $this->plugin_name . '-admin-js', plugin_dir_url( __FILE__ ) . 'js/wp-signups-admin.js', array( 'jquery' ), $this->version, true );
        }
    }

    /**
     * Check for our post types.
     *
     * @return bool
     */
    public function is_signups_posttype($type = 'clipboard') {

        global $post_type;

        switch ($post_type) {
            case 'wp_signups_' . $type:
                return true;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * Add settings menu icon and links.
     *
     * @since    1.0.0
     */
    public function add_menu_icon_button() {

        add_menu_page( 'Signups', 'Signups', 'manage_options', 'wp_signups_settings', null, 'dashicons-clipboard', 85.333);
        add_submenu_page(
            'wp_signups_settings',
            esc_html__( 'Settings', 'invoice-app' ),
            esc_html__( 'Settings', 'invoice-app' ),
            'manage_options',
            'wp_signups_settings',
            array($this, 'options_page')
        );

    }

    public function setup_posttypes() {
        $labels = array(
            'name'                  => _x( 'Clipboards', $this->plugin_name ),
            'singular_name'         => _x( 'Clipboard', $this->plugin_name ),
            'menu_name'             => __( 'Clipboards', $this->plugin_name ),
            'name_admin_bar'        => __( 'Clipboard', $this->plugin_name ),
            'all_items'             => __( 'All Clipboards', $this->plugin_name ),
            'add_new_item'          => __( 'Add New Clipboard', $this->plugin_name ),
            'new_item'              => __( 'New Clipboard', $this->plugin_name ),
            'edit_item'             => __( 'Edit Clipboard', $this->plugin_name ),
            'update_item'           => __( 'Update Clipboard', $this->plugin_name ),
            'view_item'             => __( 'View Sheet', $this->plugin_name ),
            'search_items'          => __( 'Search Clipboards', $this->plugin_name ),
            'not_found'             => __( 'No Clipboards found', $this->plugin_name ),
            'not_found_in_trash'    => __( 'No Clipboards found in the Trash', $this->plugin_name ),
            'insert_into_item'      => __( 'Insert into Clipboard', $this->plugin_name ),
            'uploaded_to_this_item' => __( 'Uploaded to this Clipboard', $this->plugin_name ),
            'items_list'            => __( 'Clipboards list', $this->plugin_name ),
            'items_list_navigation' => __( 'Clipboards list navigation', $this->plugin_name ),
        );
        $args = array(
            'label'                 => __( 'Clipboard', $this->plugin_name ),
            'description'           => __( 'Clipboards', $this->plugin_name ),
            'labels'                => $labels,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'wp_signups_settings',
            'can_export'            => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'rewrite'               => array('slug' => 'clipboard', 'with_front' => false)
        );
        register_post_type('wp_signups_clipboard', $args);

        $labels = array(
            'name'                  => _x( 'Signups', $this->plugin_name ),
            'singular_name'         => _x( 'Signup', $this->plugin_name ),
            'menu_name'             => __( 'Signups', $this->plugin_name ),
            'name_admin_bar'        => __( 'Signup', $this->plugin_name ),
            'all_items'             => __( 'All Signups', $this->plugin_name ),
            'add_new_item'          => __( 'Add New Signup', $this->plugin_name ),
            'new_item'              => __( 'New Signup', $this->plugin_name ),
            'edit_item'             => __( 'Edit Signup', $this->plugin_name ),
            'update_item'           => __( 'Update Signup', $this->plugin_name ),
            'view_item'             => __( 'View Signup', $this->plugin_name ),
            'search_items'          => __( 'Search Signups', $this->plugin_name ),
            'not_found'             => __( 'No Signups found', $this->plugin_name ),
            'not_found_in_trash'    => __( 'No Signups found in the Trash', $this->plugin_name ),
            'insert_into_item'      => __( 'Insert into Signup', $this->plugin_name ),
            'uploaded_to_this_item' => __( 'Uploaded to this Signup', $this->plugin_name ),
            'items_list'            => __( 'Signups list', $this->plugin_name ),
            'items_list_navigation' => __( 'Signups list navigation', $this->plugin_name ),
        );
        $args = array(
            'label'                 => __( 'Signup', $this->plugin_name ),
            'description'           => __( 'Signups', $this->plugin_name ),
            'labels'                => $labels,
            'supports'              => array('title'),
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'can_export'            => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'rewrite'               => array('slug' => 'signup', 'with_front' => false)
        );
        register_post_type('wp_signups_people', $args);
    }

    /**
     * Edit post type list table.
     *
     * @since    1.0.0
     */
    public function edit_clipboard_post_list($defaults) {

        unset($defaults['title']);
        unset($defaults['date']);

        $defaults['wp_signups_clipboard_title'] = __('Title', $this->plugin_name);
        $defaults['wp_signups_event_date'] = __('Event Date', $this->plugin_name);
        $defaults['wp_signups_totals_spots'] = __('Total Spots', $this->plugin_name);
        $defaults['wp_signups_filled_spots'] = __('Filled Spots', $this->plugin_name);

        return $defaults;

    }

    /**
     * Populate invoice post type list table.
     *
     * @param $column_name
     * @param $post_ID
     */
    public function add_clipboard_details_to_post_list($column_name, $post_ID) {

        switch ($column_name) {

            case 'wp_signups_clipboard_title' :
                echo '<a class="row-title" href="' . esc_url(add_query_arg(array('post' => $post_ID, 'action' => 'edit'), 'post.php')) . '">' . get_the_title() . '</a>';
                break;

            case 'wp_signups_event_date' :

                $event_date = get_metadata('post', $post_ID);
                if (!empty($event_date)) {
                    echo esc_html($event_date['wp_signups_event_date'][0])
                         . '<br> <em><small>' . __('Start Time', $this->plugin_name) . ': '
                         . $event_date['wp_signups_event_start_time'][0]
                         . '</small></em>'
                         . '<br> <em><small>' . __('End Time', $this->plugin_name) . ': '
                         . $event_date['wp_signups_event_end_time'][0]
                         . '</small></em> ';
                }
                break;

            case 'wp_signups_totals_spots' :
                echo Wp_Signups_Public::get_clipboard_total_spots($post_ID);
                break;

            case 'wp_signups_filled_spots' :
                echo Wp_Signups_Public::get_clipboard_signup_count($post_ID);
                break;

            default :
                break;
        }

    }

    /**
     * Edit post type list table.
     *
     * @param    $defaults
     * @since    1.0.0
     */
    public function edit_signup_post_list($defaults) {

        unset($defaults['title']);
        unset($defaults['date']);

        $defaults['wp_signups_clipboard'] = __('Clipboard', $this->plugin_name);
        $defaults['wp_signups_slot'] = __('What', $this->plugin_name);
        $defaults['wp_signups_name'] = __('Name', $this->plugin_name);
        $defaults['wp_signups_email'] = __('Email', $this->plugin_name);
        $defaults['wp_signups_phone'] = __('Phone', $this->plugin_name);

        return $defaults;

    }

    /**
     * Populate invoice post type list table.
     *
     * @param    $column_name
     * @param    $post_ID
     * @since    1.0.0
     */
    public function add_signup_details_to_post_list($column_name, $post_ID) {

        switch ($column_name) {

            case 'wp_signups_clipboard' :
               $parentId = wp_get_post_parent_id($post_ID);
                echo get_the_title($parentId);
                break;

            case 'wp_signups_slot' :
                echo get_post_meta($post_ID, 'wp_signups_slot_title', true);
                break;

            case 'wp_signups_name' :
                echo get_post_meta($post_ID, 'wp_signups_signup_firstname', true) . ' ' . get_post_meta($post_ID, 'wp_signups_signup_lastname', true);
                break;

            case 'wp_signups_email' :
                echo the_title();
                break;

            case 'wp_signups_phone' :
                echo get_post_meta($post_ID, 'wp_signups_signup_phone', true);
                break;

            case 'wp_signups_reminder' :
                echo get_post_meta($post_ID, 'wp_signups_signup_reminded', true);
                break;

            default :
                break;
        }

    }

    /**
     * Filter admin list table for wp_signups_people post type.
     *
     * @param $query
     */
    public function signup_table_filtering($query) {

        global $wpdb;
        $qv = &$query->query_vars;

        if (is_admin() && $query->query['post_type'] == 'wp_signups_people') {
            if( !empty( $_GET['clipboard_id'] ) ) {
                $qv['post_parent'] = intval($_GET['clipboard_id']);

            }
        }

    }

    /**
     * Function that displays the options form.
     *
     * @since    1.0.0
     */
    public function options_page() {

        $options = $this->option_fields();
        $other = new WP_Signups_Plugin_Options('Signups', 'wp_signups_settings', 'wp_signups_settings');

        if (isset($_GET['tab']) && !is_numeric($_GET['tab'])){
            $active_tab = sanitize_text_field($_GET['tab']);
        } else {
            $active_tab = 'general';
        }

        $other->render_form($options, $active_tab);

    }

    /**
     * Function that builds the options array for Plugin_Settings class.
     *
     * @since    1.0.0
     */
    public function option_fields() {

        $options = array(
            'general' => apply_filters('marketing_lists_settings_quotes',
                array(
                    'confirmation_subject' => array(
                        'id'   => 'confirmation_subject',
                        'label' => __('Subject:', $this->plugin_name),
                        'type' => 'text',
                        'desc' => __('Appears in the email subject line.', $this->plugin_name)
                    ),
                    'confirmation_from_address' => array(
                        'id'   => 'confirmation_from_address',
                        'label' => __('From Address:', $this->plugin_name),
                        'type' => 'text',
                        'desc' => __('If you leave this blank the admin email on file under Settings > General will be used.', $this->plugin_name)
                    )
                )
            ),
        );
        return apply_filters('marketing_lists_settings_group', $options);

    }

    /**
     * Setup wp signups metaboxes.
     */
    public function setup_metaboxes() {
        add_meta_box('wp_signups_clipboard_details', __('Clipboard Details', $this->plugin_name), array(&$this, 'clipboard_details'), 'wp_signups_clipboard', 'normal');
        add_meta_box('wp_signups_signup_details', __('Signup Slots', $this->plugin_name), array(&$this, 'signup_slot_details'), 'wp_signups_clipboard', 'normal');
        add_meta_box('wp_signups_volunteer_details', __('Signup', $this->plugin_name), array(&$this, 'volunteer_details'), 'wp_signups_people', 'normal');
    }

    /**
     * Save wp signups metaboxes.
     *
     * @param $post_id
     * @param $post
     */
    public function save_signups_meta($post_id, $post) {

        if ( !current_user_can('edit_plugins') ){
            return;
        }

        if (!empty( $_REQUEST['event-date'])) {
            update_post_meta($post->ID, 'wp_signups_event_date', sanitize_text_field( $_REQUEST['event-date'] ) );
        }
        if (!empty( $_REQUEST['event-start-time'])) {
            update_post_meta($post->ID, 'wp_signups_event_start_time', sanitize_text_field( $_REQUEST['event-start-time'] ) );
        }
        if (!empty( $_REQUEST['event-end-time'])) {
            update_post_meta($post->ID, 'wp_signups_event_end_time', sanitize_text_field( $_REQUEST['event-end-time'] ) );
        }
        if (!empty( $_REQUEST['event-location'])) {
            update_post_meta($post->ID, 'wp_signups_event_location', sanitize_text_field( $_REQUEST['event-location'] ) );
        }

        if (!empty($_REQUEST['slot'])) {

            $sanitized_array = array();
            foreach ($_REQUEST['slot'] as $slot) {

                $slot['qty'] = intval($slot['qty']);
                $sanitized_array[] = array_map('sanitize_text_field', $slot);

            }
            update_post_meta($post->ID, 'wp_signups_slots', $sanitized_array);

        }

        if (!empty($_REQUEST['volunteer-firstname'])) {
            update_post_meta($post->ID, 'wp_signups_volunteer_firstname', sanitize_text_field($_REQUEST['volunteer-firstname']));
        }
        if (!empty($_REQUEST['volunteer-lastname'])) {
            update_post_meta($post->ID, 'wp_signups_volunteer_lastname', sanitize_text_field($_REQUEST['volunteer-lastname']));
        }
        if (!empty($_REQUEST['volunteer-email'])) {
            update_post_meta($post->ID, 'wp_signups_volunteer_email', sanitize_text_field($_REQUEST['volunteer-email']));
        }
        if (!empty($_REQUEST['volunteer-phone'])) {
            update_post_meta($post->ID, 'wp_signups_volunteer_phone', sanitize_text_field($_REQUEST['volunteer-phone']));
        }
        if (!empty($_REQUEST['volunteer-address'])) {
            update_post_meta($post->ID, 'wp_signups_volunteer_address', sanitize_text_field($_REQUEST['volunteer-address']));
        }
        if (!empty($_REQUEST['volunteer-city'])) {
            update_post_meta($post->ID, 'wp_signups_volunteer_city', sanitize_text_field($_REQUEST['volunteer-city']));
        }
        if (!empty($_REQUEST['volunteer-state'])) {
            update_post_meta($post->ID, 'wp_signups_volunteer_state', sanitize_text_field($_REQUEST['volunteer-state']));
        }
        if (!empty($_REQUEST['volunteer-zip'])) {
            update_post_meta($post->ID, 'wp_signups_volunteer_zip', sanitize_text_field($_REQUEST['volunteer-zip']));
        }

    }

    /**
     * Save page permalink of signup shortcade page.
     *
     * @param $post_id
     */
    public function post_has_signup_shortcode($post_id) {

        if(!defined('DOING_AUTOSAVE') || !DOING_AUTOSAVE){
            $content = get_post_field('post_content', $post_id);
            if (has_shortcode($content, 'signups')) {
                $options = get_option('wp_signups_settings');
                $options['shortcode_page'] = get_the_permalink($post_id);
                update_option('wp_signups_settings', $options);
            }
        }

    }

    /**
     * Add link to link to existing signups.
     *
     * @param $actions
     * @param $post
     *
     * @return mixed
     */
    public function add_post_links($actions, $post) {

        if ($this->is_signups_posttype()) {
            $actions['show_signups'] = '<a href="' . esc_url(add_query_arg(array('post_type' => 'wp_signups_people', 'clipboard_id' =>$post->ID) )) . '">Signups</a>';
        }
        return $actions;
    }

    /**
     * Get slot by id.
     *
     * @param $id
     */
    public function get_slot($id) {

        $args = array(
            'posts_per_page'   => -1,
            'offset'           => 0,
            'meta_value'       => $id,
        );
        $posts_array = get_posts( $args );
    }

    /**
     * Clipboard metabox.
     */
    public function clipboard_details() {

        global $post;

        $meta = get_metadata('post', $post->ID);

        ?>
        <table class=" form-table">
            <tbody>
            <tr class="">
                <th><label for="event-date">Date of Event</label></th>
                <td><input type="text" name="event-date" id="event-date" value="<?php echo (!empty($meta['wp_signups_event_date']) ? esc_attr($meta['wp_signups_event_date'][0]) : ''); ?>" class="ltr wp-signups-datepicker">
                </td>
            </tr>
            <tr class="">
                <th><label for="event-start-time">Start time</label></th>
                <td><input type="text" name="event-start-time" id="event-start-time" value="<?php echo (!empty($meta['wp_signups_event_start_time']) ? esc_attr($meta['wp_signups_event_start_time'][0]) : ''); ?>" class="ltr wp-signups-timepicker">
                </td>
            </tr>
            <tr class="">
                <th><label for="event-end-time">End Time</label></th>
                <td><input type="text" name="event-end-time" id="event-end-time" value="<?php echo (!empty($meta['wp_signups_event_end_time']) ? esc_attr($meta['wp_signups_event_end_time'][0]) : ''); ?>" class="ltr wp-signups-timepicker">
                </td>
            </tr>
            <tr class="">
                <th><label for="event-location">Location</label></th>
                <td><input type="text" name="event-location" id="event-location" value="<?php echo (!empty($meta['wp_signups_event_location']) ? esc_attr($meta['wp_signups_event_location'][0]) : ''); ?>" class="regular-text ltr">
                </td>
            </tr>
            </tbody>
        </table>
    <?php }

    /**
     * Slots
     */
    public function signup_slot_details() {

        global $post;

        $meta = get_post_meta($post->ID, 'wp_signups_slots', true);

        ?>
        <table class=" form-table">
            <tbody>
            <tr>
            <ul class="slots ui-sortable">
            <?php if (!empty($meta)) {
                $row = 1;
                foreach ($meta as $line_item) {
                    $title = ( ! empty( $line_item['title'] ) ) ? $line_item['title'] : '';
                    $qty   = ( ! empty( $line_item['qty'] ) ) ? $line_item['qty'] : '';
                    $id    = ( ! empty( $line_item['id'] ) ) ? $line_item['id'] : uniqid();

                    echo '<li id="slot-' . $row . '" class="ui-sortable-handle"> what? &nbsp;';
                    echo '<input type="text" name="slot[' . $row . '][title]" value="' . esc_attr( $title ) . '" size="20">&nbsp;&nbsp;&nbsp; # of available spots: ';
                    echo '<input type="text" name="slot[' . $row . '][qty]" value="' . esc_attr( $qty ) . '" size="5">';
                    echo '<input type="hidden" name="slot[' . $row . '][id]" value="' . esc_attr( $id ) . '"> ';
                    echo '<span class="dashicons dashicons-plus add-slot"></span> <span class="dashicons dashicons-minus remove-slot"></span></li>';
                    $row ++;
                }
            } else { ?>
                <li id="slot-1" class="ui-sortable-handle">
                    what? &nbsp;
                    <input type="text" name="slot[1][title]" value="" size="20">
                    &nbsp;&nbsp;&nbsp; # of available spots:
                    <input type="text" name="slot[1][qty]" value="" size="5">
                    <input type="hidden" name="slot[1][id]" value="<?php echo uniqid(); ?>">
                    <span class="dashicons dashicons-plus add-slot"></span> <span class="dashicons dashicons-minus remove-slot"></span>
                </li>
            <?php } ?>
            </ul>
            </tr>
            </tbody>
        </table>

    <?php }

    /**
     * Signup metabox.
     */
    public function volunteer_details() {

        global $post;

        $meta = get_metadata('post', $post->ID);

        ?>
        <table class=" form-table">
            <tbody>
            <tr class="">
                <th><label for="signup-firstname">First Name</label></th>
                <td><input type="text" name="signup-firstname" id="signup-firstname" value="<?php echo (!empty($meta['wp_signups_signup_firstname']) ? esc_attr($meta['wp_signups_signup_firstname'][0]) : ''); ?>" class="ltr">
                </td>
            </tr>
            <tr class="">
                <th><label for="volunteer-lastname">Last Name</label></th>
                <td><input type="text" name="volunteer-lastname" id="volunteer-lastname" value="<?php echo (!empty($meta['wp_signups_signup_lastname']) ? esc_attr($meta['wp_signups_signup_lastname'][0]) : ''); ?>" class="ltr">
                </td>
            </tr>
            <tr class="">
                <th><label for="volunteer-email">Email</label></th>
                <td><input type="text" name="volunteer-email" id="volunteer-email" value="<?php echo (!empty($meta['wp_signups_signup_email']) ? esc_attr($meta['wp_signups_signup_email'][0]) : ''); ?>" class="ltr regular-text">
                </td>
            </tr>
            <tr class="">
                <th><label for="volunteer-phone">Phone</label></th>
                <td><input type="text" name="volunteer-phone" id="volunteer-phone" value="<?php echo (!empty($meta['wp_signups_signup_phone']) ? esc_attr($meta['wp_signups_signup_phone'][0]) : ''); ?>" class="ltr">
                </td>
            </tr>
            <tr class="">
                <th><label for="volunteer-address">Address</label></th>
                <td><input type="text" name="volunteer-address" id="volunteer-address" value="<?php echo (!empty($meta['wp_signups_signup_address']) ? esc_attr($meta['wp_signups_signup_address'][0]) : ''); ?>" class="ltr regular-text">
                </td>
            </tr>
            <tr class="">
                <th><label for="volunteer-city">City</label></th>
                <td><input type="text" name="volunteer-city" id="volunteer-city" value="<?php echo (!empty($meta['wp_signups_signup_city']) ? esc_attr($meta['wp_signups_signup_city'][0]) : ''); ?>" class="ltr">
                </td>
            </tr>
            <tr class="">
                <th><label for="volunteer-state">State</label></th>
                <td><input type="text" name="volunteer-state" id="volunteer-state" value="<?php echo (!empty($meta['wp_signups_signup_state']) ? esc_attr($meta['wp_signups_signup_state'][0]) : ''); ?>" class="ltr">
                </td>
            </tr>
            <tr class="">
                <th><label for="volunteer-zip"><?php _e('Zip', $this->plugin_name); ?></label></th>
                <td><input type="text" name="volunteer-zip" id="volunteer-zip" value="<?php echo (!empty($meta['wp_signups_signup_zip']) ? esc_attr($meta['wp_signups_signup_zip'][0]) : ''); ?>" class="ltr">
                </td>
            </tr>
            </tbody>
        </table>
    <?php }

}
