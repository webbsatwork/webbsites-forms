<?php

/**
 * @package Webbsites Forms
 * version 0.9.0
 *
 * Setup File
 *
 */

// Load required file.php
require_once( ABSPATH . 'wp-admin/includes/file.php' );

// Enable shortcodes in widgets
add_filter( 'widget_text', 'do_shortcode' );

// new line; set the CRLF in a variable
$nl = "\r\n";

// set the version
$ver = get_bloginfo( 'version' );

// create a global variable
global $wsform_form;


// Increase the upload sizes
@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'upload_max_filesize' , '64M' );
@ini_set( 'post_max_size', '64M');
@ini_set( 'max_execution_time', '300' );



// create php directory
add_action( 'wp_loaded', 'wsform_setup_form_plugin' );



function wsform_setup_form_plugin()
{
	//wsform_options();
	$wf = new WebbsitesForm();

    // $options = $wf->opt_vals;

	// default forms email sender
	if( $wf->default_sender == '' )
	{
		$wf->default_sender = 'no-reply@' . $_SERVER['SERVER_NAME'];
		update_option( '_wsform_opt_from_email', $options->default_sender );
	}

	// default honeypot id
	if( $wf->honeypot_id == '' )
	{
		$wf->honeypot_id = 'wf_url';
		update_option( '_wsform_opt_honeypot_id', $wf->honeypot_id );
	}

	// DEFINE CONSTANTS
	// save user_id constant
	define( 'WSFORM_USER', $wf->daemon_id );

	// default forms email
	define( 'WSFORM_DEFAULT_FORMS_EMAIL', 'forms@webbsatwork.com' ); // for system email daemon, if real user isn't chosen

	// set constant for email sender
	define( 'WSFORM_EMAIL_FROM', $wf->default_sender );

	// set constant for email sender
	define( 'WSFORM_HONEYPOT_ID', $wf->honeypot_id );

	//// set constant for email sender
	//define( 'WSFORM_SSL_KEY', $wf->openssl_key );

	// constant for path to forms folder
	define( 'WSFORM_SITE_HOME_DIR', get_home_path() );

	// constant for path to plugin folder
	define( 'WSFORM_PATH_TO_PLUGIN', WSFORM_SITE_HOME_DIR . 'wp-content/plugins/webbsites-forms/' );

	// constant for path to forms folder
	define( 'WSFORM_PATH_TO_FORMS', WSFORM_SITE_HOME_DIR . 'wp-content/webbsites-forms/' );

	// constant for path to predefined forms
	define( 'WSFORM_PATH_TO_CUSTOM_FORMS', WSFORM_PATH_TO_PLUGIN . 'lib/custom/' );

	// constant for path to predefined outputs
	define( 'WSFORM_PATH_TO_CUSTOM_OUTPUT', WSFORM_PATH_TO_PLUGIN . 'lib/output/' );

	// constant url to webbsites plugin directory
	define( 'WSFORM_URL_TO_PLUGIN', site_url( '/wp-content/plugins/webbsites-forms/' ) );

	// constant to url for form action
	define( 'WSFORM_URL_TO_FORM_DISPLAY', WSFORM_URL_TO_PLUGIN . 'lib/submission.php' );
	// define( 'WSFORM_URL_TO_FORM_DISPLAY', site_url( '/wp-content/wsform-forms/submission.php' )  );

	// create folder for forms if it doesn't exist
    if ( ! file_exists( WSFORM_PATH_TO_FORMS ) )
		mkdir( WSFORM_PATH_TO_FORMS, 0755, true );

	// if the submission display file isn't there, put it there
	define( 'WSFORM_PATH_TO_DISPLAY_FORMS',  WSFORM_PATH_TO_FORMS . 'submission.php' );

	if( ! file_exists( WSFORM_PATH_TO_DISPLAY_FORMS ) )
	{
		$master_file = file_get_contents( WSFORM_PATH_TO_PLUGIN . 'lib/submission.php' );
		file_put_contents( WSFORM_PATH_TO_DISPLAY_FORMS, $master_file );
	}

	// define the upload directory
	define( 'WSFORM_UPLOADS_DIR', site_url( '/wp-content/webbsites-forms/uploads/' )  );
	define( 'WSFORM_UPLOADS_DIR_PATH', WSFORM_PATH_TO_FORMS . 'uploads/'  );

	// create uploads folder if it doesn't exist
	if( ! file_exists( WSFORM_UPLOADS_DIR_PATH ) )
		mkdir( WSFORM_UPLOADS_DIR_PATH, 0755, true );
}



// add stylesheets and scripts
add_action( 'wp_enqueue_scripts', 'wsform_enqueue_scripts' );

function wsform_enqueue_scripts()
{
	global $ver;

	// frontend styles
	wp_enqueue_style( 'webbsites-forms', WSFORM_URL_TO_PLUGIN . 'lib/css/webbsites-forms.css', null, $ver );
	wp_enqueue_style( 'roboto', '//fonts.googleapis.com/css?family=Roboto+Condensed:400,400i,700,700i&display=swap', null, $ver );

	// frontend scripts
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'webbsites-forms', WSFORM_URL_TO_PLUGIN . 'lib/js/webbsites-forms.js', null, $ver );

	// Make the WP ajax url available on the frontend
	$wsform_ajax_array = array(
        'nonce' => wp_create_nonce( 'wsform_ajax' ),
        'ajax_url' => admin_url( 'admin-ajax.php' )
	);

	wp_localize_script( 'webbsites-forms', 'wsform_ajax_pub', $wsform_ajax_array );
}



// honeypot css
add_action( 'wp_footer', 'wsform_css' );


// add admin stylesheet and scripts
add_action( 'admin_enqueue_scripts', 'wsform_admin_scripts' );

function wsform_admin_scripts()
{
	global $ver;

    // Only load on WS Forms pages
	$screen = get_current_screen();
    if( ! in_array( $screen->post_type, [ 'wsform_form', 'wsform_sub' ] ) ) return false;

	// plugin css
    wp_enqueue_style( 'webbsites-forms-admin-css', WSFORM_URL_TO_PLUGIN . 'lib/css/webbsites-forms-admin.css', null, $ver );
    wp_enqueue_style( 'spectrum-css', WSFORM_URL_TO_PLUGIN . 'lib/css/spectrum.css', null, $ver );

	// jquery & jquery ui
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-sortable' );

	// plugin js
    wp_enqueue_script( 'webbsites-forms-admin', WSFORM_URL_TO_PLUGIN . 'lib/js/webbsites-forms-admin.js', null, $ver );
    wp_enqueue_script( 'spectrum', WSFORM_URL_TO_PLUGIN . 'lib/js/spectrum.js', null, $ver );

	// Preload all of the inputs for the forms builder
	$wsform_input_types = WebbsitesForm::populate_quiver();
	wp_localize_script( 'webbsites-forms-admin', 'wsform_input_types', $wsform_input_types );

    // Localize get path & nonce
	wp_localize_script( 
        'webbsites-forms-admin', 
        'wsform_ajax_admin', 
        [ 
            'nonce' => wp_create_nonce( 'wsform_ajax' ),
            'get_file_url' => plugins_url( '/ajax/ws-forms-ajax-get.php?', __FILE__ )
        ] 
    );
}


// echo plugins_url( '/ajax/', __FILE__ );


//function wsform_disable_forms_plugin( $errormsg )
//{
//	$pluginlog = plugin_dir_path( __FILE__ ) . 'debug.log';
//	$message = 'SOME ERROR' . PHP_EOL;
//	error_log($message, 3, $pluginlog);
//}



// update/create daemon
add_action( 'wp_loaded', 'wsform_update_forms_daemon' );

function wsform_update_forms_daemon()
{
	$forms_user 	= 'forms.daemon';
	$forms_email 	= WSFORM_DEFAULT_FORMS_EMAIL;
	$forms_password = 'inexplicable*content_kraze';


	// Set up forms daemon; use current user if none is set up; create daemon if user asks for it and it doesn't exist
	$forms_daemon = WSFORM_USER;

	if( $forms_daemon == 'wf' )
	{
		if( email_exists( $forms_email ) )
		{
			$user = get_user_by( 'email', $forms_email );
			$user_id = $user->ID;
			update_option( '_wsform_opt_daemon_id', $user_id );
		}
		else
		{
			// new user arguments
			$user_args = array(
				'user_pass'			=> $forms_password,
				'user_login'		=> $forms_user,
				'user_email'		=> $forms_email,
				'display_name'		=> 'Forms Daemon',
				'first_name'		=> 'Forms',
				'last_name'			=> 'Daemon',
				'user_url'			=> 'https://webbsatwork.com/',
				'description'		=> 'User created by plugin to serve as author for all form submissions.',
				'user_registered'	=> date( 'Y-m-d H:i:s' ),
				'role'				=> 'editor'
			);

			// create the new user
			$user_id = wp_insert_user( $user_args );
			update_option( '_wsform_opt_daemon_id', $user_id );
		}
	}
	elseif( $forms_daemon < 1 )
	{
		// make forms daemon current user
		$user_id = get_current_user_id();
		update_option( '_wsform_opt_daemon_id', $user_id );
	}
}



add_action( 'wp_ajax_wsform_action', 'wsform_action' );
add_action( 'wp_ajax_nopriv_wsform_action', 'wsform_action' );

function wsform_action()
{
	global $wpdb; // this is how you get access to the database

	// get the function name
	$func = $_POST['func'];

	// if the function doesn't exist, echo no function
	if( ! function_exists( $func ) )
	{
		echo 'no function';
		wp_die();
	}

	// execute the function
	$func( $_POST, $_FILES );

    // echo 1;

	wp_die(); // this is required to terminate immediately and return a proper response
}



add_action( 'wp_ajax_wsform_action_obj', 'wsform_action_obj' );
add_action( 'wp_ajax_nopriv_wsform_action_obj', 'wsform_action_obj' );


function wsform_action_obj()
{
	// Security check of the wordpress nonce
    check_ajax_referer( 'wsform_ajax' );

    // Get the stuff from $_POST that we need
    $atts = wp_unslash( $_POST['atts'] );
    
	// get the class name
	$class = $atts['c'] == 'wsfs' ? 'WebbsitesFormSub' : 'WebbsitesForm';

	// get the function name
	$method = sanitize_key( $atts['m'] );

    // get the post/sub ID
    if( array_key_exists( 'i', $atts ) )
    {
        $the_id = $atts['i'];
    }
    else
    {
        $the_id = null;
    }


	// if the class exists ...
	if( class_exists( $class ) )
	{
        // New class
        $obj = new $class( $the_id );

        // Save the arguments in the object
        $obj->output_atts = $atts;

        // ... and if the method exists ...
        if( method_exists( $class, $method ) )
        {
            // ... execute the method
            $obj->$method( $atts );
        }
        else // If the method doesn't exist, throw an error
        {
            $obj = new WebbsitesForm();
            $obj->send_error( "The specified method '$method' doesn't exist." );
        }
    }
    else // If the class doesn't exist, throw an error
    {
        $obj = new WebbsitesForm();
        $obj->send_error( "The specified class '$class' doesn't exist." );
    }


	wp_die(); // this is required to terminate immediately and return a proper response
}


function ws_forms_throw_error( $err )
{
    $obj = new WebbsitesForm();
    $obj->send_error( $err );
}



// Delete uploads when submission is deleted
add_action( 'before_delete_post', [ 'WebbsitesForm', 'delete_post_subs' ], 10, 1 );
add_action( 'before_delete_post', [ 'WebbsitesForm', 'delete_uploads' ], 10, 1 );


// function wsform_delete_uploads( $postid ){

//     // We check if the global post type isn't ours and just return
//     global $post_type;
//     if ( $post_type != 'wsform_sub' ) return;

//     // My custom stuff for deleting my custom post type here
// 	if( ! $uploads = get_post_meta( $postid, '_wsform_attached_file' ) ) return;

// 	// Delete the uploads
// 	foreach( $uploads as $file )
// 	{
// 		$path = wsform_UPLOADS_DIR_PATH . '/' . $file;
// 		unlink( $path );
// 	}
// }
