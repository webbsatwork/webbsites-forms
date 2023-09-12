<?php
/**
 * @package Webbsites Forms
 * version 0.0.1
 */

/*
Plugin Name:    Webbsites Forms
Plugin URI:     http://webbsites.net/
Description:    Function for custom forms and predefined forms.
Author:         Bill Webb
Version:        0.0.1
Author URI:     http://webbsites.net/
*/

// allow verify_nonce to work
require_once ( ABSPATH .'wp-includes/pluggable.php' );

// Add the setup
require_once( 'lib/php/setup.php');

// Add the post type
require_once( 'lib/php/post-type.php');

// add the objects
require_once( 'lib/php/object-form.php');
// require_once( 'lib/php/object-form-admin.php');
require_once( 'lib/php/object-sub.php');
require_once( 'lib/php/SimpleXLSXGen.php');

// add the functions
require_once( 'lib/php/admin.php' );
require_once( 'lib/php/funcs.php' );
require_once( 'lib/php/menus.php' );

// add the plugin options menu item
require_once( 'lib/pages/options.php');
