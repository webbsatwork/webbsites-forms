<?php session_start();

/**
 * @package Webbsites Forms
 *
 * Display Forms Script
 * 
 * version 1.0.0
 */


// load wordpress
define( 'WP_USE_THEMES', false );
$path = dirname( __FILE__ ) . '/../../../../wp-load.php';
require_once( $path );

// if $_GET not set, go to not-found
if( empty( $_GET ) ) header( 'location: ' . site_url( '/not-found' ) );

// if $_GET['form_id'] not set, go to not-found
if( ! key_exists( 'id', $_GET ) ) header( 'location: ' . site_url( '/not-found' ) );


// Output the HTML
?><!DOCTYPE html>

<html>
	
	<head>
		
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		
		<title>Webbsites Form Output</title>
		
	</head>
	
	<body>
		
		<?php // output the form
		wsform_display_sub( $_GET['id'] ) ?>
		
	</body>
	
</html>

