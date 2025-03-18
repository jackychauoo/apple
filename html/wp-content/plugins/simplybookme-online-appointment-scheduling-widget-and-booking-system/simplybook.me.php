<?php
/*
Plugin Name: Online Appointment Scheduling Widget and Booking System
Plugin URI: https://simplybook.me/index/wp-plugin
Description: Easy to use online booking solution for you and your clients.
Version: 4.0.3
Author: Simplybook Inc.
Author URI: http://simplybook.me/
*/
session_start();

$simplybookDomain = 'simplybook_me';
$simplybookPluginDir =  plugin_basename(__DIR__);

define("SIMPLYBOOK_DIR",  WP_PLUGIN_DIR . '/' .$simplybookPluginDir);
define("SIMPLYBOOK_TPL_DIR",  SIMPLYBOOK_DIR . '/templates');
define("SIMPLYBOOK_URL",  plugins_url() . '/' .$simplybookPluginDir);

$simplybookCfg = simplybook_get_config();

function simplybook_get_config(){
	global $simplybookDomain;

	$defaultOptions = array(
		'login' => 'simplydemo',
		'server' => 'simplydemo.simplybook.me',
		'template' => 'default',
		'timeline_type' => 'modern',
		'datepicker_type' => 'top_calendar',
		'themeparams' => array(
			"main_page_mode" => "default"
		),
		'is_rtl' => 0,
	);

	//update_option($simplybookDomain, $defaultOptions); //uncomment if new option param
	$options = get_option($simplybookDomain);

	if(!$options){
		add_option($simplybookDomain, $defaultOptions); //set default value
		$options = $defaultOptions;
	}

	return array_merge($defaultOptions, $options);
}


function simplybook_admin(){  //if load wp-admin
    global $simplybookDomain, $simplybookCfg;
    add_options_page(sbGetText("Simplybook Plugin Settings"), sbGetText("Simplybook plugin"), 'manage_options', $simplybookDomain, 'simplybook_admin_page');
}

function simplybook_admin_page(){ //page content
	global $simplybookDomain, $simplybookPluginDir;
	//get transliatons
	$translations = get_translations_for_domain($simplybookDomain);
	$translationsKeys = array();
	foreach($translations->entries as $key=>$val){
		$translationsKeys[$key] = $val->translations[0];
	}

	wp_register_script( 'underscore', plugins_url($simplybookPluginDir. '/js/libs/underscore-min.js') );
	wp_register_script( $simplybookDomain.'_json_rpc', plugins_url($simplybookPluginDir. '/js/libs/json-rpc-client.js') );
	wp_register_script( $simplybookDomain.'_colpix', plugins_url($simplybookPluginDir. '/js/libs/colpick/js/colpick.js') );
	wp_register_script( $simplybookDomain.'_admin', plugins_url($simplybookPluginDir. '/js/admin.js') );
	wp_register_script( $simplybookDomain.'_translation', plugins_url($simplybookPluginDir.'/js/locale.js') );

	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('underscore', array('jquery'));
	wp_enqueue_script( $simplybookDomain.'_translation', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker') );
	wp_enqueue_script( $simplybookDomain.'_json_rpc', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'underscore') );
	wp_enqueue_script( $simplybookDomain.'_colpix', array($simplybookDomain.'_json_rpc') );
	wp_enqueue_script( $simplybookDomain.'_admin', array($simplybookDomain.'_json_rpc', $simplybookDomain.'_colpix') );

	wp_localize_script( $simplybookDomain.'_translation', 'simplybook_translation', $translationsKeys );

	wp_register_style($simplybookDomain.'colpick', plugins_url($simplybookPluginDir.'/js/libs/colpick/css/colpick.css'));
	wp_register_style($simplybookDomain.'sb-admin-page', plugins_url($simplybookPluginDir.'/css/admin.css'));

	wp_enqueue_style($simplybookDomain.'sb-admin-page' );
	wp_enqueue_style($simplybookDomain.'colpick' );

	simplybook_init_process('admin_page');
}


function simplybook_add_action_links ( $links ) { // add settings links in plugin list
    global $simplybookDomain;

    $mylinks = array(
        '<a href="' . admin_url( 'options-general.php?page='.$simplybookDomain ) . '">'.sbGetText("Settings").'</a>'
    );
    return array_merge( $links, $mylinks );
}

function simplybook_add_translations(){
    global $simplybookDomain, $simplybookPluginDir;

    if (function_exists('load_plugin_textdomain')) {
        $locale = get_locale();
        //print_r($locale);
        load_plugin_textdomain($simplybookDomain, false, './'.$simplybookPluginDir.'/langs/');
    }
}

function sbGetText($text){
    global $simplybookDomain;
    return __($text,$simplybookDomain);
}

function sb_show_text($text){
    global $simplybookDomain;
    echo __($text,$simplybookDomain);
}

function simplybook_run(){
    global $simplybookDomain, $simplybookCfg;
    simplybook_add_translations();
}


function simplybook_booking_content(){
	return simplybook_init_process('show');
}

function simplybook_init_process($do){
	global $simplybookDomain, $simplybookCfg, $simplybookPluginDir;

	$content = '';
	include SIMPLYBOOK_DIR . '/controller.php';
	return $content;
}

if (!function_exists('array_map_recursive')) {
	function array_map_recursive( $callback, $array ) {
		foreach ( $array as $key => $value ) {
			if ( is_array( $array[ $key ] ) ) {
				$array[ $key ] = array_map_recursive( $callback, $array[ $key ] );
			} else {
				$array[ $key ] = call_user_func( $callback, $array[ $key ] );
			}
		}
		return $array;
	}
}

function simplybook_save_log($file_name, $data, $append = true){
	$logDir = SIMPLYBOOK_DIR.'/logs/';

	if($append){
		file_put_contents($logDir.$file_name.'.txt', $data, FILE_APPEND | LOCK_EX );
	} else {
		file_put_contents($logDir.$file_name.'.txt', $data );
	}

}

function simplybook_clear_cache(){
	global $simplybookCfg;

	foreach($simplybookCfg['cached_methods'] as $method){
		delete_transient( 'simplybook_'.$method );
	}
}

function curl_load($url){
	curl_setopt($ch=curl_init(), CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
}

function sb_sanitize_html_output($buffer) {
	$search = array(
		'/\>[^\S ]+/s',     // strip whitespaces after tags, except space
		'/[^\S ]+\</s',     // strip whitespaces before tags, except space
		'/(\s)+/s',         // shorten multiple whitespace sequences
		'/<!--(.|\s)*?-->/' // Remove HTML comments
	);
	$replace = array('>','<','\\1',	'');
	$buffer = preg_replace($search, $replace, $buffer);
	return $buffer;
}

add_action('admin_menu', 'simplybook_admin');
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'simplybook_add_action_links' ); // add settings links in plugin list
add_action('init', 'simplybook_run'); // add translation
//add_action('the_content', 'simplybook_booking_content');
add_shortcode( 'simplybook', 'simplybook_booking_content' );

///API hook
/*add_action('wp_ajax_sb_api', 'simplybook_api_action');
add_action('wp_ajax_nopriv_sb_api', 'simplybook_api_action');*/




?>
