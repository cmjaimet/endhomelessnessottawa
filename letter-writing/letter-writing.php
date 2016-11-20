<?php
/**
* Plugin Name: Volunteering
* Description: 
* Author: Charles Jaimet
* Version: 0.1
* Requires at least: 3.0
* Tested up to: 4.6
*
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DF_LETTER_URI', plugins_url( '', __FILE__ ) . '/' );

require_once( 'classes/letter-writing.php' );
