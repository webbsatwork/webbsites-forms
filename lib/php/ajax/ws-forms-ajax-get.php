<?php

// Kick out anyone who doesn't belong
if( $_SERVER['HTTP_SEC_FETCH_SITE'] != 'same-origin' ) die();
if( empty( $_GET ) ) die();

// load wordpress db
define( 'WP_USE_THEMES', false );
$path = __DIR__ . '/../../../../../../wp-load.php';
require_once( $path );

// Load function files
require_once( __DIR__ . '/../../../webbsites-forms.php' );

// print_array( $_GET );
// return;

// Security check of the wordpress nonce
// check_ajax_referer( 'wsform_edit' );

// get the class name
$class = $_GET['c'] == 'wsfs' ? 'WebbsitesFormSub' : 'WebbsitesForm';

// get the function name
$method = sanitize_key( $_GET['m'] );

// get the post/sub ID
if( array_key_exists( 'i', $_GET ) )
{
    $the_id = $_GET['i'];
}
else
{
    $obj = new WebbsitesForm();
    $obj->send_error( "A form ID was not sent" );
}


// if the class exists ...
if( class_exists( $class ) )
{
    // New class
    $obj = new $class( $the_id );

    // Save the arguments in the object
    $obj->output_atts = $_GET;

    // ... and if the method exists ...
    if( method_exists( $class, $method ) )
    {
        // ... execute the method
        $obj->$method( $_GET );
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
