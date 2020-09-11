<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link        http://menusms.com/dongido
 * @since      1.0.0
 *
 * @package    Sendex
 * @subpackage Sendex/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Sendex
 * @subpackage Sendex/includes
 * @author     Shams <shamsiarhamid@gmail.com>
 */
class Sendex {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Sendex_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SENDEX_VERSION' ) ) {
			$this->version = SENDEX_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'sendex';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Sendex_Loader. Orchestrates the hooks of the plugin.
	 * - Sendex_i18n. Defines internationalization functionality.
	 * - Sendex_Admin. Defines all hooks for the admin area.
	 * - Sendex_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sendex-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sendex-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sendex-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sendex-public.php';

		$this->loader = new Sendex_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Sendex_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Sendex_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Sendex_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		// Add setting menu item 
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_sendex_admin_setting' );

		// Saves and update settings
		$this->loader->add_action( 'admin_init', $plugin_admin, 'sendex_admin_settings_save' );
		
		// Hook our sms page
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_sendex_sms_page' );
		
		// calls sending function whenever we try sending messages.
		$this->loader->add_action( 'admin_init', $plugin_admin, 'send_message' );
		//calls otp fuctions
		// $this->loader->add_action('wp_ajax_send_otp', $plugin_admin, 'send_otp');
		// $this->loader->add_action('wp_ajax_nopriv_send_otp', $plugin_admin, 'send_otp');
		// $this->loader->add_action('wp_ajax_check_is_valid_otp', $plugin_admin, 'check_is_valid_otp');
		// $this->loader->add_action('wp_ajax_nopriv_check_is_valid_otp', $plugin_admin, 'check_is_valid_otp');
		// $this->loader->add_action('woocommerce_register_form', $plugin_admin, 'woocommerce_register_form_add_phone_otp', 10, 1);
		// $this->loader->add_action('user_register', $plugin_admin, 'add_phone_field');    
		// $this->loader->add_action('personal_options_update', $plugin_admin, 'add_phone_field' );    
		// $this->loader->add_action('edit_user_profile_update', $plugin_admin,'add_phone_field' );    

		//add_action( 'woocommerce_register_form', 'wooc_add_field_to_registeration_form', 15 );
		//remove wordpress authentication
//remove_filter('authenticate', 'wp_authenticate_username_password', 20);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Sendex_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Sendex_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
