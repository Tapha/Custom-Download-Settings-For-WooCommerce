<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wiserdev.com
 * @since      1.0.0
 *
 * @package    Custom_Download_Settings
 * @subpackage Custom_Download_Settings/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Custom_Download_Settings
 * @subpackage Custom_Download_Settings/admin
 * @author     WiserDev Ltd <tapha@wiserdev.com>
 */
class Custom_Download_Settings_Admin {

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
	 * The select ID for the custom download setting field.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $select_id   The select ID for the custom download setting field.
	 */
	private $select_id;

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

		//Set select ID
		$this->select_id = '_custom_product_download_select_field';

	}

    public function cds_product_custom_fields() {
         global $woocommerce, $post;
			echo '<div class=" product_custom_field ">';
			// This function has the logic of creating custom field
			// Custom Product Text Field
		    woocommerce_wp_select( 
			array( 
				'id'          => $this->select_id, 
				'label'       => __('Download Setting'), 
				'description' => __('Choose a download setting for this product.'),
				'value'       => get_post_meta($post->ID, $this->select_id, true),
				'options' => array(
					'one'   => __('Redirect Only'),
					'two'   => __('Force Download'),
					'three' => __('X-Accel-Redirect/X-Sendfile')
					)
				)
			);
			echo '</div>';
    }

    public function cds_product_custom_fields_save() {
         // Custom Product Text Field
	    $woocommerce_custom_product_download_field = $_POST['_custom_product_download_select_field'];
	    if (!empty($woocommerce_custom_product_download_field))
	        update_post_meta($post_id, '_custom_product_download_select_field', esc_attr($woocommerce_custom_product_download_field));
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
		 * defined in Custom_Download_Settings_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Custom_Download_Settings_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/custom-download-settings-admin.css', array(), $this->version, 'all' );

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
		 * defined in Custom_Download_Settings_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Custom_Download_Settings_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/custom-download-settings-admin.js', array( 'jquery' ), $this->version, false );

	}

}
