<?php

// require the Dompdf autoload file
require_once 'dompdf/vendor/autoload.php';

// reference the Dompdf namespace
use Dompdf\Dompdf;



class WebbsitesForm
{
	// Form Parameters
	public $post_id;
	public $post_title;
	public $post_name;
	public $form_fields;
	public $status;
	public $message;
	public $section_count;

	public $button_css_class;
	public $button_css_id;
	public $button_label;
	public $button_align;
	public $css_class;
	public $css_id;
	public $form_align;
	public $form_css_class;
	public $form_css_id;
	public $form_custom_slug;
	public $form_elements;
	public $form_id;
	public $form_keys;
	public $form_recipient;
	public $form_send_receipt;
	public $form_send_reply;
	public $form_sender;
	public $form_sender_name;
	public $form_skin;
	public $form_type = 'build';
	public $form_width;
	public $form_width_unit;
    public $form_display_visibility;
    public $form_display_vis_table;
    public $form_placeholder_text;
    public $form_visibility;
	public $hide_name_email;
	public $output_slug;
	public $form_success_message;
	public $form_error_message;
	public $form_success_url;
	public $elements_base64;
	public $raw_elements;
	public $save_email_responses;

	// Options
    public $opt_names;
    public $opt_vals;

	// public $daemon_id;
	// public $default_sender;
	// public $default_recipient;
    // public $default_success_msg;
    // public $default_error_msg;
	// public $mx_email;
	// public $honeypot_id;
	// public $dominant_color;
	// public $text_color;
	// public $header_color;
	// public $background_color;
	// public $field_color;
	// public $field_border_color;
	// public $field_border_width;
	// public $field_border_radius;

	// Paths
	public $path_to_plugin;
	public $uploads_dir_url;
	public $uploads_dir_path;
	public $path_to_custom_output;
	public $working_folder_path;
	public $path_to_display_file;
	public $path_to_custom_forms;

    // Others
	public $timezone;
	public $server_name;
	public $site_name;
	public $form_parameters;
	public $form_input_types;
    public $weekdays;

    public $error = false;
    public $error_msg = '';
    public $output_atts = null;
    public $output_msg = [];





	// Constructor
	function __construct( $post_id = null )
	{
        $this->set_app_options();

        // If form_id supplied, get the form
        if( $post_id != null ) $this->wsform( [ 'post_id' => $post_id ] );

        // Set the weekdays
        $this->weekdays = [ 'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday' ];
	}



    private function set_app_options()
    {
        // Set settings data
        $this->settings_prefix 		= 'wsform-form';
        $this->settings_groupname 	= $this->settings_prefix . '-settings-group';
		$this->server_name          = $_SERVER['SERVER_NAME'];
		$this->site_name            = get_bloginfo( 'name' );
        
        // Set Options With Initial Defaults
        $this->opt_names = new stdClass();

        $this->opt_names->daemon_id            = array( '_wsform_opt_daemon_id',                'wf' );
        $this->opt_names->default_sender       = array( '_wsform_opt_default_sender',           'no-reply@' . $_SERVER['SERVER_NAME'] );
        $this->opt_names->default_recipient    = array( '_wsform_opt_default_recipient',        get_option( 'admin_email' ) );
        $this->opt_names->default_success_msg  = array( '_wsform_opt_default_success_message',  'Your message was successfully sent!' );
        $this->opt_names->default_error_msg    = array( '_wsform_opt_default_error_message',    'Sorry, there was a problem.' );
        $this->opt_names->mx_email             = array( '_wsform_opt_mx_email',                 null );
        $this->opt_names->honeypot_id          = array( '_wsform_opt_honeypot_id',              'wsform_url' );
        $this->opt_names->dominant_color       = array( '_wsform_opt_dominant_color',           'rgb(68, 68, 68)' );
        $this->opt_names->text_color           = array( '_wsform_opt_text_color',               'rgb(34, 34, 34)' );
        $this->opt_names->header_color         = array( '_wsform_opt_header_color',             'rgb(119, 119, 119)' );
        $this->opt_names->background_color     = array( '_wsform_opt_background_color',         'rgb(238, 238, 238)' );
        $this->opt_names->field_color          = array( '_wsform_opt_field_color',              'rgb(255, 255, 255)' );
        $this->opt_names->field_border_color   = array( '_wsform_opt_field_border_color',       'rgba(0, 0, 0, 0)' );
        $this->opt_names->field_border_width   = array( '_wsform_opt_field_border_width',       '1px' );
        $this->opt_names->field_border_radius  = array( '_wsform_opt_field_border_radius',      5 );

        // Set all options
        $this->opt_vals = new stdClass();

        foreach( $this->opt_names as $key => $arr )
        {
            $this->opt_vals->$key = get_option( $arr[0], $arr[1] );
        }

        // print_array( $this->opt_vals );

    }


	// function form_options()
	// {
	// 	$this->daemon_id			= get_option( '_wsform_opt_daemon_id' );
	// 	$this->default_sender		= get_option( '_wsform_opt_default_sender' );
	// 	$this->default_recipient	= get_option( '_wsform_opt_default_recipient' );
	// 	$this->default_success_msg	= esc_attr( get_option( '_wsform_opt_default_success_message' ) );
	// 	$this->default_error_msg	= esc_attr( get_option( '_wsform_opt_default_error_message' ) );
	// 	$this->mx_email				= get_option( '_wsform_opt_mx_email' );
	// 	$this->honeypot_id			= get_option( '_wsform_opt_honeypot_id' );
	// 	$this->dominant_color		= get_option( '_wsform_opt_dominant_color' );
	// 	$this->accent_color			= get_option( '_wsform_opt_accent_color' );
	// }






	function set_form_constants()
	{
		$this->path_to_plugin 			= WSFORM_PATH_TO_PLUGIN;
		$this->uploads_dir_url 			= WSFORM_UPLOADS_DIR;
		$this->uploads_dir_path 		= WSFORM_UPLOADS_DIR_PATH;
		$this->path_to_custom_output 	= WSFORM_PATH_TO_CUSTOM_OUTPUT;
		$this->working_folder_path 		= WSFORM_UPLOADS_DIR_PATH;
		$this->path_to_display_file 	= WSFORM_PATH_TO_DISPLAY_FORMS;
		$this->path_to_custom_forms 	= WSFORM_PATH_TO_CUSTOM_FORMS;
	}




	function wsform( $args )
	{
		/*
		 *
		 *
		 *
		$args = array(
			'post_id' => integer,
			'form_id' => integer,
			'slug'    => string
		);
		 *
		 *
		 *
		*/

		// Populate several properties
		// $this->form_constants();
		// $this->form_options();
        $this->set_form_constants();
		$this->form_input_types = $this::form_input_types();
		$this->form_parameters  = $this::form_parameters();

		// If requested by form id
		if( ! empty( $args['form_id'] ) )
		{
			$this->form_id = $args['form_id'];

			// Get the post ID from the form ID
			$this->get_post_id_from_form_id();

			// Get the form info
			$this->get_form_data();
		}

		// If requested by post id
		if( ! empty( $args['post_id'] ) )
		{
			$this->post_id = $args['post_id'];

			// Get the form info
			$this->get_form_data();
		}
	}







	// get post ID by meta value
	public function get_post_id_from_form_id()
	{
		$form_id = $this->form_id;

		// Use a SQL query to get the post ID of the form
		global $wpdb;
		$res = $wpdb->get_results( "select post_id from $wpdb->postmeta where meta_value = '$form_id' AND meta_key = '_wsform_form_id'", ARRAY_A );

		//print_array( $res );
		//die;

		$this->post_id = $res[0]['post_id'];

		return true;
	}




	// Build the form
	function build_form( $args )
	{
		$this->wsform( $args );

		if( $this->form_type == 'custom' ) $this->build_custom_form();
		else $this->assemble_form();
	}



	private function build_custom_form()
	{
		$form_addr = wsform_PATH_TO_CUSTOM_FORMS . '/' . $this->form_custom_slug;

		if( ! file_exists( $form_addr ) ) return false;

		ob_start();
		require( $form_addr );
		$form = ob_get_clean();

		//$headers = wsform_ident( $fields );

		$ident = '<input type="hidden" name="wf_id" value="' . $this->form_id . '" />';

		$form = str_replace( '</form>', $ident . '</form>', $form );

		echo $form;

	}



	// get fields by post id
	function get_form_data( $post_id = null )
	{
		// Get the post ID from the	object if it's not given
		if( $post_id == null ) $post_id = $this->post_id;
		else $this->post_id = $post_id;

		// If form_id is zero, set it
		if( $this->form_id < 1 ) $this->the_form_id();

		// Get the post title, save as form_title
		$this->post_title = get_the_title( $post_id );

		// Get the post_name, save as post_name
		$this->post_name = get_post_field( 'post_name', $post_id );

		// Get the raw fields, save as form_fields
		$fields = get_post_meta( $this->post_id );
		$this->form_fields = $fields;

		// Populate properties
		$this->form_type		= key_exists( '_wsform_form_type', $fields ) ? 		trim( $fields['_wsform_form_type'][0] ) : 		'build';
		$this->form_width	    = key_exists( '_wsform_form_width', $fields ) ? 		trim( $fields['_wsform_form_width'][0] ) : 	    null;
		$this->form_width_unit	= key_exists( '_wsform_form_width_unit', $fields ) ?	trim( $fields['_wsform_form_width_unit'][0] ) :  null;
		$this->form_align		= key_exists( '_wsform_form_align', $fields ) ? 		trim( $fields['_wsform_form_align'][0] ) : 		null;

		if( $this->form_type == 'build' )
		{
			$this->elements_base64 	= key_exists( '_wsform_form_elements', $fields ) ? 		trim( $fields['_wsform_form_elements'][0] ) : 	'';
			$this->raw_elements 	= base64_decode( $this->elements_base64 );
			$this->parse_elements();
			$this->form_keys();
		}
		else
		{
			$this->form_custom_slug			= trim( $fields['_wsform_form_custom_slug'][0] );
		}

		$this->form_recipient		= key_exists( '_wsform_form_recipient', $fields ) ? 			trim( $fields['_wsform_form_recipient'][0] ) : 				'';
		$this->form_send_receipt	= key_exists( '_wsform_form_send_receipt', $fields ) ? 		trim( $fields['_wsform_form_send_receipt'][0] ) : 		'';
		$this->form_send_reply		= key_exists( '_wsform_form_send_reply', $fields ) ? 			trim( $fields['_wsform_form_send_reply'][0] ) : 			'';
		$this->hide_name_email		= key_exists( '_wsform_hide_name_email', $fields ) ? 			trim( $fields['_wsform_hide_name_email'][0] ) : 			null;
		$this->output_slug			= key_exists( '_wsform_output_slug', $fields ) ? 					trim( $fields['_wsform_output_slug'][0] ) : 					'';
		$this->form_success_message	= key_exists( '_wsform_form_success_message', $fields ) ? trim( $fields['_wsform_form_success_message'][0] ) :	'';
		$this->form_error_message	= key_exists( '_wsform_form_error_message', $fields ) ? 	trim( $fields['_wsform_form_error_message'][0] ) :		'';
		$this->form_success_url		= key_exists( '_wsform_form_success_url', $fields ) ? 		trim( $fields['_wsform_form_success_url'][0] ) : 			'';
		$this->form_skin			= key_exists( '_wsform_form_skin', $fields ) ? 						trim( $fields['_wsform_form_skin'][0] ) : 						'basic';
		$this->form_sender			= key_exists( '_wsform_form_sender', $fields ) ? 					trim( $fields['_wsform_form_sender'][0] ) : 					'';
		$this->form_sender_name		= key_exists( '_wsform_form_sender_name', $fields ) ? 		trim( $fields['_wsform_form_sender_name'][0] ) : 			'';
		$this->css_id				= key_exists( '_wsform_css_id', $fields ) ? 							trim( $fields['_wsform_css_id'][0] ) : 								'';
		$this->css_class			= key_exists( '_wsform_css_class', $fields ) ? 						trim( $fields['_wsform_css_class'][0] ) : 						'';
		$this->form_css_id			= key_exists( '_wsform_form_css_id', $fields ) ? 					trim( $fields['_wsform_form_css_id'][0] ) : 					'';
		$this->form_css_class		= key_exists( '_wsform_form_css_class', $fields ) ? 			trim( $fields['_wsform_form_css_class'][0] ) : 				'';
		$this->button_label			= key_exists( '_wsform_button_label', $fields ) ? 				trim( $fields['_wsform_button_label'][0] ) : 					'Submit';
		$this->button_css_id		= key_exists( '_wsform_button_css_id', $fields ) ? 				trim( $fields['_wsform_button_css_id'][0] ) : 				'';
		$this->button_css_class		= key_exists( '_wsform_button_css_class', $fields ) ? 		trim( $fields['_wsform_button_css_class'][0] ) : 			'';
		$this->button_align			= key_exists( '_wsform_button_align', $fields ) ? 				trim( $fields['_wsform_button_align'][0] ) : 					'align-center';

        $this->form_display_visibility = key_exists( '_wsform_display_visibility', $fields ) ? trim( $fields['_wsform_display_visibility'][0] ) : 'always';
        $this->form_placeholder_text = key_exists( '_wsform_visibility_placeholder_text', $fields ) ? trim( $fields['_wsform_visibility_placeholder_text'][0] ) : '';
        $this->form_display_vis_table = key_exists( '_wsform_display_vis_table', $fields ) ? trim( $fields['_wsform_display_vis_table'][0] ) : $this->vis_table_first_data();
        
		if( empty( $this->button_label) ) $this->button_label = 'Submit';

	}








	private function assemble_form()
	{
        // First, check to see the form's visibility
        $this->check_visibility();

        if( $this->form_visibility != true )
        {
            $this->form_placeholder();
        }
        else
        {
            $this->form_begin();

            $this->form_inputs();
    
            $this->form_end();
        }
	}



    private function form_placeholder()
    {
        // Outputs placeholder text for forms whose visibility is set to hide
        _e( $this->form_placeholder_text );
    }



	private function form_begin()
	{
        // print_array( $this );

		// Wrapper css
		$classes = 'wsform-body ' . $this->form_skin;
		if( ! empty( $this->css_class ) ) $classes .= ' ' .$this->css_class;

		$div_id = ! empty( $this->css_id ) ? ' id="' . $this->css_id . '"' : '';

		$form_classes = 'wsform-form wsform-auto-form';
		if( ! empty( $this->form_css_class ) ) $form_classes .= ' ' . $this->form_css_class;

		$form_id = ! empty( $this->form_css_id ) ? ' id="' . $this->form_css_id . '"' : '';

		$form_style = '';
		$form_styles = array();

		// Get the width
		if( ! empty( $this->form_width ) )
		{
			$form_width = intval( $this->form_width );
			$form_width_unit = $this->form_width_unit == 'pct' ? '%' : $this->form_width_unit;

			$form_styles[]= 'max-width: ' . $form_width . $form_width_unit . ';';
		}

		// Get the alignment
		if( $this->form_align == 'left' )   $form_styles[]= 'float: left; margin: 0 1em 1em 0;';
		if( $this->form_align == 'right' )  $form_styles[]= 'float: right; margin: 0 0 1em 1em;';
		if( $this->form_align == 'center' ) $form_styles[]= 'float: none; margin: 0 auto 1em;';

		// If $style_info isn't empty, create the wrapper
		if( count( $form_styles ) > 0 ) $form_style = ' style="' . trim( implode( ' ', $form_styles ) ) . '"';

		// Get the page URI
		$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$page_uri = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		// Get the default success and error messages
        $opts = $this->opt_vals;
		$success_msg = ! empty( $this->form_success_message ) ? $this->form_success_message : $opts->default_success_msg;
		$error_msg = ! empty( $this->form_error_message ) ? $this->form_error_message : $opts->default_error_msg;

		?>

        <div id="wsform-body" class="<?php echo $classes ?>"<?php echo $form_style ?> data-wsform-form-skin="<?php echo $this->form_skin ?>">

			<form class="<?php echo $form_classes ?>"<?php echo $form_id ?> action="" data-success-msg="<?php echo $success_msg ?>" method="post" autocomplete="off" enctype="multipart/form-data">

				<input type="hidden" name="wf_id" value="<?php echo $this->form_id ?>" />
				<input type="hidden" name="wf_page_uri" value="<?php echo $page_uri ?>" />

                <div id="wsform-error-msg" class="wsform-status hidden"><?php echo $error_msg ?></div>

				<?php if( $this->hide_name_email != 'checked' ) : ?>
				<div id="wsform-yourname" class="wsform-line required-input-field not-passed">
					<div class="wsform-input-div yourname" data-field-desc="your-name">
						<span class="wsform-label wsform-label-your-name">Your Name</span>
						<div class="wsform-input-wrapper">
							<input id="wf_yourname" class="wsform-input text wsform-required-input" name="wf_sender_name" type="text" value="" placeholder="Your Name">
						</div>
					</div>
				</div>

				<div id="wsform-youremail" class="wsform-line required-input-field not-passed">
					<div class="wsform-input-div email" data-field-desc="your-email">
						<span class="wsform-label wsform-label-your-name">Your Email</span>
						<div class="wsform-input-wrapper">
							<input id="wf_youremail" class="wsform-input email wsform-required-input" name="wf_sender_email" type="email" value="" placeholder="Your Email">
						</div>
					</div>
				</div>
				<?php endif ?>

		<?php

	}







	function form_inputs()
	{

		// Set up progress bar
		$this->progress_bar();

		// Count the sections. If more than zero, begin a counter
		if( $this->section_count > 0 ) $sec_ctr = 1;

		$els = $this->form_elements;

        print_array( $els, 1 );
        
		if( empty( $els ) ) return false;

		if( count( $els ) < 1 ) return false;

		// Begin loop of all elements
		for( $a = 0 ; $a < count( $els ) ; $a++ ) :

		$el_class = array();

		$el = $els[$a];
		$q = $a + 1;

		if( property_exists( $el, 'wf_required' ) && $el->wf_required == 'checked' ) $required = 1;
			else $required = 0;

		if( property_exists( $el, 'wf_allow_multiple' ) && $el->wf_allow_multiple == 'checked' ) $multiple = 1;
			else $multiple = 0;

		if( property_exists( $el, 'wf_default' ) ) $default = $el->wf_default;
			else $default = '';

		if( property_exists( $el, 'wf_placeholder' ) ) $placeholder = $el->wf_placeholder;
			else $placeholder = '';

		if( property_exists( $el, 'wf_hidden' ) ) $el_hidden = $el->wf_hidden;
			else $el_hidden = '';

		// If the item requires options to be set and the options are empty, skip it
		if( in_array( $el->wf_type, array( 'checkbox', 'select', 'radio' ) ) && empty( $el->wf_options ) ) continue;


		// If it's a section header, complete this section
		if( $el->wf_type == 'section' ) :

			if( $a > 0 ) : ?>

				</div><!-- end wsform-section-con -->

			</div><!-- end wsform-section -->

			<?php endif;

		// Begin section
		?>

			<div class="wsform-section wsform-sec-<?php echo $sec_ctr; if( $sec_ctr == 1 ) echo ' active' ?>" data-section-nbr="<?php echo $sec_ctr ?>">

				<div class="wsform-section-con">

					<h1 class="wsform-section-header"><?php echo $el->wf_label ?></h1>

					<p class="wsform-section-desc"><?php echo $default ?></p>

		<?php

		// Increase section counter
		$sec_ctr++;


		else :

		// print_array( $el );

		switch( $el->wf_type )
		{
			case 'checkbox' :

				// $return  = '<span class="wsform-label wsform-label-' . $el->wf_type . '">' . $el->wf_label . '</span>';

				$return  = '<div class="wsform-input-list">';

				for( $c = 0 ; $c < count( $el->wf_options_array ) ; $c++ )
				{
					$val = $el->wf_options_array[$c];

					if( count( $val ) > 1 )
					{
						$v = $val[0];
	 				  $l = $val[1];
					}
					else
					{
						$v = $l = $val[0];
					}

					$el_id = $el->wf_id . '_' . ( $c + 1 );

					$return .= '<label class="wsform-button-label label-for-checkbox" for="' . $el_id . '">' . $l .
					'<input type="checkbox" class="wsform-checkbox wsform-button-input';
					if( $required == 1 ) $return .= ' wsform-required-input';
					$return .= '" name="wf_input[' . $el->wf_id . '][]" value="' . $v . '" id="' . $el_id . '"';
					if( $v == $default ) $return .= ' checked';
					$return .=  ' /><span class="wsform-checkmark"></span></label>';
				}

				$return .= '</div>';

				break;

			case 'date' :
                // 2023-06-23
                $return  = '<input id="wf_' . $el->wf_id . '" class="wsform-input date';
				if( $required == 1 ) $return .= ' wsform-required-input';
                if( $default == 'current date') $default = date( 'Y-m-d' );
				$return .= '" name="wf_input[' . $el->wf_id . ']" type="date" value="' . $default . '" />';
                $return .= "<!-- current date: $default -->";
				break;

			case 'email' :
				$return = '<input id="wf_' . $el->wf_id . '" class="wsform-input email';
				if( $required == 1 ) $return .= ' wsform-required-input';
				$return .= '" name="wf_input[' . $el->wf_id . ']" type="email" placeholder="' . $placeholder . '" />';
				break;

			case 'file' :
				$accepts = array();
				$filetypes = '';

				if( property_exists( $el, 'wf_filetype_doc' )   && $el->wf_filetype_doc == 'checked' )   $accepts[] = 'text/*';
				if( property_exists( $el, 'wf_filetype_img' )   && $el->wf_filetype_img == 'checked' )   $accepts[] = 'image/*';
				if( property_exists( $el, 'wf_filetype_audio' ) && $el->wf_filetype_audio == 'checked' ) $accepts[] = 'audio/*';
				if( property_exists( $el, 'wf_filetype_pdf' )   && $el->wf_filetype_pdf == 'checked' )   $accepts[] = '.pdf';

				if( ! empty( $accepts ) ) $filetypes = implode( ',', $accepts );

				$label  = $multiple ? 'Choose file(s)...' : 'Choose a file...';
				$el_id = 'wf_' . $el->wf_id;


				// $return  = '<span class="wsform-label wsform-label-' . $el->wf_type . '">' . $el->wf_label . '</span>';

				$return  = '<div class="wsform-file-input-area"><input id="' . $el_id . '" class="wsform-input small wsform-file-input-button';
				if( $required == 1 ) $return .= ' wsform-required-input';
				$return .= '" name="wf_file_input_' . $el->wf_id . '[]" data-wsform-input-name="wf_file_input_' . $el->wf_id . '" data-wsform-input-id="' . $el->wf_id . '" type="file" ';
				if( $multiple == 1 ) $return .= 'multiple ';
				if( ! empty( $filetypes ) ) $return .= 'accept="' . $filetypes . '" ';
				$return .= '/><span><label for="' . $el_id . '">' . $label . '</label></span>';
				$return .= '<ul class="wsform-file-input-list"></ul><ul class="wsform-file-input-errors"></ul>';
				$return .= '</div>';
				break;

			case 'header' :
				$return = '<span class="wsform-header wsform-header-' . $el->wf_size . '">';
				if( $el->wf_size == 'h2' ) $return .= '<h2 class="wsform-header">' . $el->wf_label . '</h2>';
				if( $el->wf_size == 'h4' ) $return .= '<h4 class="wsform-header">' . $el->wf_label . '</h4>';
				if( $el->wf_size == 'h6' ) $return .= '<h6 class="wsform-header">' . $el->wf_label . '</h6>';
				$return .= '</span>';
				break;

			case 'hidden' :
				$return = '<input id="wf_' . $el->wf_id . '" name="wf_input[' . $el->wf_id . ']" type="hidden" value="' . $el_hidden . '" />';
				break;

			case 'number' :
				$return = '<input id="wf_' . $el->wf_id . '" class="wsform-input number';
				if( $required == 1 ) $return .= ' wsform-required-input';
				$return .= '" name="wf_input[' . $el->wf_id . ']" type="text" pattern="\d*" value="' . $default . '" />';
				break;

			case 'password' :
				$return = '<input id="wf_' . $el->wf_id . '" class="wsform-input password';
				if( $required == 1 ) $return .= ' wsform-required-input';
				$return .= '" name="wf_input[' . $el->wf_id . ']" type="password" autocomplete="off" placeholder="' . $el->wf_label . '" />';
				break;

			case 'radio' :

				// $return = '<span class="wsform-label wsform-label-' . $el->wf_type . '">' . $el->wf_label . '</span>';

				$return = '<div class="wsform-input-list">';

				for( $c = 0 ; $c < count( $el->wf_options_array ) ; $c++ )
				{
					$val = $el->wf_options_array[$c];

					if( count( $val ) > 1 )
					{
						$v = $val[0];
						$l = $val[1];
					}
					else
					{
						$v = $l = $val[0];
					}

					$id = $el->wf_id . '_' . ( $c + 1 );

					$return .= '<label class="wsform-button-label label-for-radio" for="' . $id . '">' . $l .
					'<input type="radio" class="wsform-radio-button wsform-button-input';
					if( $required == 1 ) $return .= ' wsform-required-input';
					$return .= '" name="wf_input[' . $el->wf_id . ']" value="' . $v .
					'" id="' . $id . '"';
					if( $v == $default ) $return .= ' checked';
					$return .=  ' /><span class="wsform-radio-button-span"></span></label>';
				}

				$return .= '</div>';

				break;

			case 'select' :

				$atts['id'] = $el->wf_id;
				$atts['required'] = $required;
				$atts['label'] = $el->wf_label;
				$atts['default'] = $default;
				$atts['options'] = $el->wf_options_array;
				$atts['multiple'] = $multiple;

				$return = self::select( $atts );

                // print_array( $atts );

				break;

			case 'states' :

				$atts['id'] = $el->wf_id;
				$atts['required'] = $required;
				$atts['label'] = $el->wf_label;
				$atts['default'] = $default;
				$atts['options'] = self::states_list( $el->wf_format );
				$atts['multiple'] = $multiple;

				$return = self::select( $atts );

				break;

			case 'textarea' :
				$return = '<textarea id="wf_' . $el->wf_id . '" class="wsform-input textarea';
				if( $required == 1 ) $return .= ' wsform-required-input';
				$return .= '" rows="' . $el->wf_lines . '" name="wf_input[' . $el->wf_id . ']" placeholder="' . $placeholder . '" noresize>' . $default . '</textarea>';
				break;

			case 'tel' :
				$return = '<input id="wf_' . $el->wf_id . '" class="wsform-input tel';
				if( $required == 1 ) $return .= ' wsform-required-input';
				$return .= '" name="wf_input[' . $el->wf_id . ']" type="tel" value="' . $default . '" placeholder="' . $placeholder . '" />';
				break;

			case 'time' :
				$return = '<input id="wf_' . $el->wf_id . '" class="wsform-input time';
				if( $required == 1 ) $return .= ' wsform-required-input';
				$return .= '" name="wf_input[' . $el->wf_id . ']" type="time" value="' . $default . '" />';
				break;

			case 'url' :
				$return = '<input id="wf_' . $el->wf_id . '" class="wsform-input url';
				if( $required == 1 ) $return .= ' wsform-required-input';
				$return .= '" name="wf_input[' . $el->wf_id . ']" type="text" value="' . $default . '" placeholder="' . $placeholder . '" />';
				break;

			case 'text' :
			default :
				$return  = '<input id="wf_' . $el->wf_id . '" class="wsform-input text';
				if( $required == 1 ) $return .= ' wsform-required-input';
				$return .= '" name="wf_input[' . $el->wf_id . ']" type="text" placeholder="' . $placeholder . '" ';
				if( ! empty( $default ) ) $return .= 'value="' . $default . '" ';
				$return .= '/>';
				break;

		}

		// output the HTML
		// label only
		if( in_array( $el->wf_type, array( 'header', 'hidden' ) ) ) : echo $return;

		else :

			$el_class[] = 'wsform-input-div';
			$el_class[] = $el->wf_type;
			if( ! empty( $default ) || $default == 'Select...' ) $el_class[] = 'occupado';

			$el_classes = implode( ' ', $el_class );

			?>

				<div id="wsform-line-<?php echo $el->wf_id ?>" class="wsform-line <?php echo $el->wf_id; if( $required ) echo ' required-input-field not-passed' ?>">

					<div id="wsform-input-div-<?php echo $el->wf_id ?>" data-field-desc="<?php echo $el->wf_type ?>" class="<?php echo $el_classes ?>">

						<span class="wsform-label wsform-label-<?php echo $el->wf_type ?>"><?php echo $el->wf_label ?></span>

						<div class="wsform-input-wrapper">

							<?php if( $el->wf_type == 'select' ) echo '<span class="wsform-select-arrow"></span>' ?>

							<?php echo $return; ?>

						</div>

					</div>

				</div>
		<?php

		endif;

		endif;

		endfor;

		//return ob_get_clean();

	}


	static function select( $atts )
	{
		if( ! key_exists( 'id', $atts ) ) return false;
		if( ! key_exists( 'options', $atts ) ) return false;
		if( ! key_exists( 'label', $atts ) ) return false;

		if( ! key_exists( 'placeholder', $atts ) ) $atts['placeholder'] = 'Select ...';
		if( ! key_exists( 'required', $atts ) ) $atts['required'] = false;
		if( ! key_exists( 'default', $atts ) ) 	$atts['default'] = false;
		if( ! key_exists( 'multiple', $atts ) )	$atts['multiple'] = false;

		$return  = '<div class="wsform-select-container">';
		$return .= '<input type="text" class="wsform-select-field wsform-input';
        if( $atts['required'] == true ) $return .= ' wsform-required-input';
        $return .= '" data-field-desc="select" name="wf_input[' . $atts['id'] . ']"';
		$return .= ' placeholder="' . $atts['placeholder'] . '"';
		if( $atts['default'] !== false ) $return .= ' value="' . $atts['default'] . '"';
		$return .= ' readonly /><select ';
		if( $atts['multiple'] == true ) $return .= 'multiple ';
		$return .= 'class="wsform-input-select';
		if( $atts['multiple'] == true ) $return .= ' multiple-input';
		if( $atts['required'] !== false ) $return .= ' wsform-required-input';
		$return .= '" data-field-id="' . $atts['id'] .'" data-field-label="'
		. $atts['label'] .'">';

        $return .= '<option value="">Select ...</option>';

		if( $atts['options'] != null ) :

		foreach( $atts['options'] as $val )
		{
			if( count( $val ) > 1 )
			{
				$v = trim( $val[0] );
				$d = trim( $val[1] );
			}
			else
			{
				$v = trim( $val[0] );
				$d = trim( $val[0] );
			}
			$return .= '<option value="' . $v . '"';
			if( $v == $atts['default'] ) $return .= ' selected';
			$return .= '>' . $d . '</option>';
		}

		endif;

		$return .= '</select></div>';

		return $return;

	}




	function form_end()
	{
        $opt = $this->opt_vals;
		?>
				<div id="<?php echo $opt->honeypot_id ?>" class="wsform-line">
					<div class="wsform-input-div">
						<div class="wsform-label">Leave Empty</div>
						<input id="<?php echo $opt->honeypot_id ?>-input" class="wsform-input" name="<?php echo $opt->honeypot_id ?>" type="text" placeholder="Leave Empty" data-field-desc="url-input" />
					</div>
				</div>

				<?php // If sections are used, end them here ?>

				<?php if( $this->section_count > 0 ) : ?>

					</div><!-- end wsform-section-con -->

				</div><!-- end wsform-section -->

				<?php endif; ?>

				<div class="wsform-submit-div<?php if( $this->section_count > 0 ) echo ' has-sections' ?>" id="wsform-submit-div">
					<?php if( $this->section_count > 0 ) : ?>
					<span class="wsform-prev-span"><button class="wsform-button wsform-previous-section wsform-hidden-button">&lt;</button></span>
					<?php endif ?>
					<span class="wsform-submit-span"><input<?php if( $this->section_count > 0 ) echo ' disabled="yes"' ?> class="wsform-button wsform-submit wsform-submit-button anim <?php echo $this->button_align ?> <?php if( ! empty( $this->button_css_class ) ) echo ' ' . $this->button_css_class ?>" <?php if( ! empty( $this->button_css_id ) ) echo 'id="' . $this->button_css_id . '" ' ?>name="wf_submit" type="submit" value="<?php echo $this->button_label; ?>" /></span>
					<?php if( $this->section_count > 0 ) : ?><span class="wsform-next-span"><button class="wsform-button wsform-next-section">&gt;</button></span><?php endif ?>
				</div>

        </form>

		</div>

		<!-- for testing purposes -->
		<div id="wsform-forms-test-return">

		</div>
		<!-- end test div -->

		<?php

	}



	function progress_bar()
	{
		/* Check to see if sections are being used
		 *
		 * If they are, count them all to set up progress bar
		 *
		 * Return total number of sections
		 *
		 */

		$sec_ct = 0;

		$els = $this->form_elements;

		if( ! is_array( $els ) ) return;

		foreach( $els as $el_check )

			if( $el_check->wf_type == 'section' ) $sec_ct++;


		$this->section_count = $sec_ct;


		// If section count is greater than zero, add progress bar

		if( $sec_ct > 0 ) :

		$section_width = round( ( 1 / $sec_ct * 100 ), 2 );

		?>

		<div class="wsform-progress-bar">

			<div class="wsform-progress-bar-con">

				<?php for( $s = 1 ; $s <= $sec_ct ; $s++ ) : ?>
				<span class="wsform-progress-bar-segment<?php if( $s == 1 ) echo ' active' ?>" style="width:<?php echo $section_width ?>%"><span class="wsform-progress-bar-tgt" rel="<?php echo $s ?>"></span></span>
				<?php endfor ?>

			</div>

		</div>

		<?php

		endif;

	}




	static function form_input_types()
	{
		return array(
			"checkbox" 	=> "Checkboxes",
			"date" 		=> "Date Field",
			"select" 	=> "Dropdown Menu",
			"email" 	=> "Email Address",
			"file" 		=> "File Upload",
			"header" 	=> "Header",
			"hidden" 	=> "Hidden Input",
			"number" 	=> "Number Field",
			"password" 	=> "Password Field",
			"radio" 	=> "Radio Buttons",
			//"section"	=> "Section",
			"states"	=> "State Picker",
			"tel" 		=> "Telephone Number",
			"text" 		=> "Text Field",
			"textarea" 	=> "Textarea",
			"time" 		=> "Time Field",
			"url" 		=> "Web Address",
		);

	}





	static function form_parameters()
	{
		return [
			'button_css_class'				=> '_wsform_button_css_class',
			'button_css_id' 				=> '_wsform_button_css_id',
			'button_label' 					=> '_wsform_button_label',
			'button_align' 					=> '_wsform_button_align',
			'css_class' 					=> '_wsform_css_class',
			'css_id' 						=> '_wsform_css_id',
			'form_align' 					=> '_wsform_form_align',
			'form_css_class' 				=> '_wsform_form_css_class',
			'form_css_id' 					=> '_wsform_form_css_id',
			'form_custom_slug' 				=> '_wsform_form_custom_slug',
			'form_elements' 				=> '_wsform_form_elements',
			'form_id' 						=> '_wsform_form_id',
			'form_keys' 					=> '_wsform_form_keys',
			'form_recipient' 				=> '_wsform_form_recipient',
			'form_send_receipt' 			=> '_wsform_form_send_receipt',
			'form_send_reply' 				=> '_wsform_form_send_reply',
			'form_sender' 					=> '_wsform_form_sender',
			'form_sender_name' 				=> '_wsform_form_sender_name',
			'form_skin' 					=> '_wsform_form_skin',
			'form_success_message'			=> '_wsform_form_success_message',
			'form_error_message'			=> '_wsform_form_error_message',
			'form_success_url'				=> '_wsform_form_success_url',
			'form_type' 					=> '_wsform_form_type',
			'form_width' 					=> '_wsform_form_width',
			'form_width_unit' 				=> '_wsform_form_width_unit',
			'hide_name_email' 				=> '_wsform_hide_name_email',
			'output_slug' 					=> '_wsform_output_slug',
			'raw_elements'					=> '_wsform_raw_elements',
			'save_email_responses' 			=> '_wsform_save_email_responses',
            'display_visibility'            => '_wsform_display_visibility',
            'vis_placeholder_text'          => '_wsform_visibility_placeholder_text',
            'visibility_table'              => '_wsform_display_vis_table',
        ];
	}




	function parse_elements()
	{
		$els = array( json_decode( $this->raw_elements ) );

		$els = $els[0];

		if( empty( $els ) ) return;

		// Check to see if options are included
		foreach( $els as &$val )
		{
			if( property_exists( $val, 'wf_options') )
			{
				$temp_options = explode( PHP_EOL, $val->wf_options );

				foreach( $temp_options as &$v )
				{
					if( intval( strlen( $v ) ) > 0 )
					{
						$a = explode( ' : ', $v );
						$val->wf_options_array[] = $a;
					}
				}

			}

			//// add "keys" key to map wf_id to wf_label
			//$val->keys = array();
		}

		$this->form_elements = $els;
	}













	/*
	 * ADMIN METHODS
	 *
	 */



	function meta_form_attributes()
	{
		$the_id = $this->post_id;
		$tab = ! empty( $_GET['tab'] ) ? $_GET['tab'] : null;

        $newmsg = ( strpos( $_SERVER['REQUEST_URI'], 'post-new.php' ) > 0 ) ? true : false;

		?>

		<!-- Form development tabs tabs -->
        <nav class="nav-tab-wrapper">
            <?php if( $newmsg == false ) : ?>
            <a href="?post=<?php echo $the_id ?>&action=edit&tab=wsform-responses" rel="wsform-responses" class="wsform-tab nav-tab<?php if( $tab == 'wsform-responses' || $tab === null ) echo ' nav-tab-active' ?>">Responses</a>
            <a href="?post=<?php echo $the_id ?>&action=edit&tab=wsform-type" rel="wsform-type" class="wsform-tab nav-tab<?php if( $tab == 'wsform-type' ) echo ' nav-tab-active' ?>">Form Type</a>
            <?php else : ?>
            <a href="?post=<?php echo $the_id ?>&action=edit&tab=wsform-type" rel="wsform-type" class="wsform-tab nav-tab<?php if( $tab == 'wsform-type' || $tab === null ) echo ' nav-tab-active' ?>">Form Type</a>
            <?php endif; ?>
            <a href="?post=<?php echo $the_id ?>&action=edit&tab=wsform-elements" rel="wsform-elements" class="wsform-tab nav-tab<?php if( $tab == 'wsform-elements' ) echo ' nav-tab-active' ?>">Elements</a>
            <a href="?post=<?php echo $the_id ?>&action=edit&tab=wsform-styles" rel="wsform-styles" class="wsform-tab nav-tab<?php if( $tab == 'wsform-styles' ) echo ' nav-tab-active' ?>">Styles</a>
            <a href="?post=<?php echo $the_id ?>&action=edit&tab=wsform-visibility" rel="wsform-visibility" class="wsform-tab nav-tab<?php if( $tab == 'wsform-visibility' ) echo ' nav-tab-active' ?>">Visibility</a>
            <a href="?post=<?php echo $the_id ?>&action=edit&tab=wsform-ouput" rel="wsform-output" class="wsform-tab nav-tab<?php if( $tab == 'wsform-output' ) echo ' nav-tab-active' ?>">Output</a>
        </nav>

        <?php if( $newmsg == false ) : ?>
        <div class="wsform-section wsform-responses<?php if( $tab == 'wsform-responses' || $tab === null ) echo ' wg-active' ?>">
			<?php $this->meta_form_responses() ?>
		</div>

		<div class="wsform-section wsform-type<?php if( $tab == 'wsform-type' ) echo ' wg-active' ?>">
			<?php $this->meta_form_type() ?>
		</div>

        <?php else : ?>

		<div class="wsform-section wsform-type<?php if( $tab == 'wsform-type' || $tab === null ) echo ' wg-active' ?>">
			<?php $this->meta_form_type() ?>
		</div>

        <?php endif; ?>

		<div class="wsform-section wsform-elements<?php if( $tab == 'wsform-elements' ) echo ' wg-active' ?>">
			<?php $this->meta_form_custom() ?>
			<?php $this->meta_form_builder() ?>
		</div>

		<div class="wsform-section wsform-styles<?php if( $tab == 'wsform-styles' ) echo ' wg-active' ?>">
			<?php $this->meta_form_styles() ?>
		</div>

		<div class="wsform-section wsform-visibility<?php if( $tab == 'wsform-visibility' ) echo ' wg-active' ?>">
			<?php $this->meta_form_visibility() ?>
		</div>

		<div class="wsform-section wsform-output<?php if( $tab == 'wsform-output' ) echo ' wg-active' ?>">
			<?php $this->meta_form_output() ?>
		</div>

		<?php
	}



    // function get_the_sub( $atts )
    // {
    //     $sub_id = intval( $atts['msg_id'] );
    //     $sub = new WebbsitesFormSub();
    //     $sub->webbsitesform_sub( $sub_id );
    //     $sub->form_sub_display();
    // }



    function form_responses( $posts )
    {
        foreach( $posts as $post )
        {
            $sub = new WebbsitesFormSub();
            $sub->webbsitesform_sub( $post->ID );
            $sub->form_sub_display();
        }
    }





    function form_responses_refresh( $atts = [] )
    {
        // // TESTING
        // $this->error = true;
        // $this->error_msg = print_r( $atts, 1 );
        // $this->output();
        // die();


        // // Throw an error if $atts is empty
        // if( empty( $atts ) )
        // {
        //     $this->error = true;
        //     $this->error_msg = 'Server didn\'t get any data';
        //     $this->output();
        //     die();
        // }

        // // If no form_id, throw error
        // if( array_key_exists( 'i', $atts ) && ! empty( $atts['i'] ) ) 
        // {
        //     $form_id = intval( $atts['i'] );
        // }
        // else
        // {
        //     $this->error = true;
        //     $this->error_msg = 'No Form ID given';
        //     $this->output();
        //     die();
        // }

        // Determine trash or no
        $post_status = array_key_exists( 's', $atts ) && $atts['s'] == 'trash' ? 'trash' : 'publish';

        // Start the output buffer
        ob_start();

        // Execute the function
        $this->form_responses_list( $post_status );

        // Send the output
        $this->output( ob_get_clean() );
    }





    function form_responses_download_dialog( $atts )
    {
        // Throw an error if $atts is empty
        if( empty( $atts ) )
        {
            $this->send_error( 'Server didn\'t get any data' );
        }

        $start_time = date( 'Y-m-d\T00:00' );
        $end_time = date( 'Y-m-d\T23:59' );

        ob_start();

        ?>
                
                <h1>Download Responses</h1>

                    <form action="" method="get" id="ws-forms-download-responses">

                        <div class="ws-forms-popup-dialog">

                            <fieldset>

                                <legend>1. Time frame of responses</legend>

                                <div>
                                    <label for="responses-today">
                                        <input type="radio" id="responses-today" name="responses_time_frame" value="responses_today" checked />
                                        Today&rsquo;s responses
                                    </label>

                                    <label for="responses-all">
                                        <input type="radio" id="responses-all" name="responses_time_frame" value="responses_all" />
                                        All responses
                                    </label>

                                    <label for="responses-range">
                                        <input type="radio" id="responses-range" name="responses_time_frame" class="wsf-reveal-hide" rel="ws-forms-date-time-ranges" value="responses_range" />
                                        Date/time range
                                    </label>

                                    <div id="ws-forms-date-time-ranges" class="ws-forms-deac-items ws-forms-dialog-smaller-list">
                                        <div>
                                            <label for="date-time-range-start">
                                                Start
                                                <input type="datetime-local" id="date-time-range-start" name="responses_date_time_begin" value="<?php echo $start_time ?>" disabled />
                                            </label>
                                        </div>
                                        <div>
                                            <label for="date-time-range-end">
                                                End
                                                <input type="datetime-local" id="date-time-range-end" name="responses_date_time_end" value="<?php echo $end_time ?>" disabled />
                                            </label>
                                        </div>
                                    </div>

                                </div>
                                
                            </fieldset>

                            <?php if( $this->form_type == 'build' ) : ?>
                            <fieldset>

                                <legend>
                                    2. Fields to display
                                </legend>

                                <div>
                                    <label for="fields-all">
                                        <input type="radio" id="fields-all" name="responses_fields" value="fields_all" checked />
                                        All fields
                                    </label>

                                    <label for="fields-select">
                                        <input type="radio" id="fields-select" name="responses_fields" class="wsf-reveal-hide" rel="ws-forms-select-fields" value="fields_select" />
                                        Select fields
                                    </label>

                                    <div id="ws-forms-select-fields" class="ws-forms-deac-items ws-forms-dialog-smaller-list">

                                        <label for="fields-sender-name">
                                            <input type="checkbox" id="fields-sender-name" name="fields_selected[]" value="sub_wf_sender_name" checked disabled />
                                            Sender name
                                        </label>

                                        <label for="fields-sender-email">
                                            <input type="checkbox" id="fields-sender-email" name="fields_selected[]" value="sub_wf_sender_email" checked disabled />
                                            Sender email
                                        </label>

                                        <?php foreach( $this->form_elements as $el ) : ?>
                                        <label for="fields_<?php echo $el->wf_id ?>">
                                            <input type="checkbox" id="fields_<?php echo $el->wf_id ?>" name="fields_selected[]" value="<?php echo $el->wf_id ?>" checked disabled />
                                            <?php echo $el->wf_label ?>
                                        </label>
                                        <?php endforeach ?>

                                        <label for="fields-sender-date-time">
                                            <input type="checkbox" id="fields-sender-date-time" name="fields_selected[]" value="sub_wf_date" checked disabled />
                                            Date/Time Submitted
                                        </label>

                                    </div>

                                </div>

                            </fieldset>

                            <?php endif ?>

                            <fieldset>

                                <legend>
                                    3. Download Format
                                </legend>

                                <div>
                                    <label for="format-xlsx">
                                        <input type="radio" id="format-xlsx" name="responses_format" value="format_xlsx" checked />
                                        Spreadsheet (.xlsx)
                                    </label>

                                    <label for="format-csv">
                                        <input type="radio" id="format-csv" name="responses_format" value="format_csv" />
                                        Comma-separated values (.csv)
                                    </label>

                                    <label for="format-pdf">
                                        <input type="radio" id="format-pdf" class="wsf-reveal-hide" rel="ws-forms-pdf-sort-choose" name="responses_format" value="format_pdf" />
                                        PDF
                                    </label>

                                    <div id="ws-forms-pdf-sort-choose" class="ws-forms-deac-items ws-forms-dialog-smaller-list">

                                        <label for="ws-forms-pdf-sort-select">
                                            Sort by:<br />

                                            <select id="ws-forms-pdf-sort-select" name="pdf_select_sort_field" style="margin:.5rem 0 0" disabled>
                                                <option value="pdf_sort_date" selected>Date/Time Submitted</option>
                                                <option value="pdf_sort_sub_wf_sender_name">Sender Name</option>
                                                <option value="pdf_sort_sub_wf_sender_email">Sender Email</option>
                                                <?php foreach( $this->form_elements as $elm ) : ?>
                                                <label for="<?php echo $elm->wf_id ?>">
                                                <option value="pdf_sort_<?php echo $elm->wf_id ?>"><?php echo $elm->wf_label ?></option>
                                                <?php endforeach ?>
                                            </select>

                                        </label>

                                    </div>

                                </div>

                            </fieldset>

                        </div>

                        <div class="ws-forms-popup-buttons">
                            <button class="ws-form-dialog-button download-this button button-primary">Download</button>
                            <button class="ws-form-dialog-button cancel-this button">Cancel</button>
                        </div>

                    </form>

                </div>

        <?php 
        
        // Empty the buffer and output the contents
        $this->output( ob_get_clean() );

    }



    function empty_trash()
    {
        // Get a list of trashed posts
        $this->form_submissions( [ 'post_status' => 'trash'] );

        // Create an array for reporting
        $rpt = [];
        $rpt['errors'] = [];
        $rpt['deleted'] = [];

        // Send each to trash
        foreach( $this->form_subs as $sub )
        {
            $r = wp_delete_post( $sub->ID );
            if( $r == false OR $r == null ) $rpt['errors'][] = $sub->ID;
        }

        // If there was a problem, send an error; otherwise, send ok
        if( count( $rpt['errors'] ) > 0 )
        {
            $this->send_error( $rpt );
        }
        else
        {
            ob_start();
            $this->form_responses_list();
            $this->output( ob_get_clean() );
        }
    }




    function download_subs( $atts )
    {
        // $form_id = $this->form_id;
        // $form_id = intval( $atts['form_id'] );

        // Set the time frame format
        $reg = '/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/';

        if( $atts['responses_time_frame'] == 'responses_range' )
        {
            $responses_range = 'range';
            
            // if time values fail validation, set to "all"
            if( preg_match( $reg, $atts['responses_date_time_begin'] ) )
            {
                $time_begin = $atts['responses_date_time_begin'];

                if( preg_match( $reg, $atts['responses_date_time_end'] ) )
                {
                    $time_end = $atts['responses_date_time_end'];
                }
                else
                {
                    $responses_range = 'all';
                }

            }
            else
            {
                $responses_range = 'all';
            }
        }
        elseif( $atts['responses_time_frame'] == 'responses_today' )
        {
            $responses_range = 'range';
            $time_begin = date( 'Y-m-d\T00:00' );
            $time_end = date( 'Y-m-d\T23:59' );
        }
        else // all entries
        {
            $responses_range = 'all';
            $time_begin = null;
            $time_end = null;
        }


        // Set the fields to be downloaded
        if( $atts['responses_fields'] == 'fields_select' )
        {
            $selected_fields = $atts['fields_selected'];
        }
        else
        {
            $selected_fields = 'all';
        }

        // Set the responses format
        $responses_format = 
            in_array( $atts['responses_format'], ['format_xlsx','format_csv','format_pdf'] )
            ? $atts['responses_format']
            : 'format_xlsx';

        // Set the sort
        if( $atts['responses_format'] == 'format_pdf' )
        {
            $data_sort = esc_html( $atts['pdf_select_sort_field'] );
        }
        else
        {
            $data_sort = 'sub_wf_date';
        }

        // Get the form info
        // $form = new WebbsitesForm( $form_id );
        // $this->wsform(  );

        // Get the subs
        $sub_atts = [
            'responses_range' => $responses_range,
            'time_begin' => $time_begin,
            'time_end' => $time_end,
            'selected_fields' => $selected_fields,
            'data_sort' => $data_sort,
            'responses_format' => $responses_format
        ];


        // Get the subs
        $this->form_submissions( $sub_atts );

        // print_array( $sub_atts );
        // print_array( $this );
        // return;


        // Set the columns
        $form_els = $this->form_elements;

        $cols = [
            'sub_wf_sender_name' => [],
            'sub_wf_sender_email' => [],
        ];

        foreach( $form_els as $el )
        {
            $cols[ $el->wf_id ] = [];
        }

        $cols[ 'sub_wf_date' ] = [];

        foreach( $this->form_subs as $post )
        {
            $sub = new WebbsitesFormSub( $post->ID );

            $cols['sub_wf_sender_name'][] = $sub->sub_wf_sender_name;
            $cols['sub_wf_sender_email'][] = '<a href="mailto:' . $sub->sub_wf_sender_email . '">' . $sub->sub_wf_sender_email . '</a>';

            foreach( $sub->sub_wf_input as $key => $data )
            {
                $cols[$key][] = $data;
            }

            $cols['sub_wf_date'][] = $this::fancy_date( $sub->sub_wf_time );

        }

        // Remove data not selected
        if( $selected_fields != 'all' )
        {
            foreach( $cols as $k => $c )
            {
                if( ! in_array( $k, $selected_fields ) ) unset( $cols[$k] );
            }
        }

        // If it's a PDF, sort according to form
        if( $atts['responses_format'] == 'format_pdf' )
        {
            if( $atts['pdf_select_sort_field'] != 'pdf_sort_date' )
            {
                $skey = substr( $atts['pdf_select_sort_field'], 9 );
                $cols = $this::sort_this_array( $cols, $skey, 'ASC' );
            }
        }

        // Add labels
        foreach( $cols as $ckey => &$col )
        {
            if( $ckey == 'sub_wf_sender_name' ) array_unshift( $col, 'Sender Name' );
            elseif( $ckey == 'sub_wf_sender_email' ) array_unshift( $col, 'Sender Email' );
            elseif( $ckey == 'sub_wf_date' ) array_unshift( $col, 'Date / Time Submitted' );
            else array_unshift( $col, $this->form_keys[$ckey]['wf_label']);
        }

        // Disassociate
        $cols = array_values( $cols );


        // Invert the array
        $data = [];

        for( $cl = 0 ; $cl < count( $cols ) ; $cl++ )
        {
            for( $rw = 0 ; $rw < count( $cols[0] ) ; $rw++ )
            {
                $data[$rw][$cl] = $cols[$cl][$rw];
            }
        }

        // Output the file
        // Set document slug
        // Lowercase, remove spaces, special characters
        $the_title = strtolower( preg_replace( '/\s+/', '-', $this->post_title ) );
        $the_title = strtolower( preg_replace( '/[^A-Za-z0-9-]/', '', $the_title ) );

        if( $responses_format == 'format_xlsx' )
        {
            if( count( $data ) <= 1 )
            {
                $this->download_error_page( 'Sorry, but there was no data for the selected time period.' );
            }

            // Bold headers
            for( $n = 0 ; $n < count( $data[0] ) ; $n++ )
            {
                $data[0][$n] = '<b>' . $data[0][$n] . '</b>';
            }

            $doc = Shuchkin\SimpleXLSXGen::fromArray( $data );
            $doc->downloadAs( "$the_title.xlsx" );
        }
        elseif( $responses_format == 'format_csv' )
        {
            if( count( $data ) <= 1 )
            {
                $this->download_error_page( 'Sorry, but there was no data for the selected time period.' );
            }

            // Get the last array key
            $last_key = array_key_last( $data[0] );

            // Assemble the .csv file
            for( $d = 0 ; $d < count( $data[0] ) ; $d++ )
            {
                $csv[$d] = '"';

                foreach( $data[$d] as $kk => $cc )
                {
                    $csv[$d] .= $cc . '"';
                    if( $last_key != $kk ) $csv[$d] .= ',"';
                }
            }

            // Collapse the array
            $csv_str = implode( "\r\n", $csv );

            // Header to download .csv file
            header( 'Content-Disposition: attachment; filename="' . $the_title . '.csv";' );

            echo $csv_str;
        }
        else // pdf
        {
            // // Save the $args array
            // // Save the object
            // $args['obj'] = $this;
            // $args['data'] = $data;
            // $args['atts'] = $atts;

            // Start the output buffer
            ob_start();

            $this->generate_PDF_table( $data );

            // Save the buffer contents
            $html = ob_get_clean();

            // echo $html;
            // return;

            // instantiate and use the dompdf class
            $dompdf = new Dompdf();
            $dompdf->loadHtml( $html );

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('Legal', 'landscape');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->add_info( 'Title', $this->post_title );
            $dompdf->stream( "$the_title.pdf" );
        }
    }




    private function download_error_page( $html )
    {

        $tz = wp_timezone();
        $pdate = new DateTime();
        $pdate->setTimezone( $tz );
    
        $the_date = $pdate->format( 'l, n/j/y' );
        $the_time = $pdate->format( 'g:i a' );

        $datestamp = "Created $the_date at $the_time";

        ?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php _e( 'Download Error' ) ?></title>
    <style type="text/css">
        body {font-family:sans-serif;font-size:.8rem;position:relative}
        h1 {margin:0 0 .5rem;font-size:1.5rem;padding:0;font-weight:bold}
        h2 {margin:0 0 1rem;padding:0;font-size:.8rem;font-weight:normal}
        p {font-size:.8rem;margin:0;padding:0;line-height:1.5;}
        /* body > div > p {font-size:.6rem;margin:0;padding:0;line-height:1.5;;text-align:right;display:block;height:0;position:relative;top:-30px} */
        table {width:100%;border-collapse:collapse;font-size:.7rem}
        th {font-weight: bold; text-align:left;background:#000;color:#fff}
        th, td {padding:.5rem .2rem}
        tr:nth-child(odd) {background:#eee}
    </style>

</head>
<body>

<h1><?php _e( 'Download Error' ) ?></h1>

<h2><?php _e( $datestamp ) ?></h2>

<p><?php _e( $html ) ?></h2>

</body>
</html>
        
        

<?php
    die();
    }




    private function generate_PDF_table( $data )
    {
        // $data = $args['data'];
        // $atts = $args['atts'];
        // $obj = $args['obj'];

        $title = $this->post_title;
        $user = get_user_meta( get_current_user_id() );

        // print_array( $user );
        // return;

        if( ! empty( $user['first_name'][0] ) && ! empty( $user['last_name'][0] ) )
        {
            $username = $user['first_name'][0] . ' ' . $user['last_name'][0];
        }
        else
        {
            $username = $user['nickname'][0];
        }

        $tz = wp_timezone();
        $pdate = new DateTime();
        $pdate->setTimezone( $tz );
    
        $the_date = $pdate->format( 'l, n/j/y' );
        $the_time = $pdate->format( 'g:i a' );

        $datestamp = "Created by $username on $the_date at $the_time";

        ?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php _e( $title ) ?></title>
    <style type="text/css">
        body {font-family:sans-serif;font-size:.8rem;position:relative}
        h1 {margin:0 0 .5rem;font-size:1.5rem;padding:0;font-weight:bold}
        h2 {margin:0 0 1rem;padding:0;font-size:.8rem;font-weight:normal}
        p {font-size:.8rem;margin:0;padding:0;line-height:1.5;}
        body > p {font-size:.6rem;text-align:right;display:block;height:0;position:relative;top:-30px}
        /* body > div > p {font-size:.6rem;margin:0;padding:0;line-height:1.5;;text-align:right;display:block;height:0;position:relative;top:-30px} */
        table {width:100%;border-collapse:collapse;font-size:.7rem}
        th {font-weight: bold; text-align:left;background:#000;color:#fff}
        th, td {padding:.5rem .2rem}
        tr:nth-child(odd) {background:#eee}
    </style>

</head>
<body>

<h1><?php _e( $title ) ?></h1>

<h2><?php _e( $datestamp ) ?></h2>

<p>Copyright &copy; <?php echo date( 'Y' ) ?>, Webbsites &mdash; <a href="https://webbsites.net/">https://webbsites.net/</a></p>

<table>

<?php 

if( count( $data ) == 1 ) :

echo '<div><p><strong>No responses in selected time frame</strong></p></div>';

else:

$last_key = array_key_last( $data );

foreach( $data as $i => $row )
{
    // if( $i == 0 ) echo '<thead>';
    // if( $i == 1 ) echo '<tbody>';

    echo '<tr>';

    foreach( $row as $item )
    {
        if( $i == 0 ) echo '<th>' . $item . '</th>';
        else echo '<td>' . $item . '</th>';
    }

    echo '</tr>';

    // if( $i == 0 ) echo '</thead>';
    // if( $i == $last_key ) echo '</tbody>';
}

endif;

?></table>
</body>
</html>
        
        

<?php
    }





    static function sort_this_array( $cols, $skey, $sort = 'ASC' )
    {
        // Get the column to sort the rest of the columns
        // If the column doesn't exist, return original column untouched
        if( ! array_key_exists( $skey, $cols ) ) return $cols;

        // Isolate the key column
        $key_col = $cols[$skey];
        natsort( $key_col );

        // Save the sorting keys
        $sorting_keys = array_keys( $key_col );

        // Rearrange the original array
        $new_cols = [];
        foreach( $cols as $key => $col )
        {
            for( $c = 0 ; $c < count( $col ) ; $c++ )
            {
                $i = $sorting_keys[$c];
                $this_new_col[$c] = $col[$i];
            }

            // Reverse array if DESC called
            if( $sort != 'ASC' )
            {
                $this_new_col = array_reverse( $this_new_col );
            }

            // Insert new column into array
            $new_cols[$key] = $this_new_col;
        }

        return $new_cols;
    }





    function form_submissions( $a = [] )
    {
        // Set up the WP Query
        // Always gonna be 'wsform_sub'
        $q['post_type'] = 'wsform_sub';

        // Post ID is parent form id
        $q['post_parent'] = 
            array_key_exists( 'post_id', $a ) 
            ? intval( $a['post_id'] ) 
            : $this->post_id;

        $q['post_status'] = array_key_exists( 'post_status', $a ) && $a['post_status'] == 'trash' ? 'trash' : 'publish';
        $q['paged'] = array_key_exists( 'paged', $a ) ? intval( $a['paged'] ) : 0;
        $q['posts_per_page'] = array_key_exists( 'posts_per_page', $a ) ? $a['posts_per_page'] : -1;
        $q['ignore_sticky_posts'] = array_key_exists( 'ignore_sticky_posts', $a ) ? $a['ignore_sticky_posts'] : true;
        $q['order_by'] = 'date';
        $q['order'] = array_key_exists( 'order', $a ) ? $a['order'] : 'DESC';

        // Time range of posts
        // Set 'before' and 'after' date query if range not set to 'all'
        if( array_key_exists( 'responses_range', $a ) && $a['responses_range'] != 'all' )
        {
            // $now = time();

            // // Set current timestamp
            $tz = wp_timezone();
            $zdate = new DateTime();
            $zdate->setTimezone( $tz );

            // Attempt to get $after and $before times
            $after = array_key_exists( 'time_begin', $a ) && ! empty( $a['time_begin'] ) ? $a['time_begin'] : 0; // set to beginning of Unix time if array key isn't there
            $before = array_key_exists( 'time_end', $a ) && ! empty( $a['time_end'] ) ? $a['time_end'] : time(); // set to now if invalid

            // If time values are valid and in correct order, set the date query
            if( strtotime( $before ) > strtotime( $after ) )
            {
                $q['date_query']['after'] = $after;
                $q['date_query']['before'] = $before;
            }
        }

        // print_array( $q );

        $query = new WP_Query( $q );

        // return $wp->posts;
        $this->form_query_object = $query;
        $this->form_subs = $query->posts;

        // COUNT SUBMISSIONS
        // Count the total number of subs and save it in the object
        $this->nbr_subs = count( $this->form_subs );

        // Count the number of unread subs & subs in trash
        // Start the counters
        $nbr_unread = $nbr_in_trash = 0;

        // Get all the post metas
        foreach( $this->form_subs as $sub )
            $pm[$sub->ID] = get_post_meta( $sub->ID );

        // Count the number of unread subs
        if( $this->nbr_subs > 0 )
            foreach( $pm as $pmt )
                if( $pmt['_wsform_sub_is_read'][0] == 0 ) $nbr_unread++;

        // Save number of unread and trashed subs in the object
        $this->nbr_subs_unread = $nbr_unread;


        // Get the number of posts in the trash
        if( $q['post_status'] == 'publish' )
        {
            $f = new WebbsitesForm( $this->post_id );
            $f->form_submissions( ['post_status' => 'trash'] );
            $this->nbr_subs_in_trash = count( $f->form_subs );
        }
        else
        {
            $this->nbr_subs_in_trash = $this->nbr_subs;
        }
    }





    static function before_delete_post( $post_id, $post )
    {

    }







    static function admin_column_responses( $post_id )
    {
        $form = new WebbsitesForm( $post_id );
        $form->form_submissions();

        echo $form->nbr_subs . ' total, ' . $form->nbr_subs_unread . ' new';
    }



    function form_responses_list( $post_status = 'publish' )
    {
        // Get the form id; use the object if it's null
        // $post_id = $parent_id != null ? intval( $post_id ) : $this->post_id;

        // Get the form_submissions
        $this->form_submissions( [ 
            'post_status' => $post_status, 
            'post_id' => $this->post_id,
            ] );

        // print_array( $this );

        // Set the messages count text
        if( $post_status == 'publish' )
        {
            if( $this->nbr_subs == 1 ) $resp_ct_text = $this->nbr_subs . ' response';
            else $resp_ct_text = $this->nbr_subs . ' responses';
    
            if( $this->nbr_subs_unread > 0 ) $resp_ct_text .= ', ' . $this->nbr_subs_unread . ' new';
    
            if( $this->nbr_subs_in_trash > 0 ) $resp_ct_text .= ', ' . $this->nbr_subs_in_trash . ' in trash';
        }
        else
        {
            if( $this->nbr_subs == 1 ) $resp_ct_text = $this->nbr_subs . ' response in trash';
            else $resp_ct_text = $this->nbr_subs . ' responses in trash';
        }

        // Trash toggle label
        if( $post_status == 'publish' )
        {
            $tog_lbl = 'Show Trash';
            $tog_class = ' show-trash';
        }
        else
        {
            $tog_lbl = 'Hide Trash';
            $tog_class = ' hide-trash';
        }

        $tog_class .= ( $post_status == 'trash' OR $this->nbr_subs_in_trash > 0 ) ? '' : ' ws-forms-hide';
        $trash_vis = $this->nbr_subs_in_trash > 0 ? '' : ' ws-forms-hide';

        ?>

                <div class="wsform-message-list-controls">
                    <span id="trash-toggle" class="wsform-list-control<?php echo $tog_class ?>"><?php _e( $tog_lbl ) ?></span>
                    <span id="empty-trash" class="wsform-list-control<?php echo $trash_vis ?>"><?php _e( 'Empty trash' ) ?></span>
                </div>

                <div class="wsform-messages-count-area">

                    <p id="wsform-messages-count" class="wsform-note align-center">
                        <?php _e( $resp_ct_text ) ?>
                    </p>

                </div>

                <div id="wsform-messages-list-area" class="wsform-messages-list-area" data-post-status="<?php echo $post_status ?>">

                    <div class="wsform-messages-list-con">

                        <ul id="wsform-message-list-items" class="wsform-message-list-items">

                            <?php 
                            
                            foreach( $this->form_subs as $sb )
                            {
                                $sub = new WebbsitesFormSub( $sb->ID );

                                $sender_name = $sub->sub_wf_sender_name;
                                $sender_email = $sub->sub_wf_sender_email;
                                $send_date_time = $this::fancy_date( $sub->sub_wf_time );

                                $this_msg_class = 'wsform-message-list-item';

                                if( $sub->sub_wf_is_read != 1 ) 
                                    $this_msg_class .= ' message-unread';

                                ?>
                                    <li class="<?php echo $this_msg_class ?>" id="ws-form-message-list-item-<?php echo $sb->ID ?>" rel="<?php echo $sb->ID ?>">
                                        <p><strong><?php _e( $sender_name ) ?></strong><br>
                                        <span class="wsform-message-list-meta">
                                            <?php _e( $sender_email ) ?><br>
                                            <?php _e( $send_date_time ) ?></span></p>
                                    </li>
                                <?php 
                            }
                                                
                            ?>

                        </ul>

                    </div>

                </div>

<?php

        
    }






    function meta_form_responses()
    {
        ?>
			<div id="wsform-responses" class="wsform-meta-box-module wsform-appearance-modules active" data-form-id="<?php echo $this->post_id ?>">

                <form>
                    <div class="wsform-meta-box-module-buttons">
                        <span class="wsform-popup refresh-responses page-title-action" rel="refresh">Refresh</span>
                        <span class="wsform-popup download-responses page-title-action" rel="download">Download</span>
                    </div>
                </form>

                <h1 class="wsform-module-header">Form Responses</h1>

                <div class="wsform-cpanel-area">
                    
                    <div class="wsform-messages-container">

                        <!-- Main message display area -->
                        <div class="wsform-messages-area">

                            <!-- Left rail list of messages -->
                            <div id="wsform-messages-list" class="wsform-messages-list">

                                <?php $this->form_responses_list() ?>

                            </div>

                            <!-- Message body area -->
                            <div id="wsform-message-display-area" class="wsform-message-display-area">

                                <div id="ws-form-empty-viewer-header" class="wsform-messages-notify active-message">
                                    <h3>No message selected</h3>
                                </div>

                                <div id="ws-form-active-message-con" class="ws-form-active-message-con"></div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
<?php
    }



	function meta_form_type()
	{
		// CUSTOM VS BUILT FORM
		// output the HTML

		?>
		<div id="wsform-type" class="wsform-meta-box-module wsform-content-modules wsform-type active">

			<h1 class="wsform-module-header">Form Type</h1>

			<p>Choose if this is a custom form or if you&rsquo;d like to build a form.</p>

			<label class="wsform-label-el inline" for="wsform-chooseformtype-build">
				<input type="radio" class="wsform-input wsform-form-type-choose" id="wsform-chooseformtype-build" rel="build" name="_wsform_form_type" value="build"<?php if( $this->form_type == 'build' ) echo ' checked' ?> />
				<span class="wsform-forms-radio-label">Build</span>
			</label>

			<label class="wsform-label-el inline" for="wsform-chooseformtype-custom">
				<input type="radio" class="wsform-input wsform-form-type-choose" id="wsform-chooseformtype-custom" rel="custom" name="_wsform_form_type" value="custom"<?php if( $this->form_type == 'custom' ) echo ' checked' ?> />
				<span class="wsform-forms-radio-label">Custom</span>
			</label>

			<?php // Add a hidden input with the form ID ?>
			<input type="hidden" id="wsform-form-id" name="_wsform_form_id" value='<?php echo $this->form_id ?>' />

		</div>
		<?php

	}





	function meta_form_custom()
	{
		// get the saved form custom slug
		//$path = $this->path_to_custom_forms;
		//$name = '_wsform_form_custom_slug';
		//$slug = get_post_meta( get_the_ID(), $name, 1 );
		//$slug = $this->output_slug;

		// output the HTML
		?>

		<div id="wsform-custom" class="wsform-meta-box-module wsform-content-modules<?php if( $this->form_type == 'custom' ) echo ' active' ?>">

			<h1 class="wsform-module-header">Form Template</h1>

			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<p class="wsform-note">Select the template you created for this custom form:</p>
					</th>
					<td>
						<?php $this::custom_template_select( $this->path_to_custom_forms, $this->form_custom_slug, '_wsform_form_custom_slug' ) ?>
					</td>
				</tr>
			</table>

		</div>

		<?php

	}





	// gets/sets form id
	private function the_form_id()
	{
		// Outputs the current form's ID. If a new form is being created, creates a form ID with the next available count.

		// Gut check: see if form_id exists in the object
		if( $this->form_id > 0 ) return;

		// Now see if it's in the post_meta
		$the_form_id = intval( get_post_meta( $this->post_id, '_wsform_form_id', 1 ) );

		// If it's there, great; set it and forget it
		if( $the_form_id > 0 ) : $this->form_id = $the_form_id;

		// If it's not there, then we got work to do
		else :

		// Use a SQL query to find all the form IDs out there
		// Set up the query
		global $wpdb;

		$res = $wpdb->get_results( "SELECT meta_value FROM $wpdb->postmeta where meta_key = '_wsform_form_id'", ARRAY_A );

		// If form IDs have been set, find the highest value and set one higher
		if( ! empty( $res ) )
		{
			// Find the max value and set it as an integer
			$max = max( $res );
			$highest_value = intval( $max['meta_value'] );

			// Set the new value one higher
			$new_form_id = intval( $highest_value + 1 );

			// Set the new form ID
			$this->form_id = $new_form_id;
		}

		// If form IDs haven't been set yet, make the new form_id 1
		else
		{
			$new_form_id = $this->form_id = 1;
		}

		endif;
	}



	function meta_form_builder()
	{
		// FORM ELEMENTS
		// get the input types for the dropdown menu
		$input_types = $this::form_input_types();

		?>


		<div id="wsform-builder" class="wsform-meta-box-module wsform-content-module<?php if( $this->form_type == 'build' ) echo ' active' ?>">

			<h1 class="wsform-module-header">Required Elements</h1>

			<div class="wsform-checkboxes-div">

				<table class="form-table">

					<tr valign="top">
						<th scope="row">
							<label class="wsform-label inline" for="wsform-output-chooser-hide">
								<span class="wsform-forms-radio-label">Hide Required Fields</span>
							</label>
						</th>
						<td>
							<label class="wsform-label inline" for="wsform-output-chooser-hide">
								<input type="checkbox" class="wsform-checkbox wsform-output-choose" id="wsform-output-chooser-hide" name="_wsform_hide_name_email" value="<?php echo $this->hide_name_email ?>"<?php if( $this->hide_name_email == 'checked' ) echo ' checked' ?> />
								Click here if you don&rsquo;t want to use the supplied &lsquo;Your Name&rsquo; &amp &lsquo;Your Email&rsquo; fields.
							</label>
							<div id="wsform-name-email-required-note" class="wsform-important-note<?php if( $this->hide_name_email != 'checked' ) echo ' hidden' ?>">
								<p class="wsform-note">Important: Do NOT check this box unless you have a <strong>really good</strong> reason to do so! If you do it, you will <strong>need</strong> to create the following fields:</p>
								<ul>
									<li>&mdash; A <strong>required</strong> text field with the ID <strong>wf_sender_name</strong>.</li>
									<li>&mdash; A <strong>required</strong> email field with the ID <strong>wf_sender_email</strong>.</li>
								</ul>
							</div>
						</td>
					</tr>

				</table>

			</div>

			<h1 class="wsform-module-header">Optional Elements</h1>

			<div class="wsform-meta-box-module-content">

				<p class="wsform-note">Add form elements here. Note: &ldquo;Your Name&rdquo; and &ldquo;Your Email&rdquo; fields are automatically added if &ldquo;Send Response&rdquo; is checked below.</p>

				<div class="wsform-select-sortable-list-con">

					<div class="wsform-element-list-con">

						<div data-wsform-form-id="<?php echo $this->form_id ?>" class="wsform-element-list">

							<?php foreach( $input_types as $key => $val ) : ?>
							<button class="wsform-element-add" data-element-type="<?php echo $key ?>"><?php echo $val ?></button>
							<?php endforeach ?>

						</div>

					</div>

					<div class="wsform-sortable-group wsform-select-sortable-list" data-wsform-form-id="<?php echo $this->form_id ?>">
						<ul id="wsform-elements" class="wsform-sortable-list">
							<?php $this->render_form_elements() ?>
						</ul>
						<input type="hidden" id="wsform-sg-input" name="_wsform_form_elements" value='<?php echo $this->elements_base64 ?>' />

					</div>

					<select data-wsform-form-id="<?php echo $this->form_id ?>" class="wsform-add-form-element">

						<option value="add_element">Add Element ...</option>
						<?php foreach( $input_types as $key => $val ) : ?>
						<option value="<?php echo $key ?>"><?php echo $val ?></option>
						<?php endforeach; ?>

					</select>

				</div>

			</div>

		</div>

		<?php

	}


    function check_visibility()
    {
        // First, check for the easy ones
        if( $this->form_display_visibility == 'always' )
        {
            $this->form_visibility = true;
        }
        elseif( $this->form_display_visibility == 'never' )
        {
            $this->form_visibility = false;
        }
        else
        {
            // Here comes the hard part :O
            // Get the values from the visibility table
            $days = (array) json_decode( base64_decode( $this->form_display_vis_table ) );

            // Next, instantiate a DateTime() object using the current date and 
            // the DateTimeZone object from Wordpress
            $tz = wp_timezone();
            $cdate = new DateTime();
            $cdate->setTimezone( $tz );

            // Get the current day in English (i.e. "Tuesday")
            $current_day = strtolower( $cdate->format( 'l' ) );
    
            // Now, check to see if the date is active.
            if( $days["_ws_day_active_$current_day"] === true )
            {
                // Now, check to see if "All Day" is checked
                if( $days["_ws_all_day_$current_day"] === true )
                {
                    $matches_now = true;
                }
                else
                {
                    // We need to determine if the current time is between the From and To times set for the day
                    // Get the current time & formatted date
                    $t_now = $cdate->getTimestamp();
                    $d_now = $cdate->format( 'D, j M Y' );

                    // Get the time string for From and To times
                    $time_from = $days["_ws_active_time_from_$current_day"];
                    $time_to = $days["_ws_active_time_to_$current_day"];

                    // Get the timezone offset from Wordpress
                    $tz_offset = get_option( 'gmt_offset' );

                    // Get the From and To timestamps for today
                    $cdate->setTimestamp( strtotime( $d_now . ' ' . $time_from . ' ' . $tz_offset ) );
                    $t_from = $cdate->getTimestamp();

                    $cdate->setTimestamp( strtotime( $d_now . ' ' . $time_to . ' ' . $tz_offset ) );
                    $t_to = $cdate->getTimestamp();

                    // If the current time is between the from and to times, matches_now is true
                    if( $t_now >= $t_from && $t_now <= $t_to )
                    {
                        $matches_now = true;
                    }
                    // If not...
                    else
                    {
                        $matches_now = false;
                    }

                }
            }
            // If the day is not active:
            else
            {
                $matches_now = false;
            }

            // Now, set the property to show or hide the form based on the $matches_now variable
            // and the $form_display_visibility property. A value of 'on-these-days' will mirror the
            // true/false boolean; a value of 'not-on-these-days' reverses it
            if( $this->form_display_visibility == 'on-these-days' )
            {
                $this->form_visibility = $matches_now;
            }
            else
            {
                $this->form_visibility = $matches_now === true ? false : true;
            }
        }
    }




    // function simplify_vis_table()
    // {
    //     // Make the visibility index a bit easier to read
    //     $weekdays = $this->weekdays;
    //     $table = (array) json_decode( base64_decode( $this->form_display_vis_table ) );
    //     $days = [];

    //     foreach( $weekdays as $day )
    //     {
    //         $d = strtolower( $day );
    //         $days[$day]['ws_day_active'] = $table["_ws_day_active_$d"];
    //         $days[$day]['ws_all_day'] = $table["_ws_all_day_$d"];
    //         $days[$day]['ws_active_time_from'] = $table["_ws_active_time_from_$d"];
    //         $days[$day]['ws_active_time_to'] = $table["_ws_active_time_to_$d"];
    //     }

    //     $this->simplified_vis_table = $days;
    // }



    function meta_form_visibility()
    {
        ?>
			<div id="wsform-visibility" class="wsform-meta-box-module wsform-appearance-modules active">

                <h1 class="wsform-module-header">Form Visibility</h1>

				<table class="form-table">

					<tr valign="top">
						<th scope="row">
							<span class="form-label">Behavior</span>
							<p class="wsform-note">Choose when you&rsquo;d like your form to appear.</p>
						</th>
						<td class="td-align-top">
                            <select class="wsform-select" id="wsform-display-visibility" name="_wsform_display_visibility">
                                <option value="always"<?php if( $this->form_display_visibility == 'always' ) echo ' selected' ?>>Always show this form</option>
                                <option value="on-these-days"<?php if( $this->form_display_visibility == 'on-these-days' ) echo ' selected' ?>>ONLY during the days/times below</option>
                                <option value="not-on-these-days"<?php if( $this->form_display_visibility == 'not-on-these-days' ) echo ' selected' ?>>NOT during the days/times below</option>
                                <option value="never"<?php if( $this->form_display_visibility == 'never' ) echo ' selected' ?>>Hide (never show this form)</option>
							</select>

                            <?php $this->visibility_table( $this->form_display_vis_table ) ?>

						</td>

					</tr>

					<tr valign="top">
						<th scope="row">
							<span class="form-label">Placeholder Text</span>
							<p class="wsform-note">Enter the HTML you&rsquo;d like the system to display when the form is hidden.</p>
						</th>
						<td>
                            <?php wp_editor( 
                                $this->form_placeholder_text, 
                                'wsform-visibility-placeholder-text', 
                                [
                                    'media_buttons' => false,
                                    'textarea_name' => '_wsform_visibility_placeholder_text',
                                    'textarea_rows' => 5,
                                ] 
                            ) ?>
                        </td>
                    </tr>
                </table>

            </div>



<?php
    }




    function vis_table_first_data()
    {
        $array = [
            '_ws_day_active_sunday' => true,
            '_ws_all_day_sunday' => true,
            '_ws_active_time_from_sunday' => '00:00',
            '_ws_active_time_to_sunday' => '23:59',
            
            '_ws_day_active_monday' => true,
            '_ws_all_day_monday' => true,
            '_ws_active_time_from_monday' => '00:00',
            '_ws_active_time_to_monday' => '23:59',
            
            '_ws_day_active_tuesday' => true,
            '_ws_all_day_tuesday' => true,
            '_ws_active_time_from_tuesday' => '00:00',
            '_ws_active_time_to_tuesday' => '23:59',
            
            '_ws_day_active_wednesday' => true,
            '_ws_all_day_wednesday' => true,
            '_ws_active_time_from_wednesday' => '00:00',
            '_ws_active_time_to_wednesday' => '23:59',
            
            '_ws_day_active_thursday' => true,
            '_ws_all_day_thursday' => true,
            '_ws_active_time_from_thursday' => '00:00',
            '_ws_active_time_to_thursday' => '23:59',
            
            '_ws_day_active_friday' => true,
            '_ws_all_day_friday' => true,
            '_ws_active_time_from_friday' => '00:00',
            '_ws_active_time_to_friday' => '23:59',
            
            '_ws_day_active_saturday' => true,
            '_ws_all_day_saturday' => true,
            '_ws_active_time_from_saturday' => '00:00',
            '_ws_active_time_to_saturday' => '23:59',
            
        ];

        return base64_encode( json_encode( $array ) );
    }




    function visibility_table( $enc_data )
    {
        // print_array( $this );

        if( empty( $enc_data ) ) $enc_data = $this->vis_table_first_data();

        $input = (array) json_decode( base64_decode( $enc_data ) );

        $weekdays = $this->weekdays;

        $vis_tbl_classes = 'wsform-display-visibility-table';

        if( $this->form_display_visibility == 'always' || $this->form_display_visibility == 'hide' )
            $vis_tbl_classes .= ' wsform-hide';

        // Start the grid
        $html  = '<div id="wsform-display-visibility-table" class="' . $vis_tbl_classes . '">' . "\r\n";

        // Hidden input to capture the values in the database
        $html .= '<input type="hidden" id="wsform_display_vis_table" name="_wsform_display_vis_table" value="' . $enc_data . '" />' . "\r\n";
        
        foreach( $weekdays as $day )
        {
            $sday = strtolower( $day );

            // The first column: The Day
            $html .= '<div class="wsform-disp-table-col-1 the-day">'
                  .  '<label class="ws-form-label" for="day-active-' . $sday . '">'
                  .  '<input class="wsform-vis-table-input active-day" rel="' . $sday . '"'
                  .  'type="checkbox" id="day-active-' . $sday 
                  .  '" data-field-name="_ws_day_active_' . $sday . '"';

            if( $input["_ws_day_active_$sday"] == 'checked' ) $html .= ' checked';

            $html .= ' /> <strong>' . $day . '</strong></label></div>'
                  .  "\r\n";

            // The second column: All Day
            $html .= '<div class="wsform-disp-table-col-2 all-day">'
                  .  '<span class="wsform-cell-content all-day-' . $sday;

            if( $input["_ws_day_active_$sday"] != 'checked' ) {$html .= ' wsform-hide';}

            $html .= '"><label class="ws-form-label" for="all-day-' . $sday . '">All Day '
                  .  '<input class="wsform-vis-table-input all-day" type="checkbox" rel="' . $sday . '" id="all-day-' . $sday 
                  .  '" data-field-name="_ws_all_day_' . $sday . '" value="' . $input["_ws_all_day_$sday"] . '"';

                  if( $input["_ws_all_day_$sday"] == true ) $html .= ' checked';
                  
            $html .= ' />'
                  .  '</label></span></div>'
                  .  "\r\n";

            // The third column: From time
            $html .= '<div class="wsform-disp-table-col-3 from-time">'
                  .  '<span class="wsform-cell-content from-time-' . $sday;

                  if( $input["_ws_all_day_$sday"] == 'checked' ) {$html .= ' wsform-hide';}
                  if( $input["_ws_day_active_$sday"] != 'checked' ) {$html .= ' wsform-hide';}
      
            $html .= '"><span id="wsform-from-' . $sday . '" class="wsform-label">From '
                  .  '<input class="wsform-vis-table-input active-time-from" type="time" rel="' . $sday . '" '
                  .  'id="time-active-' . $sday . '-from" data-field-name="_ws_active_time_from_' . $sday 
                  .  '" value="' . $input['_ws_active_time_from_' . $sday] . '" /></span></div>'
                  .  "\r\n";

            // The fourth column: To time
            $html .= '<div class="wsform-disp-table-col-4 to-time" rel="' . $sday . '">'
                  .  '<span class="wsform-cell-content to-time-' . $sday;

                  if( $input["_ws_all_day_$sday"] == 'checked' ) {$html .= ' wsform-hide';}
                  if( $input["_ws_day_active_$sday"] != 'checked' ) {$html .= ' wsform-hide';}
      
            $html .= '"><span id="wsform-to-' . $sday . '" class="wsform-label">To '
                  .  '<input class="wsform-vis-table-input active-time-to" type="time" rel="' . $sday . '" '
                  .  'id="time-active-' . $sday . '-to" data-field-name="_ws_active_time_to_' . $sday 
                  .  '" value="' . $input['_ws_active_time_to_' . $sday] . '" /></span></div>'
                  .  "\r\n";
        }

        $html .= '<div class="wsform-disp-table-footer"><span class="page-title-action" id="wsform-check-all-days">Check/uncheck all</span></div>';
        $html .= '</div>';

        echo $html;

    }



	function meta_form_styles()
	{
    //$the_id 			= $this->post_id;

		$form_type 			= $this->form_type == 'custom' ? 'custom' : 'build';
		//$form_skin 			= $this->form_skin;
		//$form_width 		= $this->form_width;
		//$form_width_unit 	= $this->form_width_unit;
		//$form_align 		= $this->form_align;
		//$css_id 			= $this->css_id;
		//$css_class 			= $this->css_class;
		//$form_css_id 		= $this->form_css_id;
		//$form_css_class 	= $this->form_css_class;
		//$button_label 		= $this->button_label;
		//$button_css_id 		= $this->button_css_id;
		//$button_css_class	= $this->button_css_class;

		// Output the HTML
		?>

			<div id="wsform-styles" class="wsform-meta-box-module wsform-appearance-modules<?php if( $form_type == 'build' ) echo ' active' ?>">

				<h1 class="wsform-module-header">Form Styles</h1>

				<table class="form-table">

					<!-- <tr valign="top">
						<th scope="row">
							<span class="form-label">Skin</span>
							<p class="wsform-note">Choose a template for your form&rsquo;s appearance. For examples, <a href="#">click here</a>.</p>
						</th>
						<td>
							<?php // $this->skin_chooser() ?>
						</td>
					</tr> -->

					<tr valign="top">
						<th scope="row">
							<span class="form-label">Max Width</span>
							<p class="wsform-note">Form defaults to width of enclosing element.</p>
						</th>
						<td>
							<input type="text" class="wsform-input wsform-data-input wsform-width sm" name="_wsform_form_width" value="<?php echo $this->form_width ?>" />
							<select class="wsform-select" id="wsform-width-unit" name="_wsform_form_width_unit">
								<option value="px"<?php if( $this->form_width_unit == 'px' ) echo ' selected' ?>>px</option>
								<option value="em"<?php if( $this->form_width_unit == 'em' ) echo ' selected' ?>>em</option>
								<option value="pct"<?php if( $this->form_width_unit == 'pct' ) echo ' selected' ?>>%</option>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<span class="form-label">Alignment</span>
							<p class="wsform-note">Form defaults to no alignment with no text wrap.</p>
						</th>
						<td>
							<select class="wsform-select" id="wsform-align" name="_wsform_form_align">
								<option value="none"<?php if( $this->form_align == 'px' ) echo ' selected' ?>>No float</option>
								<option value="left"<?php if( $this->form_align == 'left' ) echo ' selected' ?>>Float left</option>
								<option value="right"<?php if( $this->form_align == 'right' ) echo ' selected' ?>>Float right</option>
								<option value="center"<?php if( $this->form_align == 'center' ) echo ' selected' ?>>Center in container</option>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<span class="form-label">CSS Identifiers</span>
							<p class="wsform-note">Optional classes and IDs for your the &lt;div&gt; wrapper around your form.</p>
						</th>
						<td>
							<table>
								<tr>
									<td>
										<strong>Wrapper ID</strong><br />
										<input type="text" class="wsform-input wsform-data-input wsform-css-id sm" name="_wsform_css_id" value="<?php echo $this->css_id ?>" />
									</td>
									<td>
										<strong>Wrapper Class</strong><br />
										<input type="text" class="wsform-input wsform-data-input wsform-css-class sm" name="_wsform_css_class" value="<?php echo $this->css_class ?>" />
									</td>
								</tr>
							</table>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<p class="wsform-note">Optional classes and IDs for the &lt;form&gt; element.</p>
						</th>
						<td>
							<table>
								<tr>
									<td>
										<strong>Form ID</strong><br />
										<input type="text" class="wsform-input wsform-data-input wsform-form-css-id sm" name="_wsform_form_css_id" value="<?php echo $this->form_css_id ?>" />
									</td>
									<td>
										<strong>Form Class</strong><br />
										<input type="text" class="wsform-input wsform-data-input wsform-form-css-class sm" name="_wsform_form_css_class" value="<?php echo $this->form_css_class ?>" />
									</td>
								</tr>
							</table>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<span class="form-label">Submit Button</span>
							<p class="wsform-note">Enter custom information for your button.</p>
						</th>
						<td>
							<table>
								<tr>
									<td>
										<strong>Label</strong><br />
										<input type="text" class="wsform-input wsform-data-input wsform-button-label sm" name="_wsform_button_label" value="<?php echo $this->button_label ?>" placeholder="Submit" />
									</td>
									<td>
										<strong>CSS ID</strong><br />
										<input type="text" class="wsform-input wsform-data-input wsform-button-css-id sm" name="_wsform_button_css_id" value="<?php echo $this->button_css_id ?>" />
									</td>
									<td>
										<strong>CSS Class</strong><br />
										<input type="text" class="wsform-input wsform-data-input wsform-button-css-class sm" name="_wsform_button_css_class" value="<?php echo $this->button_css_class ?>" />
									</td>
								</tr>
							</table>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<p class="wsform-note">Select how the button will align in the displayed form.</p>
						</th>
						<td>
							<select class="wsform-select" id="wsform-button-align" name="_wsform_button_align">
								<option value="align-center"<?php if( $this->button_align == 'align-center' ) echo ' selected' ?>>Centered</option>
								<option value="align-left"<?php if( $this->button_align == 'align-left' ) echo ' selected' ?>>Flush left</option>
								<option value="align-right"<?php if( $this->button_align == 'align-right' ) echo ' selected' ?>>Flush right</option>
								<option value="align-full"<?php if( $this->button_align == 'align-full' ) echo ' selected' ?>>Full width</option>
							</select>
						</td>
					</tr>

				</table>

			</div>



		<?php
		//print_array( $this );
	}




	function render_form_elements()
	{
		if( empty( $this->form_elements ) ) return;

		// get the post meta & save the JSON data in a variable
		// $post = $this->form_elements;

		foreach( $this->form_elements as $post ) $this->render_form_element( $post );
	}




	// static function return_rendered_form_element( $atts = array() )
	// {
	// 	ob_start();
	// 	self::render_form_element( $atts );
	// 	return ob_get_clean();
	// }



    function render_form_element_ajax( $a = array() )
    {
        if( key_exists( 'e', $a ) ) $atts['wf_id'] = $a['e'];
        if( key_exists( 't', $a ) ) $atts['wf_type'] = $a['t'];
        // if( key_exists( 's', $a ) ) $atts['wf_sequence'] = $a['s'];
        $atts['wf_item_display'] = ' open';

        ob_start();
        $this->render_form_element( $atts );
        $this->output( ob_get_clean() );
    }




	function render_form_element( $atts = array() )
	{

		/* Render Element
		 *
		 * @param $el_type
		 * @param $el_label
		 * @param $el_value
		 * @param $atts
		 *
		 * $atts = array(
			'wf_label' => 'New Element',
			'wf_id' => 'new_element',
			'wf_type' => 'text',
			'wf_required' => 'no',
			'wf_options' => '',
			'wf_lines' => '',
			'wf_size' => '',
			'wf_default' => '',
			'wf_item_display' => '',
			'wf_placeholder' => '',
		 * );
		 *
		 */

		$atts = (array) $atts;

		// set default values
		$a = array(
			'wf_label' 			=> 'New Element',
			'wf_id' 			=> 'new_element',
			'wf_type' 			=> 'text',
			'wf_required' 		=> 'not checked',
			'wf_options' 		=> '',
			'wf_lines'			=> '',
			'wf_size'			=> '',
			'wf_item_display'	=> '',
			'wf_default'		=> '',
			'wf_placeholder'	=> '',
		);

		// Join the two arrays; add defaults if they're not set in the parameters
		$att_keys = array_keys( $atts );

		$input_types = $this->form_input_types;

		foreach( $a as $key => $val )
			if( ! in_array( $key, $att_keys ) ) $atts[$key] = $val;
        
        // // Get the sequence number if it's not given
        // if( empty( $atts['wf_sequence'] ) )
        // {
        //     preg_match( '/__[a-z0-9]{1,4}_[a-z0-9]{1,4}__/', $atts['wf_id'], $matches );
        //     if( ! empty( $matches ) ) $atts['wf_sequence'] = $matches[0];
        // }

		$el_type = $atts['wf_type'];
		$el_name = $input_types[$el_type];
        // $el_title = $input_types[$el_type];

			//"checkbox" 	=> "Checkboxes",
			//"date" 		=> "Date Field",
			//"select" 		=> "Dropdown Menu",
			//"email" 		=> "Email Address",
			//"file" 		=> "File Upload",
			//"header" 		=> "Header",
			//"number" 		=> "Number Field",
			//"hidden" 		=> "Hidden Input",
			//"password" 	=> "Password Field",
			//"radio" 		=> "Radio Buttons",
			//"section"		=> "Section",
			//"states"		=> "State Picker",
			//"tel" 		=> "Telephone Number",
			//"text" 		=> "Text Field",
			//"textarea" 	=> "Textarea",
			//"time" 		=> "Time Field",
			//"url" 		=> "Web Address",

        
		?>
        

				<li class="wsform-sg-item wsform-form-input-<?php echo $atts['wf_type']; echo $atts['wf_item_display']; ?>">

					<input class="wsform-data-input" type="hidden" name="wf_type" data-wsform-key="wf_type" value="<?php echo $atts['wf_type'] ?>" />

					<div class="wsform-sg-item-header">
						<span class="wsform-sg-icon wsform-sg-move-item ico-menu" title="Move Element"></span>
						<span class="wsform-sg-header-label">
							<span class="wsform-sg-label"><?php echo $el_name ?>: </span>
							<span class="wsform-sg-info"><span class="wsform-sg-input-label"><?php echo $atts['wf_label'] ?></span></span>
						</span>
						<span class="wsform-sg-icon wsform-dup-item ico-list-add" title="Copy Element"></span>
						<span class="wsform-sg-icon wsform-delete-item ico-cancel" title="Delete Element"></span>
						<span class="wsform-sg-icon ico-wsform-sg-toggle-item ico-down" title="Edit Element"></span>
					</div>

					<div class="wsform-sg-item-body">

						<div class="wsform-sg-item-body-content">

							<div class="wsform-sg-item-param">
								<span class="wsform-sg-item-label">
									Label
								</span>
								<span class="wsform-sg-item-input">
									<input type="text" class="wsform-input wsform-data-input wsform-label wsform-keyup-change lg" data-wsform-key="wf_label" rel="wsform-label" value="<?php echo $atts['wf_label'] ?>" />
								</span>
							</div>

							<?php if( ! in_array( $el_type, array( 'header', 'section' ) ) ) : ?>
							<div class="wsform-sg-item-param">
								<span class="wsform-sg-item-label">
									ID
								</span>
								<span class="wsform-sg-item-input">
									<input type="text" class="wsform-input wsform-data-input wsform-id lg" data-wsform-key="wf_id" rel="wsform-id" value="<?php echo $atts['wf_id'] ?>" />
								</span>
							</div>
							<?php endif ?>

							<?php if( ! in_array( $el_type, array( 'hidden', 'header', 'section' ) ) ) : ?>
							<div class="wsform-sg-item-param">
								<span class="wsform-sg-item-label">
									Required
								</span>
								<span class="wsform-sg-item-input">
									<label class="wsform-label-el inline">
										<input type="checkbox" class="wsform-input wsform-data-input wsform-required wsform-sg-checkbox" data-wsform-key="wf_required" rel="wsform-required" value="<?php echo $atts['wf_required'] ?>"<?php if( $atts['wf_required'] == 'checked' ) echo ' checked' ?> />
									</label>
								</span>
							</div>
							<?php endif; ?>

							<?php if( in_array( $el_type, array( 'checkbox', 'radio', 'select' ) ) ) : ?>
							<div class="wsform-sg-item-param">
								<span class="wsform-sg-item-label">
									Options
								</span>
								<span class="wsform-sg-item-input">
									<textarea class="wsform-input wsform-data-input wsform-options lg" data-wsform-key="wf_options" rel="wsform-options"><?php echo $atts['wf_options'] ?></textarea>
									<span class="wsform-sg-item-input-note">Enter each choice on a new line. For more control, you may specify both a value and label like "red : Red"</span>
								</span>
							</div>

							<?php endif; ?>

							<?php if( $el_type == 'header' ) : ?>
							<div class="wsform-sg-item-param">
								<span class="wsform-sg-item-label">
									Size
								</span>
								<span class="wsform-sg-item-input">
									<select class="wsform-input wsform-data-input wsform-form-required" data-wsform-key="wf_size" rel="wsform-required">
										<option value="h2"<?php if( $atts['wf_size'] == 'h2' ) echo ' selected' ?>>Heading 2</option>
										<option value="h4"<?php if( $atts['wf_size'] == 'h4' ) echo ' selected' ?>>Heading 4</option>
										<option value="h6"<?php if( $atts['wf_size'] == 'h6' ) echo ' selected' ?>>Heading 6</option>
									</select>
								</span>
							</div>
							<?php endif; ?>

							<?php if( $el_type == 'states' ) : ?>
								<?php $states = self::states_list( 'backend' ) ?>
								<div class="wsform-sg-item-param">
									<span class="wsform-sg-item-label">
										Format
									</span>
									<span class="wsform-sg-item-input">
										<select class="wsform-input wsform-data-input wsform-required" data-wsform-key="wf_format" rel="wsform-required">
											<option value="two-letter"<?php if( $atts['wf_format'] == 'two-letter' ) echo ' selected' ?>>Two-letter code</option>
											<option value="full"<?php if( $atts['wf_format'] == 'full' ) echo ' selected' ?>>Full state name</option>
										</select>
									</span>
								</div>
								<div class="wsform-sg-item-param">
									<span class="wsform-sg-item-label">
										Default Value
									</span>
									<span class="wsform-sg-item-input">
										<select class="wsform-input wsform-data-input wsform-required" data-wsform-key="wf_default" rel="wsform-required">
											<option value="">Select ...</option>
											<?php foreach( $states as $ab => $name ) : ?>
											<option value="<?php echo $ab ?>"<?php if( $ab == $atts['wf_default'] ) echo ' selected' ?>><?php echo $name ?></option>
											<?php endforeach; ?>
										</select>
									</span>
								</div>
							<?php endif; ?>

							<?php if( $el_type == 'textarea' ) : ?>
							<div class="wsform-sg-item-param">
								<span class="wsform-sg-item-label">
									Rows
								</span>
								<span class="wsform-sg-item-input">
									<select class="wsform-input wsform-data-input wsform-required" data-wsform-key="wf_lines" rel="wsform-required">
										<?php for( $c = 2 ; $c <= 10 ; $c++ ) : ?>
										<option<?php if( $atts['wf_lines'] == $c ) echo ' selected' ?>><?php echo $c ?></option>
										<?php endfor; ?>
									</select>
									<span class="wsform-sg-item-input-note">Number of lines for the textarea.</span>
								</span>
							</div>
							<?php endif; ?>

							<?php if( $el_type == 'number' ) :

							$minval = array_key_exists( 'wf_minval', $atts ) ? $atts['wf_minval'] : '';
							$maxval = array_key_exists( 'wf_maxval', $atts ) ? $atts['wf_maxval'] : '';
							?>
							<div class="wsform-sg-item-param">
								<span class="wsform-sg-item-label">
									Range
								</span>
								<span class="wsform-sg-item-input">
									Min:
									<input type="text" class="wsform-input wsform-data-input wsform-numvals tiny" data-wsform-key="wf_minval" rel="wsform-minval" value="<?php echo $minval ?>" />
									Max:
									<input type="text" class="wsform-input wsform-data-input wsform-numvals tiny" data-wsform-key="wf_maxval" rel="wsform-maxval" value="<?php echo $maxval ?>" />
									<span class="wsform-sg-item-input-note">Range of number input.</span>
								</span>
							</div>
							<?php endif; ?>

							<?php if( $el_type == 'file' ) :

							$filetype_doc = array_key_exists( 'wf_filetype_doc', $atts ) ? $atts['wf_filetype_doc'] : '';
							$filetype_img = array_key_exists( 'wf_filetype_img', $atts ) ? $atts['wf_filetype_img'] : '';
							$filetype_audio = array_key_exists( 'wf_filetype_audio', $atts ) ? $atts['wf_filetype_audio'] : '';
							$filetype_pdf = array_key_exists( 'wf_filetype_pdf', $atts ) ? $atts['wf_filetype_pdf'] : '';
							?>
							<div class="wsform-sg-item-param">
								<span class="wsform-sg-item-label">
									File Types
								</span>
								<span class="wsform-sg-item-input">
									<label class="wsform-label-el" for="wsform-filetype-doc">
										<input type="checkbox" class="wsform-input wsform-data-input wsform-form-filetype wsform-sg-checkbox" data-wsform-key="wf_filetype_doc" id="wsform-filetype-doc" rel="wsform-filetype-img" value="<?php echo $filetype_img ?>"<?php if( $filetype_img == 'checked' ) echo ' checked' ?> />
										<span class="wsform-label-info">Text Documents (.txt, .doc, etc.)</span>
									</label>
									<label class="wsform-label-el" for="wsform-filetype-img">
										<input type="checkbox" class="wsform-input wsform-data-input wsform-form-filetype wsform-sg-checkbox" data-wsform-key="wf_filetype_img" id="wsform-filetype-img" rel="wsform-filetype-img" value="<?php echo $filetype_img ?>"<?php if( $filetype_img == 'checked' ) echo ' checked' ?> />
										<span class="wsform-label-info">Images (.jpg, .gif, etc.)</span>
									</label>
									<label class="wsform-label-el" for="wsform-filetype-audio">
										<input type="checkbox" class="wsform-input wsform-data-input wsform-form-filetype wsform-sg-checkbox" data-wsform-key="wf_filetype_audio" id="wsform-filetype-audio" rel="wsform-filetype-audio" value="<?php echo $filetype_audio ?>"<?php if( $filetype_audio == 'checked' ) echo ' checked' ?> />
										<span class="wsform-label-info">Audio (.mp3, .wav, etc.)</span>
									</label>
									<label class="wsform-label-el" for="wsform-filetype-pdf">
										<input type="checkbox" class="wsform-input wsform-data-input wsform-form-filetype wsform-sg-checkbox" data-wsform-key="wf_filetype_pdf" id="wsform-filetype-pdf" rel="wsform-filetype-pdf" value="<?php echo $filetype_pdf ?>"<?php if( $filetype_pdf == 'checked' ) echo ' checked' ?> />
										<span class="wsform-label-info">PDFs</span>
									</label>
								</span>
							</div>
							<div class="wsform-sg-item-param">
								<span class="wsform-sg-item-label"></span>
								<span class="wsform-sg-item-input">
									<span class="wsform-sg-item-input-note">
										<strong>Note: </strong>Your system limits your uploads to a max size of <strong><?php echo wsform_simplify_bytes( wsform_file_upload_max_size() ) ?></strong>.
										<a href="https://www.cloudways.com/blog/increase-media-file-maximum-upload-size-in-wordpress/" target="_blank">Click here</a> to learn how to configure that size.
									</span>
								</span>
							</div>
							<?php endif; ?>

							<?php if( $el_type == 'hidden' ) :

							$hidden = array_key_exists( 'hidden', $atts ) ? $atts['wf_hidden'] : '';

							?>
							<div class="wsform-sg-item-param">
								<span class="wsform-sg-item-label">
									Value
								</span>
								<span class="wsform-sg-item-input">
									<input type="text" class="wsform-input wsform-data-input wsform-id lg" data-wsform-key="wf_hidden" rel="wsform-hidden" value="<?php echo $hidden ?>" />
									<span class="wsform-sg-item-input-note">Value for hidden input.</span>
								</span>
							</div>

						<?php elseif( ! in_array( $el_type, array( 'password', 'header', 'section', 'states' ) ) ) : 
                            $input_note = 'Sets a default value for the input';
                            if( $el_type == 'date' ) $input_note .= ' (enter "current date" to use current date)';
                            ?>
							<div class="wsform-sg-item-param">
								<span class="wsform-sg-item-label">
									Default Value
								</span>
								<span class="wsform-sg-item-input">
									<input type="text" class="wsform-input wsform-data-input wsform-default-val lg" data-wsform-key="wf_default" rel="wsform-default-val" value="<?php echo $atts['wf_default'] ?>" />
									<span class="wsform-sg-item-input-note"><?php echo $input_note ?></span>
								</span>
							</div>
							<?php endif; ?>

							<?php if( in_array( $el_type, array( 'file', 'select' ) ) ) :

							$multiple = array_key_exists( 'wf_allow_multiple', $atts ) ? $atts['wf_allow_multiple'] : false;

							?>
							<div class="wsform-sg-item-param">
								<span class="wsform-sg-item-label">
									Multiple
								</span>
								<span class="wsform-sg-item-input">
									<label class="wsform-label-el inline">
										<input type="checkbox" class="wsform-input wsform-data-input wsform-form-allow-multiple wsform-sg-checkbox" data-wsform-key="wf_allow_multiple" rel="wsform-allow-multiple" value="<?php echo $multiple ?>"<?php if( $multiple == 'checked' ) echo ' checked' ?> />
									</label>
									<span class="wsform-sg-item-input-note">Allows user to make more than one selection.</span>
								</span>
							</div>
							<?php endif; ?>

							<?php if( in_array( $el_type, array( 'date', 'email', 'tel', 'text', 'textarea', 'time', 'url' ) ) ) : ?>
							<div class="wsform-sg-item-param">
								<span class="wsform-sg-item-label">
									Placeholder
								</span>
								<span class="wsform-sg-item-input">
									<input type="text" class="wsform-input wsform-data-input wsform-placeholder lg" data-wsform-key="wf_placeholder" rel="wsform-placeholder" value="<?php echo $atts['wf_placeholder'] ?>" />
									<span class="wsform-sg-item-input-note">Sample form input</span>
								</span>
							</div>
							<?php endif; ?>

							<?php if( in_array( $el_type, array( 'section' ) ) ) : ?>
							<div class="wsform-sg-item-param">
								<span class="wsform-sg-item-label">
									Description
								</span>
								<span class="wsform-sg-item-input">
									<textarea class="wsform-input wsform-data-input wsform-default-val lg" data-wsform-key="wf_default" rel="wsform-options"><?php echo $atts['wf_default'] ?></textarea>
									<span class="wsform-sg-item-input-note">Short description for section.</span>
								</span>
							</div>
							<?php endif; ?>

						</div>

					</div>

				</li>

		<?php
	}





	function meta_form_output()
	{
		//print_array( $this );

		//// get the post metas & save in variables
		//$the_id = $this->post_id;
		//
		//$send_email    		= $this->output_send_email;
		//$form_recipient 	= $this->form_recipient;
		//$form_sender		= $this->form_sender;
		//$send_receipt 		= $this->form_send_receipt;
		//$hide_name_email 	= $this->hide_name_email;
		//$email_return		= $this->form_email_return;
		//$save_responses		= $this->output_save_email_responses;
		//$output_path 		= wsform_PATH_TO_CUSTOM_OUTPUT;
		//$meta_val			= '_wsform_form_output_slug';
		//$slug_name 			= get_post_meta( $the_id, $meta_val, 1 );

		// Output the HTML
		?>

			<div id="wsform-output" class="wsform-meta-box-module wsform-form-output active">

				<h1 class="wsform-module-header">Form Output</h1>

				<p>Decide what to do with the output</p>

				<div class="wsform-checkboxes-div">

					<table class="form-table">

						<tr valign="top">
							<th scope="row">
                                <span class="wsform-forms-radio-label">Email Receipts</span>
							</th>
							<td>
								<label class="wsform-label inline" for="wsform-form-send-receipt">
									<input type="checkbox" class="wsform-checkbox wsform-output-choose" id="wsform-form-send-receipt" name="_wsform_form_send_receipt" value="<?php echo $this->form_send_receipt ?>"<?php if( $this->form_send_receipt == 'checked' ) echo ' checked' ?> />
									Click to receive email receipts when you get responses to this form.
								</label>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
                                <span class="wsform-forms-radio-label">Receipt Addresses</span>
							</th>
							<td>
								<label class="wsform-label inline">
									<textarea id="wsform-form-recipient" class="wsform-input wsform-output-watcher lg" rel="_wsform_form_recipient" name="_wsform_form_recipient" placeholder="e.g. email1@domain.com, email2@domain.com" style="height: 66px; resize: none"><?php echo $this->form_recipient ?></textarea>
									Enter an email address or addresses to collect the form output. Separate multiple addresses with commas. Overrides <strong><em>&ldquo;Default Email Recipient&rdquo;</em></strong> value in <a href="edit.php?post_type=wsform_form&page=forms-options">Settings</a>.
								</label>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
                                <span class="wsform-forms-radio-label">Send Replies</span>
							</th>
							<td>
								<label class="wsform-label inline">
									<input type="checkbox" class="wsform-checkbox wsform-output-choose" id="wsform-output-send-reply" name="_wsform_form_send_reply" value="<?php echo $this->form_send_reply ?>"<?php if( $this->form_send_reply == 'checked' ) echo ' checked' ?> />
									Click to send an automated reply to senders for all form submissions.
								</label>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
                                <span class="wsform-forms-radio-label">Reply Sender Name</span>
							</th>
							<td>
								<input id="wsform-output-sender-name" class="wsform-input wsform-output-watcher lg" rel="_wsform_form_sender_name" name="_wsform_form_sender_name" placeholder="Joe Smith" value="<?php echo $this->form_sender_name ?>" />
								<p class="wsform-note">Enter a name for the &ldquo;From&rdquo; field for email responses. Overrides website name <?php echo $this->site_name ?>.</p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
                                <span class="wsform-forms-radio-label">Reply Sender Email</span>
							</th>
							<td>
								<input type="email" id="wsform-sender-email" class="wsform-input wsform-output-watcher lg" rel="_wsform_form_sender" name="_wsform_form_sender" placeholder="email@domain.com" value="<?php echo $this->form_sender ?>" />
								<p class="wsform-note">Enter an email address for the &ldquo;From&rdquo; field for email responses. Overrides <strong>"Default Email Sender"</strong> value in <a href="edit.php?post_type=wsform_form&page=forms-options">Settings</a>.</p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
                                <span class="wsform-forms-radio-label">Format Output</span>
							</th>
							<td>
								<?php self::custom_template_select( $this->path_to_custom_output, $this->output_slug, '_wsform_output_slug' ) ?>
								<p class="wsform-note">Select a template if you want to format the output of the email form.</p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
                                <span class="wsform-forms-radio-label">Success Message</span>
							</th>
							<td>
								<textarea id="wsform-form-success-message" class="wsform-input wsform-output-watcher lg" rel="_wsform_form_success_message" name="_wsform_form_success_message" placeholder="e.g. Message successfully sent!" style="height: 66px; resize: none"><?php echo $this->form_success_message ?></textarea>
								<p class="wsform-note">Enter a message that the user will receive when the form is successfully submitted. Overrides <strong>"Default Success Message"</strong> value in <a href="edit.php?post_type=wsform_form&page=forms-options">Settings</a>.</p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
                                <span class="wsform-forms-radio-label">Error Message</span>
							</th>
							<td>
								<p class="wsform-note">Enter a message that the user will receive when the form is attempted to be submitted with errors. Overrides <strong>"Default Error Message"</strong> value in <a href="edit.php?post_type=wsform_form&page=forms-options">Settings</a>.</p>
								<textarea id="wsform-form-error-message" class="wsform-input wsform-output-watcher lg" rel="_wsform_form_error_message" name="_wsform_form_error_message" placeholder="e.g. Message cannot be sent with errors" style="height: 66px; resize: none"><?php echo $this->form_error_message ?></textarea>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
                                <span class="wsform-forms-radio-label">Success URL</span>
							</th>
							<td>
								<input id="wsform-success-url" class="wsform-input wsform-output-watcher lg" rel="_wsform_form_success_url" name="_wsform_form_success_url" placeholder="e.g. <?php echo get_option( 'home' ) ?>" value="<?php echo $this->form_success_url ?>" />
								<p class="wsform-note">Enter a destination for a successful form submission. Leave blank to remain on current page.</p>
							</td>
						</tr>

						<!--
						<tr valign="top">
							<th scope="row">
								<label class="wsform-label inline" for="wsform-output-chooser-response-save">
									<input type="checkbox" class="wsform-checkbox wsform-output-choose" id="wsform-output-chooser-response-save" name="_wsform_output_save_email_responses" value="" />
									<span class="wsform-forms-radio-label">Save Responses</span>
								</label>
							</th>
							<td>
								<p class="wsform-note">Saves responses to your form in a .csv spreadsheet file.</p>
								<p>
									<button class="wsform-sel-all">View Responses</button>
									<button class="wsform-sel-all">Reset Responses</button>
								</p>
							</td>
						</tr>
						-->

					</table>

				</div>

			</div>

	<!--	<p style="text-align: center;">
			<button id="test-vars" class="button button-primary button-large">Test Vars</button>
		</p>

		<div id="dump-vars"></div> -->


		<?php
	}




	static function custom_template_select( $path, $slug, $name )
	{

		// get a list of saved forms
		$dir = scandir( $path );

		// create an array and put the templates in it
		$arr = array();

		foreach( $dir as $val )
		{
			if( strpos( $val, '.php' ) > 0 )
			{
				$contents = file_get_contents( $path . '/' . $val );

				preg_match( '/\/\*\sTemplate\sName:\s(.+)\s\*\//', $contents, $matches );

				//print_array( $matches, 1 );

				if( array_key_exists( 1, $matches ) )
				{
					$temp_name = $matches[1];
					$arr[$val] = $temp_name;
				}
			}
		}

		if( count( $arr ) == 0 ) return false;

		// sort the resulting array
		asort( $arr );

		// Use the array to build a dropdown menu
		// Menu matches to $slug value
		?>

				<select class="wsform-select" id="<?php echo $name ?>" name="<?php echo $name ?>">
					<option value="">No special formatting</option>
					<?php foreach( $arr as $k => $v ) : ?>
					<option value="<?php echo $k ?>"<?php if( $slug == $k ) echo ' selected' ?>><?php echo $v ?></option>
					<?php endforeach; ?>
				</select>

				<!-- Output slug is: <?php echo $slug ?> -->

		<?php

	}











	static function admin_shortcode_handle( $form_id )
	{
		return '&lsqb;webbsitesform id=&quot;' . $form_id . '&quot;&rsqb;';
	}

	static function admin_php_handle( $form_id )
	{
		return '&lt;?php webbsitesform&lpar;' . $form_id . '&rpar;&semi; ?&gt;';
	}





	function skin_chooser()
	{
		$skins = array(
			'basic' 	=> 'Basic Form',
			'orange' 	=> 'Orange',
			'skeleton'	=> 'Skeleton'
			//'daytime' 	=> 'Daytime',
			//'funky' 	=> 'Funky',
			//'smooth' 	=> 'Smooth'
		);

		?>
		<select class="wsform-select" id="wsform-skin-select" name="_wsform_form_skin">
		<?php

		foreach( $skins as $key => $skin )
		{
			echo '<option value="' . $key . '"';
			if( $this->form_skin == $key ) echo ' selected';
			echo '>' . $skin . '</option>';
		}
		?>
		</select>

		<?php
	}






	function form_keys()
	{
		$els = $this->form_elements;

		$arr = array();

		if( empty( $els ) ) return;

		foreach( $els as $el )
		{
			if( ! in_array( $el->wf_type, array( 'header', 'section' ) ) )
			{
				$arr[$el->wf_id]['wf_label'] = $el->wf_label;
				$arr[$el->wf_id]['wf_type']  = $el->wf_type;
			}
		}

		$this->form_keys = $arr;
	}









	//function form_skins()
	//{
	//	$skins = array(
	//		'basic' 	=> 'Basic Form',
	//		'orange' 	=> 'Orange',
	//		'skeleton'	=> 'Skeleton'
	//		//'daytime' 	=> 'Daytime',
	//		//'funky' 	=> 'Funky',
	//		//'smooth' 	=> 'Smooth'
	//	);
	//
	//	asort( $skins );
	//
	//	return $skins;
	//}


	static function populate_quiver()
	{
		// $input_types = self::form_input_types();

		// $elements = array();

		// foreach( $input_types as $key => $val )
		// {
		// 	$atts = array(
		// 		'wf_label' => $val,
		// 		'wf_type' => $key
		// 	 );

		// 	$elements[$key] = self::return_rendered_form_element( $atts );
		// }

		// return $elements;
	}







	static function forms_css()
	{
        global $wsf;

        $opt = $wsf->opt_vals;

		$return  = '<style type="text/css">:root {--wsform-dominant: ' . $opt->dominant_color
			. ';--wsform-text: ' . $opt->text_color . ';--wsform-header: ' . $opt->header_color 
            . ';--wsform-background: ' . $opt->background_color . ';--wsform-field: ' . $opt->field_color 
            . ';--wsform-field-border: ' . $opt->field_border_color . ';--wsform-field-border-width: ' 
            . $opt->field_border_width . ';--wsform-field-border-radius: ' . $opt->field_border_radius . 'px}'
			. '#' . $opt->honeypot_id . '{display:none;position:absolute;top:-9999px;left:-9999px;}</style>';

		echo $return;
	}




	function state_picker( $args = null )
	{
		/*
		$args = array(
			'css_class' => $css_class (string),
			'css_id'	=> $css_id (string),
			'default'	=> $default (string),
			'format'	=> $format (string 'abbreviate' | 'full'),
			'name' 		=> $name (string)
		);
		*/

		$css_class	= ! empty( $args['css_class'] ) ? trim( $args['css_class'] ) 	: null;
		$css_id 	= ! empty( $args['css_id'] ) 	? trim( $args['css_id'] ) 		: null;
		$default 	= ! empty( $args['default'] ) 	? trim( $args['default'] ) 		: null;
		$format 	= $args['format'] == 'full' 	? 'full' 						: 'abbreviate';
		$name 		= ! empty( $args['name'] ) 		? trim( $args['name'] ) 		: null;

		// Can't have a form object without a name!
		if( $name == null ) return false;

		// Get the list of countries
		$states = self::states_list();
		?>

		<select name="<?php echo $name ?>"<?php if( $css_class != null ) : ?> class="<?php echo $css_class ?>"<?php endif; ?><?php if( $css_id != null ) : ?> id="<?php echo $css_id ?>"<?php endif; ?> data-field-type="select">
			<option value="">Select ...</option>
			<?php foreach( $states as $ab => $name ) : ?>
			<option value="<?php echo $ab ?>"<?php if( $ab == $default ) echo ' selected' ?>><?php echo $ab ?></option>
			<?php endforeach; ?>
		</select>

		<?php
	}







	static function states_list( $format = 'full' ) {

		$states = array( 'AL' => "Alabama",
			'AK' => "Alaska",
			'AZ' => "Arizona",
			'AR' => "Arkansas",
			'CA' => "California",
			'CO' => "Colorado",
			'CT' => "Connecticut",
			'DE' => "Delaware",
			'FL' => "Florida",
			'GA' => "Georgia",
			'HI' => "Hawaii",
			'ID' => "Idaho",
			'IL' => "Illinois",
			'IN' => "Indiana",
			'IA' => "Iowa",
			'KS' => "Kansas",
			'KY' => "Kentucky",
			'LA' => "Louisiana",
			'MN' => "Maine",
			'MD' => "Maryland",
			'MA' => "Massachusetts",
			'MI' => "Michigan",
			'MN' => "Minnesota",
			'MS' => "Mississippi",
			'MO' => "Missouri",
			'MN' => "Montana",
			'NE' => "Nebraska",
			'NV' => "Nevada",
			'NH' => "New Hampshire",
			'NJ' => "New Jersey",
			'NM' => "New Mexico",
			'NY' => "New York",
			'NC' => "North Carolina",
			'ND' => "North Dakota",
			'OH' => "Ohio",
			'OK' => "Oklahoma",
			'OR' => "Oregon",
			'PA' => "Pennsylvania",
			'RI' => "Rhode Island",
			'SC' => "South Carolina",
			'SD' => "South Dakota",
			'TN' => "Tennessee",
			'TX' => "Texas",
			'UT' => "Utah",
			'VT' => "Vermont",
			'VA' => "Virginia",
			'WA' => "Washington",
			'WV' => "West Virginia",
			'WI' => "Wisconsin",
			'WY' => "Wyoming",
			'GU' => "Guam",
			'MH' => "Marshall Islands",
			'MP' => "Northern Mariana Islands",
			'PR' => "Puerto Rico",
			'VI' => "Virgin Islands",
			'AS' => "American Samoa",
			'AB' => "Alberta",
			'BC' => "British Columbia",
			'MB' => "Manitoba",
			'NB' => "New Brunswick",
			'NL' => "Newfoundland and Labrador",
			'NT' => "Northwest Territories",
			'NS' => "Nova Scotia",
			'NU' => "Nunavut",
			'ON' => "Ontario",
			'PE' => "Prince Edward Island",
			'QC' => "Quebec",
			'SK' => "Saskatchewan",
			'YT' => "Yukon",
			'NA' => "Other -- N/A"
		);

		if( $format == 'backend' ) return $states;

		foreach( $states as $abb => $name )
		{
			if( $format == 'full' )
			{
				$states_list[] = array( $name );
			}
			else
			{
				$states_list[] = array( $abb );
			}
		}

		return $states_list;
	}




    static function fancy_date( $time )
    {
		$format = 'g:i a, l, F j, Y';
        $tz = wp_timezone();

        // Now
        $now = new DateTime();
        $now->setTimezone( $tz );

        // Match time
        $md = new DateTime();
        $md->setTimestamp( $time );
        $md->setTimezone( $tz );
        $md->setTime( 0,0,0 );
        $i = $now->diff( $md );

        // Set timestamp back to saved time (to print correctly)
        $md->setTimestamp( $time );

        if( $i->days == 0 ) return 'Today at ' . $md->format( 'g:i a' );
        if( $i->days == 1 ) return 'Yesterday at ' . $md->format( 'g:i a' );
        elseif( $i->days < 7 ) return $md->format( 'l' ) . ' at ' . $md->format( 'g:i a' );
        else return $md->format( 'F j, Y' ) . ' at ' . $md->format( 'g:i a' );
    }






	function send_error( $error )
	{
        $this->error = true;
        $this->error_msg = $error;
        $this->output( 'error' );
        die();
	}





    function output( $output = '' )
    {
        $this->output_data['error'] = $this->error;
        $this->output_data['error_msg'] = $this->error_msg;
        $this->output_data['output_atts'] = $this->output_atts;
        $this->output_data['output'] = $output;

        echo json_encode( $this->output_data );
    }





    // STATIC FUNCTIONS
    static function delete_uploads( $post_id )
    {
        // We check if the global post type isn't ours and just return
        global $post_type;
        if ( $post_type != 'wsform_sub' ) return;
    
        // Check for any uploads left in the upload folder
        if( ! $uploads = get_post_meta( $post_id, '_wsform_attached_file' ) ) return;
    
        // Delete the uploads
        foreach( $uploads as $file )
        {
            $path = wsform_UPLOADS_DIR_PATH . '/' . $file;
            unlink( $path );
        }
    }



    static function delete_post_subs( $post_id )
    {
        // We check if the global post type isn't ours and just return
        global $post_type;
        if ( $post_type != 'wsform_sub' ) return;
    
    }
    






	function __destruct() {}

}
