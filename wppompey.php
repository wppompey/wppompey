<?php 
/*
Plugin Name: wppompey 
Plugin URI: https://github.com/wppompey/wppompey
Description: WordPress Portsmouth Custom Post Types and Fields
Version: 0.2.0
Author: bobbingwide
Author URI: https://www.oik-plugins.com/author/bobbingwide
License: GPL2

    Copyright 2012-2019, 2025 Bobbing Wide (email : herb@bobbingwide.com )

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
	add_filter( 'register_post_type_args', 'wppompey_register_post_type_args', 100 );
	add_filter( 'render_block_core/post-title', 'wppompey_render_block_core_post_title', 10, 3 );
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
  $post_type_args['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'author', 'autosave', 'custom-fields' );
  $post_type_args['has_archive'] = true;
  $post_type_args['menu_icon'] = 'dashicons-admin-post';
	
	$post_type_args['taxonomies'] = array( "clinktype" );
	$post_type_args['show_in_rest'] = true;
  bw_register_post_type( $post_type, $post_type_args );
	
  bw_register_field( "_url", "url", "Website", true );
	bw_register_field_for_object_type( "_url", $post_type, true );
	
	wppompey_register_google_maps_fields( $post_type );
	
}

function wppompey_register_google_maps_fields( $post_type ) {
	
  bw_register_field( "_address", "textarea", "Address" ); 
	bw_register_field( "_post_code", "text", "Post Code" );
	bw_register_field( "_lat", "numeric", "Latitude", array( '#theme_null' => false, '#optional' => true ) );
	bw_register_field( "_long", "numeric", "Longitude", array( '#theme_null' => false, '#optional' => true ) );
	
	// Don't display this by default since the content may be nested
	//bw_register_field_for_object_type( "featured", $post_type );
	
	bw_register_field_for_object_type( "googlemap", $post_type, true );
	
	bw_register_field_for_object_type( "_address", $post_type, true );
	bw_register_field_for_object_type( "_post_code", $post_type, true );
	bw_register_field_for_object_type( "_lat", $post_type, true );
	bw_register_field_for_object_type( "_long", $post_type, true );

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
  $post_type_args['supports'] = array( 'title', 'editor', 'thumbnail', 'excerpt', 'home', 'publicize', 'author', 'revisions', 'custom-fields', 'autosave' );
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

/**
 * Implements register_post_type_args filter.
 *
 * This code doesn't do anything for now.
 * I'm not sure why I added this code;
 * but these comments are Feb 2025.
 * @param $args
 * @return mixed
 */
function wppompey_register_post_type_args( $args ) {
	//gob();
	return $args;
}

/**
 * Converts hyphens in post titles to br tags.
 *
 * @param $block_content
 * @param $parsed_block
 * @param $block
 * @return array|string|string[]
 */
function wppompey_render_block_core_post_title( $block_content, $parsed_block, $block ) {
	//bw_trace2( $block_content, 'block_content', false );
	//bw_trace2( $parsed_block, 'parsed_block', false);
	// WordPress Portsmouth Online Meetup &#8211; 19th February 2025 &#8211; Site and Block editor Show and Tell
	$block_content = str_replace(' &#8211; ', '<br />', $block_content );
	return $block_content;
}

wppompey_loaded();