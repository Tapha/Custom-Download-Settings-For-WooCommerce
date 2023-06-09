(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

//Code starts here

/**
  * This pulls in the download setting in the hidden input set by 
  * the cds_product_posts_insert_edit method and displays it in the 
  * currently displayed select.
*/

$(function() {
		//Edit Post Pages
		$("input[name=_downloadable]").change(function() {
    		
    		if(this.checked) {
				var $download_setting = $("#current_download_setting").val();

				//Update selected to the current download setting
				$( 'select[name="_custom_download_field"] option[value="' + $download_setting + '"]').attr( 'selected', 'selected' );
			}
		});
		
		if ($('input#_downloadable').is(':checked')) {
			var $download_setting = $("#current_download_setting").val();

			//Update selected to the current download setting
			$( 'select[name="_custom_download_field"] option[value="' + $download_setting + '"]').attr( 'selected', 'selected' );
		}

		//Quick Edit
		$('#the-list').on('click', '.editinline', function(){			

		    /**
		     * Extract metadata and put it as the value for the custom field form
		     */
		    inlineEditPost.revert();

		    var post_id = $(this).closest('tr').attr('id');

		    post_id = post_id.replace("post-", "");

		    var $cfd_inline_data = $('#custom_download_field_inline_' + post_id),
		        $wc_inline_data = $('#woocommerce_inline_' + post_id );

		    var $custom_edit_field_value = $cfd_inline_data.find("#_custom_download_field").text();

		    $('select[name="_custom_download_field"] option').removeAttr("selected");

		    if ($custom_edit_field_value == '')
		    {
		    	//If no custom value is set

			    var $download_setting = $("#current_download_setting").val();

				//Update selected to the current download setting
					
				if (($download_setting == 'one') || ($download_setting == 'two') || ($download_setting == 'three'))
				{
					$( 'select[name="_custom_download_field"] option[value="' + $download_setting + '"]').attr( 'selected', 'selected' );
				}
		    }
		    else
		    {
		    	$( 'select[name="_custom_download_field"] option[value="' + $custom_edit_field_value + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
		    }

		    console.log($custom_edit_field_value);

		    $custom_edit_field_value = '';

		    /**
		     * Only show custom field for appropriate types of products (simple)
		     */
		    var product_type = $wc_inline_data.find('.product_type').text();

		    if (product_type=='simple' || product_type=='external') {
		        $('.cds_quickedit_field', '.inline-edit-row').show();
		    } else {
		        $('.cds_quickedit_field', '.inline-edit-row').hide();
		    }

		});

  });

})( jQuery );
