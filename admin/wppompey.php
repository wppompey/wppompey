<?php // (C) Copyright Bobbing Wide 2017

/**
 * Display the WP-Pompey admin menu
 * 
 * - There isn't a menu 
 * - But we do have other actions to perform
 */
function wppompey_lazy_admin_menu() {
	add_action( "save_post_clink", "wppompey_save_post_clink", 10, 2 );
	add_action( "save_post_meetup", "wppompey_save_post_clink", 10, 2 );
}


/**
 * Implement "save_post_clink" for clink and meetup
 *
 * Here we attempt to set the _lat and _long fields if they're null and the _address and/or _post_code are set.
 *
 * Example data:
 * `
     [_address] => (string) "28, Ballantrae Road,,Liverpool,LAN"
    [_post_code] => (string) "L18 6JQ"
    [_lat] => (string) null
    [_long] => (string) null
	 `
 *
 * @param ID $post_id The ID of the post being saved
 * @param object $post the post object
 */
function wppompey_save_post_clink( $post_id, $post ) {
	bw_trace2( $_POST, "_POST", true, BW_TRACE_DEBUG );
	oik_require( "admin/oik-admin.php" );
	$input['postal-code'] = bw_array_get( $_POST, "_post_code", null );
	$input['extended-address'] = bw_array_get( $_POST, "_address", null );
	if ( $input['postal-code'] || $input['extended-address'] ) { 
		$input['lat'] = bw_array_get( $_POST, "_lat", false );
		$input['long'] = bw_array_get( $_POST, "_long", false );
		$input = oik_set_latlng( $input );
		bw_trace2( $input, "input", false, BW_TRACE_VERBOSE );
		$_POST['_lat'] = $input['lat'];
		$_POST['_long'] = $input['long'];
	}
}

