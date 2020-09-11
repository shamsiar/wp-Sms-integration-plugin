<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link        http://menusms.com/dongido
 * @since      1.0.0
 *
 * @package    Sendex
 * @subpackage Sendex/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sendex
 * @subpackage Sendex/admin
 * @author     Shams <shamsiarhamid@gmail.com>
 */

require_once(plugin_dir_path(__FILE__) . 'nusoap/nusoap.php');

class Sendex_Admin
{

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
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sendex_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sendex_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/sendex-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sendex_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sendex_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/sendex-admin.js', array('jquery'), $this->version, false);

		//add localized js file 
		add_action('wp_enqueue_scripts','custom_add_js');

		function custom_add_js() {
			
		wp_enqueue_script( 'phone-verification-js', plugins_url( 'js/phone_verification.js', __FILE__ ),array('jquery'));

		wp_localize_script( 'phone-verification-js', 'my_ajax_object',
					array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		}
		
		//End /////// 
	}

	/**
	 *  Register the administration menu for this plugin into the WordPress Dashboard
	 * @since    1.0.0
	 */

	public function add_sendex_admin_setting()
	{

		/*
	     * Add a settings page for this plugin to the Settings menu.
	     *
	     * Administration Menus: http://codex.wordpress.org/Administration_Menus
	     *
	     */
		add_options_page('SENDEX SMS PAGE', 'SENDEX', 'manage_options', $this->plugin_name, array($this, 'display_sendex_settings_page'));

		//page title, menu title, capability, slug , function name
// add_menu_page('SMS Integration','SMS Integration','manage_options','sms_integration_setting','sms_integration_setting');
	}

	/**
	 * Render the settings page for this plugin.( The html file )
	 *
	 * @since    1.0.0
	 */

	public function display_sendex_settings_page()
	{
		include_once('partials/sendex-admin-display.php');
	}


	/**
	 * Registers and Defines the necessary fields we need for api setting.
	 *
	 */
	public function sendex_admin_settings_save()
	{

		register_setting($this->plugin_name, $this->plugin_name, array($this, 'plugin_options_validate'));

		add_settings_section('sendex_main', 'Main Settings', array($this, 'sendex_section_text'), 'sendex-settings-page');

		add_settings_field('api_sid', 'Username', array($this, 'sendex_setting_sid'), 'sendex-settings-page', 'sendex_main');

		add_settings_field('api_auth_token', 'Password', array($this, 'sendex_setting_token'), 'sendex-settings-page', 'sendex_main');

		add_settings_field('api_auth_from', 'From', array($this, 'sendex_setting_from'), 'sendex-settings-page', 'sendex_main');
	}

	/**
	 * Displays the settings sub header
	 *
	 */
	public function sendex_section_text()
	{
		echo '<h3>SMS API details</h3>';
	}

	/**
	 * Renders the sid input field
	 *
	 */
	public function sendex_setting_sid()
	{

		$options = get_option($this->plugin_name);
		echo "<input id='plugin_text_string' name='$this->plugin_name[api_sid]' size='40' type='text' value='{$options['api_sid']}' />";
	}

	/**
	 * Renders the auth_from input field
	 *
	 */
	public function sendex_setting_from()
	{
		$options = get_option($this->plugin_name);
		echo "<input id='plugin_text_string' name='$this->plugin_name[api_auth_from]' size='40' type='text' value='{$options['api_auth_from']}' />";
	}

	/**
	 * Renders the auth_token input field
	 *
	 */
	public function sendex_setting_token()
	{
		$options = get_option($this->plugin_name);
		echo "<input id='plugin_text_string' name='$this->plugin_name[api_auth_token]' size='40' type='text' value='{$options['api_auth_token']}' />";
	}
	/**
	 * Sanitises all input fields.
	 *
	 */
	public function plugin_options_validate($input)
	{
		$newinput['api_sid'] = trim($input['api_sid']);
		$newinput['api_auth_token'] = trim($input['api_auth_token']);
		$newinput['api_auth_from'] = trim($input['api_auth_from']);

		return $newinput;
	}


	/**
	 * Register the sms page for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function register_sendex_sms_page()
	{
		// Create our settings page as a submenu page.
		add_submenu_page(
			'tools.php',                                         // parent slug
			__('SENDEX SMS PAGE', $this->plugin_name . '-sms'), // page title
			__('SENDEX', $this->plugin_name . '-sms'),         // menu title
			'manage_options',                                 // capability
			$this->plugin_name . '-sms',                       // menu_slug
			array($this, 'display_sendex_sms_page')       // callable function
		);
	}

	/**
	 * Display the sms page - The page we are going to be sending message from.
	 *
	 * @since    1.0.0
	 */

	public function display_sendex_sms_page()
	{
		include_once('partials/sendex-admin-sms.php');
	}

	public function send_message($phone=null, $msg=null)
	{

		$user_phone = array();
		if (!is_null($phone) && !is_null($msg)) {
			$to        = (isset($_POST['numbers'])) ? $_POST['numbers'] : '';
			$role        = (isset($_POST['role'])) ? $_POST['role'] : '';
			//$sender_id = (isset($_POST['sender']) )  ? $_POST['sender']  : '';
			$message   = (isset($_POST['message'])) ? $_POST['message'] : '';
			$box   = (isset($_POST['box'])) ? $_POST['box'] : '';
			$user_phone = array();
			
			if ($role) {
				$args = array(
					'role'    => $role,
					'orderby' => 'user_nicename',
					'order'   => 'ASC',
				);
				$users = get_users($args);
				foreach ($users as $usermeta) {
					if (!empty($usermeta->billing_phone))
						$user_phone[] = $usermeta->billing_phone;
				}
			}
		} else {
			//print_r($phone);die;
			$user_phone = $phone;
			$message = $msg;
		}
		
		//print_r('<pre>');print_r($receipents);die;
		//gets our api details from the database.
		$api_details = get_option('sendex'); #sendex is what we use to identify our option, it can be anything

		if (is_array($api_details) and count($api_details) != 0) {
			$client = new nusoap_client("https://user.mobireach.com.bd/index.php?r=sms/service", true);

			$params = array(
				'Username' => $api_details['api_sid'],
				'Password' => $api_details['api_auth_token'],
				'From' => $api_details['api_auth_from'],
			);
			//print_r('<pre>');print_r($client);die;
		}

		try {
			$recipients = !empty($user_phone) ? array_merge($user_phone, array_filter(explode(',', $to))) : explode(',', $to); //print_r('<pre>');print_r($recipients);die;
			$total_msgs = count($recipients) * $box;
			$params['Message'] = trim($message);
			$sms_credit = $this->get_sms_credit();

			if ($total_msgs > $sms_credit) {
				self::DisplayError("Your SMS Credit is not enough!!!");
			} else {
				for ($i = 0, $limit = 90; $i < count($recipients); $i += $limit) {
					$recpntPart = array_slice(preg_replace('/^[0]/', '88$0', $recipients), $i, $limit, true);
					$params['To'] = implode(',', $recpntPart);
					$result = $client->call('SendTextMultiMessage', $params);
				}
				//print_r($result);die;
				$params['sms_amount'] = $total_msgs;
				if ($result[0]['StatusText'] == 'success') {
					$new_credit = $this->update_sms_credit($sms_credit - $total_msgs);
					$insert_log = $this->insert_log($params);
					self::DisplaySuccess();
				} else {
					self::DisplayError();
				}
			}
		} catch (Exception $e) {
			self::DisplayError($e->getMessage());
		}
	}

	/**
	 * Designs for displaying Notices
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var $message - String - The message we are displaying
	 * @var $status   - Boolean - its either true or false
	 */
	public static function admin_notice($message, $status = true)
	{
		$class =  ($status) ? 'notice notice-success' : 'notice notice-error';
		$message = __($message, 'sample-text-domain');

		printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
	}

	/**
	 * Displays Error Notices
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public static function DisplayError($message = "Aww!, there was an error.")
	{
		add_action('admin_notices', function () use ($message) {
			self::admin_notice($message, false);
		});
	}

	/**
	 * Displays Success Notices
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public static function DisplaySuccess($message = "Successful!")
	{
		add_action('admin_notices', function () use ($message) {
			self::admin_notice($message, true);
		});
	}


	/*
	 * Get Current Sms Credit
	 */
	public function get_sms_credit()
	{
		return get_option('sms_credit');
	}

	/*
	 * Get Current Sms Credit
	 */
	public function update_sms_credit($value)
	{
		update_option('sms_credit', $value);
		return get_option('sms_credit');
	}

	/*
	 * insert into sms_log
	 */
	public function insert_log($params)
	{
		global $wpdb;
		$table = $wpdb->prefix . 'sendex';
		$data = array('date' => date('Y-m-d H:i:s'), 'sender' => $params['From'], 'message' => $params['Message'], 'recipient' => $params['To'], 'sms_amount' => $params['sms_amount'], 'status' => 'Success');
		$wpdb->insert($table, $data);
	}

	/*
	* Send OTP function
	*/
	public function send_otp()
	{
		global $wpdb;
		$table = "phone_verification";
		$phone = $_GET['phone'];
		$otp = rand(1000, 9999);

		$status = 1;
		$send_otp = 1;

		$wpdb->delete(
			$table,
			['phone' => $phone],
			['%s']
		);

		$wpdb->insert($table, ['phone' => $phone, 'otp' => $otp], ['%s', '%d']);

		$message = "OTP sent to your mobile no. ";

		//Send SMS//
		if ($send_otp == 1) {
			$sms_msg = "Your Verification Code is " . $otp . ".";

			$this->send_message($phone, $sms_msg);
		}
		//
		return wp_send_json(array('status' => $status, 'message' => $message, 'phone' => $phone));
		wp_die();
	}


	/*
	*  check for otp is valid or not
	*/
	public function check_is_valid_otp()
	{
		global $wpdb;
		$table = "phone_verification";
		$phone = $_GET['phone'];
		$otp = $_GET['otp'];

		$count1 = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE phone='$phone'");

		if ($count1 > 0) {
			$PhoneVerification = $wpdb->get_row("SELECT * FROM $table WHERE phone='$phone'");
			$is_verified = $PhoneVerification->is_verified;

			if ($is_verified == 1) {
				$status = 1;
				$message = "This phone no. is already verified";
			} else {
				$count = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE phone='$phone' AND otp='$otp' ");

				if ($count > 0) {
					$status = 1;
					$message = "Phone no. successfully verified";

					$wpdb->update($table, ['is_verified' => 1], ['phone' => $phone, 'otp' => $otp], ['%d'], ['%s']);
				} else {
					$status = 0;
					$message = "Incorrect OTP";
				}
			}
		} else {
			$status = 0;
			$message = "No record for this phone no.";
		}

		return wp_send_json(array('status' => $status, 'message' => $message));

		wp_die();
	}
		
	
	public function add_phone_field($user_id)
	{
		update_user_meta($user_id, 'billing_phone', $_POST['phone']);
	}
/*
 * Apply number field
 */
	function wooc_add_phone_number_field() {
	    return apply_filters( 'woocommerce_forms_field', array(
		'wooc_user_phone' => array(
		    'type'        => 'text',
		    'label'       => __( 'Phone Number', ' woocommerce' ),
		    'placeholder' => __( 'Your phone number', 'woocommerce' ),
		    'required'    => true,
		),
	    ) );
	}
/*
 * Add phone number field on Register
 */
	function wooc_add_field_to_registeration_form() {
	    $fields = wooc_add_phone_number_field();
	    foreach ( $fields as $key => $field_args ) {
		woocommerce_form_field( $key, $field_args );
	    }
	}
}
