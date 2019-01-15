<?php 
/*
Plugin Name: wppompey 
Plugin URI: https://github.com/wppompey/wppompey
Description: WordPress Portsmouth Custom Post Types and Fields
Version: 0.1.0
Author: bobbingwide
Author URI: https://www.oik-plugins.com/author/bobbingwide
License: GPL2

    Copyright 2012-2019 Bobbing Wide (email : herb@bobbingwide.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/


/**
 * Implement "oik_admin_menu" action
 */
function wppompey_admin_menu() {
  oik_register_plugin_server( __FILE__ );
	oik_require( "admin/wppompey.php", "wppompey" );
	wppompey_lazy_admin_menu();
}

/**
 * Function to run when wppompey loaded
 */
function wppompey_loaded() {
	add_action( "oik_admin_menu", "wppompey_admin_menu" );
	add_action( "oik_fields_loaded", "wppompey_oik_fields_loaded" );
}


/**
 * Implement "oik_fields_loaded" for wppompey 
 */
function wppompey_oik_fields_loaded() {
 
	wppompey_register_categories();
	wppompey_register_post_types();
}

/**
 * Register custom taxonomies for WP-Pompey
 *
 */
function wppompey_register_categories() {
  bw_register_custom_category( "clinktype", null, "Clink type" );
}

/** 
 * Register the custom post types for WP-Pompey
 */
function wppompey_register_post_types() {
	wppompey_register_clink();
	wppompey_register_meetup();
}

/**
 * Register a Custom link 
 * 
 * Based on the Course CPT from the bobbingwide/tags plugin
 *
 * Fields:
 * - website URL 
 * - Address: street, additional, city, province, post code
 * - latitude & longitude
 */ 
function wppompey_register_clink() {
	$post_type = "clink";
  $post_type_args = array();
  $post_type_args['label'] = 'Clinks';
	$post_type_args['singular_label'] = 'Clink';
  $post_type_args['description'] = 'Custom links';
  $post_type_args['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'author' );
  $post_type_args['has_archive'] = true;
  $post_type_args['menu_icon'] = 'dashicons-admin-post';
	
	$post_type_args['taxonomies'] = array( "clinktype" );
	$post_type_args['show_in_rest'] = true;
  bw_register_post_type( $post_type, $post_type_args );
	
  bw_register_field( "_url", "url", "Website" ); 
	bw_register_field_for_object_type( "_url", $post_type );
	
	wppompey_register_google_maps_fields( $post_type );
	
}

function wppompey_register_google_maps_fields( $post_type ) {
	
  bw_register_field( "_address", "textarea", "Address" ); 
	bw_register_field( "_post_code", "text", "Post Code" );
	bw_register_field( "_lat", "numeric", "Latitude", array( '#theme_null' => false, '#optional' => true ) );
	bw_register_field( "_long", "numeric", "Longitude", array( '#theme_null' => false, '#optional' => true ) );
	
	// Don't display this by default since the content may be nested
	//bw_register_field_for_object_type( "featured", $post_type );
	
	bw_register_field_for_object_type( "googlemap", $post_type );
	
	bw_register_field_for_object_type( "_address", $post_type );
	bw_register_field_for_object_type( "_post_code", $post_type );
	bw_register_field_for_object_type( "_lat", $post_type );
	bw_register_field_for_object_type( "_long", $post_type );

}

/**
 * Register a Meetup
 * 
 */
function wppompey_register_meetup() { 
	$post_type = "meetup";
  $post_type_args = array();
  $post_type_args['label'] = 'Meetups';
  $post_type_args['description'] = 'Meetups';
  $post_type_args['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt', 'home', 'publicize', 'author', 'revisions' );
  $post_type_args['has_archive'] = true;
  $post_type_args['menu_icon'] = 'dashicons-flag';
  $post_type_args['show_in_rest'] = true;
  bw_register_post_type( $post_type, $post_type_args );
	
	bw_register_field( "_date", "date", "Date" );
	bw_register_field( "_time", "time", "Start time", array( '#theme_null' => false ) );
  bw_register_field( "_meetup", "url", "Meetup" );
	// 
	// Attendees? Expected and actual
	//  
	
	bw_register_field_for_object_type( "_date", $post_type );
	bw_register_field_for_object_type( "_time", $post_type );
	bw_register_field_for_object_type( "_meetup", $post_type );
	
	wppompey_register_google_maps_fields( $post_type );
	
}



wppompey_loaded();

