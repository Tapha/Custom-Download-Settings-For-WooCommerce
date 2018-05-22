<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wiserdev.com
 * @since      1.0.0
 *
 * @package    Custom_Download_Settings
 * @subpackage Custom_Download_Settings/includes
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
 * @package    Custom_Download_Settings
 * @subpackage Custom_Download_Settings/includes
 * @author     WiserDev Ltd <tapha@wiserdev.com>
 */
class Custom_Download_Settings {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Custom_Download_Settings_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'custom-download-settings';

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
	 * - Custom_Download_Settings_Loader. Orchestrates the hooks of the plugin.
	 * - Custom_Download_Settings_i18n. Defines internationalization functionality.
	 * - Custom_Download_Settings_Admin. Defines all hooks for the admin area.
	 * - Custom_Download_Settings_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-custom-download-settings-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-custom-download-settings-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-custom-download-settings-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-custom-download-settings-public.php';

		$this->loader = new Custom_Download_Settings_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Custom_Download_Settings_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Custom_Download_Settings_i18n();

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

		$plugin_admin = new Custom_Download_Settings_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// The code for displaying WooCommerce Product Custom Fields
		$this->loader->add_action( 'woocommerce_product_options_downloads', $plugin_admin, 'cds_product_custom_fields' );

		// This saves WooCommerce Product Custom Fields
		$this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'cds_product_custom_fields_save' );

		//This creates a custom field on the Quick Edit Section of product posts.
		$this->loader->add_action( 'woocommerce_product_quick_edit_end', $plugin_admin, 'cds_product_custom_quick_edit_fields');

		//This saves the custom field on the Quick Edit Section of product posts.
		$this->loader->add_action( 'woocommerce_product_quick_edit_save', $plugin_admin, 'cds_product_custom_quick_edit_fields_save');

		//This is to insert the new data into the the custom field via a hidden field. JS reads from here and populates custom field with new data.
		$this->loader->add_action( 'manage_product_posts_custom_column', $plugin_admin, 'cds_product_posts_insert_edit');

		//Intercepting the download requests to do a check of custom settings and then applying the appropriate custom download method.

		//This is to handle forced download requests.
		$this->loader->add_action( 'woocommerce_download_file_force', $plugin_admin, 'cds_download_reroute', 9, 2);

		//This is to handle redirect download requests.
		$this->loader->add_action( 'woocommerce_download_file_redirect', $plugin_admin, 'cds_download_reroute', 9, 2);

		//This is to handle xsendfile download requests.
		$this->loader->add_action( 'woocommerce_download_file_xsendfile', $plugin_admin, 'cds_download_reroute', 9, 2);

		//Filters
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Custom_Download_Settings_Public( $this->get_plugin_name(), $this->get_version() );

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
	 * @return    Custom_Download_Settings_Loader    Orchestrates the hooks of the plugin.
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
