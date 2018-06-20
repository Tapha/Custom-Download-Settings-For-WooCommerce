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
		$this->select_id = '_custom_download_field';

	}

    public function cds_product_custom_fields() {
         global $woocommerce, $post;
         
			echo '<div class=" product_custom_field ">';

			$meta_check_result = $this->cds_meta_data_check($post->ID);

			if ($meta_check_result != 5)
			{
				echo "<input id='current_download_setting' type='hidden' name='current_download_setting' value='".$meta_check_result."'>"; //Hidden input field with current download setting data.
			}
			// This function has the logic of creating custom field
			// Custom Product Text Field
		    woocommerce_wp_select( 
			array( 
				'id'          => $this->select_id, 
				'label'       => __('Download Setting'), 
				'description' => __('Choose a download setting for this product.'),
				'value'       => get_post_meta($post->ID, $this->select_id, true),
				'options' => array(
					'one'  => __('Force Download'),
					'two'   => __('X-Accel-Redirect/X-Sendfile'),
					'three' => __('Redirect Only')
					)
				)
			);
			echo '</div>';
    }

    public function cds_product_custom_fields_save() {
    	global $woocommerce, $post;
         // Custom Product Text Field
	    $woocommerce_custom_product_download_field = $_POST['_custom_download_field'];
	    if (!empty($woocommerce_custom_product_download_field))
	        update_post_meta($post->ID, '_custom_download_field', esc_attr($woocommerce_custom_product_download_field));
    }

    public function cds_product_custom_quick_edit_fields() {
    	global $woocommerce, $post;

    	$meta_check_result = $this->cds_meta_data_check($post->ID);

        echo "<div class='cds_quickedit_field' style='position: relative; top: 30px; left: -189px;'>
		        <label class='alignleft'>
		            <div class='title'>";
		echo _e('Download Setting', 'woocommerce' );
		echo "</div>";

		if ($meta_check_result != 5)
		{
			echo "<input id='current_download_setting' type='hidden' name='current_download_setting' value='".$meta_check_result."'>"; //Hidden input field with current download setting data.
		}		

		echo "<select id='custom_download_select' name='_custom_download_field'>";
			
			echo "<option value='one'>Force Download</option>";
			echo "<option value='two'>X-Accel-Redirect/X-Sendfile</option>";
			echo "<option value='three'>Redirect Only</option>";
				  
		echo "</select>
	          </label>
	    	  </div>";
    }

    public function cds_product_custom_quick_edit_fields_save($product)
    {
    	global $woocommerce, $post;
    	/*
		Notes:
		$_REQUEST['_custom_field_demo'] -> the custom field we added above
		Only save custom fields on quick edit option on appropriate product types (simple, etc..)
		Custom fields are just post meta
		*/

		if ( $product->is_type('simple') || $product->is_type('external') ) {

		    $post_id = $product->id;

		    if ( isset( $_REQUEST['_custom_download_field'] ) ) {

		        $customDwnload = trim(esc_attr( $_REQUEST['_custom_download_field'] ));

		        // Do sanitation and Validation here

		        update_post_meta( $post_id, '_custom_download_field', wc_clean( $customDwnload ) );
		    }

		}
    }

    public function cds_product_posts_insert_edit($column)
    {	
    	global $woocommerce, $post;

    	$post_id = $post->ID;

    	switch ( $column ) {
		    case 'name' :
		      
		        echo "<div class='hidden custom_download_field_inline' id='custom_download_field_inline_".$post_id."'>
		            	<div id='_custom_download_field'>".get_post_meta($post_id, '_custom_download_field', true)."</div>
		        	  </div>";

		        break;

		    default :
		        break;
		}

    }

    private function cds_meta_data_check($post_id)
    {
    	/*Check product to see if meta data for custom download field is set,
    	* if not then check the current default setting on the site and then set that to be
    	* the default setting in the custom download setting field.
    	*/ 

    	$custom_download_field_check = metadata_exists('post', $post_id, '_custom_download_field');

    	if ($custom_download_field_check == false)
    	{
    		//Get default site setting and return corresponding option.
    		$file_download_method = get_option( 'woocommerce_file_download_method', 'force' );

    		//Check setting and return corresponding select position to method.

    		switch ($file_download_method) {
    			case 'force':
    				return 'one';
    				break;
    			case 'xsendfile':
    				return 'two';
    				break;
    			case 'redirect':
    				return 'three';
    				break;    			
    			default:
    				return false;
    				break;
    		}
    	}
    	else
    	{
    		return 5;
    	}
    }

    private function cds_get_custom_download_setting($post_id = 1)
    {
    	$custom_download_setting = get_post_meta($post_id, '_custom_download_field', true);

    	switch ($custom_download_setting) {
    			case 'one':
    				return 'force';
    				break;
    			case 'two':
    				return 'xsendfile';
    				break;
    			case 'three':
    				return 'redirect';
    				break;    			
    			default:
    				return false;
    				break;
    	}
    }

    public function cds_download_reroute($file_path, $filename)
    {
    	global $woocommerce, $post;

    	//Retrieve product information.

    	$product_id = absint( $_GET['download_file'] ); // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected, WordPress.VIP.ValidatedSanitizedInput.InputNotValidated
		//$product    = wc_get_product( $product_id );
    	
    	//Check to see if custom setting is enabled.
    	$download_setting = $this->cds_meta_data_check($product_id);

    	//If it is, then reroute to the download method in the custom setting.
    	if ($download_setting == 5)
    	{	
    		$file_download_method = $this->cds_get_custom_download_setting($product_id);

    		// Trigger download via the customly set method
        	do_action( 'woocommerce_download_file_' . $file_download_method, $file_path, $filename );
    	}
    	else
    	{
    		$file_download_method = $this->cds_download_setting_stter($download_setting);

    		//If not then simply allow the next action in the lifecycle to run.
    		do_action( 'woocommerce_download_file_' . $file_download_method, $file_path, $filename );
    	}
    }

    private function cds_download_setting_stter($option)
    {
    	switch ($option) {
    			case 'one':
    				return 'force';
    				break;
    			case 'two':
    				return 'xsendfile';
    				break;
    			case 'three':
    				return 'redirect';
    				break;    			
    			default:
    				return false;
    				break;
    		}
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
