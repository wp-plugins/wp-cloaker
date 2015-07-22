<?php
/*
Plugin Name: WP Cloaker
plugin URI:http://www.wwgate.net
Description: WP Cloaker gives you the ability to shorten your affiliate ugly links and keep track of how many clicks on each link.
Version:1.0.0
Author: Fadi Ismail
Author URI: http://www.wwgate.net
*/

define('wp_cloaker_url', plugins_url('',__FILE__) );
define('wp_cloaker_version', '1.0.0' );

// if the file is called directly, abort
if(! defined('WPINC')){
	die();
}	
require_once(plugin_dir_path(__FILE__).'class-wp-cloaker.php');
require_once(plugin_dir_path(__FILE__).'class-wp-cloaker-clicks.php');
require_once(plugin_dir_path(__FILE__).'class-wp-cloaker-admin.php');
function wp_Cloaker_Start(){
	$wp_cloaker = new WP_Cloaker();
	$wp_cloaker->initialize();
	
	$wp_cloaker_clicks = new WP_Cloaker_Clicks();
	$wp_cloaker_clicks->initialize();
	
	$wp_cloaker_admin = new WP_Cloaker_Admin();
	$wp_cloaker_admin->initialize();
	//register_uninstall_hook(__FILE__, 'wp_cloaker_uninstall');
	register_activation_hook(__FILE__,array(&$wp_cloaker_clicks,'wp_cloaker_create_clicks_table'));
	register_activation_hook(__FILE__,array(&$wp_cloaker_clicks,'wp_cloaker_create_clicks_count_table'));
}
wp_Cloaker_Start();

