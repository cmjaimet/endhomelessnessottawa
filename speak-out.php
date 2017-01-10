<?php
/**
* Plugin Name: Speak Out
* Description: Lets readers send letters to their government representatives.
* Author: Charles Jaimet
* Contributors: Jennifer Poohachoff, Ben Dick, Amir Ammar
* Version: 1.0
* Requires at least: 3.0
* Tested up to: 4.6
*
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DF_LETTER_URI', plugins_url( '', __FILE__ ) . '/' );

require_once( 'classes/DatafestSpeakOut.php' );
