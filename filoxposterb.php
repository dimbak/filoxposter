<?php
/**
 * Plugin Name:       Filoxposterb
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Submit posts from the front end.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            DB
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       filoxposterb
 * Domain Path:       /languages
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
add_action( 'init', 'dbfilox_textdomain' );

function dbfilox_textdomain() {
	load_plugin_textdomain( 'filoxposterb', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}


add_action( 'wp_enqueue_scripts', 'dbfilox_icon_loading' );

function dbfilox_icon_loading() {
	if ( is_front_page() ) {
		wp_enqueue_script( 'dbfilox-javascript', plugin_dir_url( __FILE__ ). 'js/handleform.js', array('jquery'), '1.0.0', true );
		wp_localize_script( 'dbfilox-javascript', 'wpApiSettings', array( 'root' => esc_url_raw( rest_url() ), 'nonce' => wp_create_nonce( 'wp_rest' ) ) );
	}
}


add_shortcode( 'fix_post_shortcode', 'filo_new_post_form' );

function filo_new_post_form() {
    /**
     * @var     WP_User $current_user
     */
    $current_user = $GLOBALS['current_user'];

    ob_start(); 
	?>
<form action="#" method="POST" class="comment-form" id="form-new-post">
	<?php 
	
	wp_nonce_field( 'filo-nonce-value', 'filo-nonce' ); 
	
		if ( isset( $_GET['filo-message'] ) ) {
			$error = sanitize_title( $_GET['filo-message'] );
			error_log( $error) ;
			switch ( $error ) {
				case 'emptyfields' :
					$message = __( 'Please fill the fields.', 'filoxposterb' );
					error_log( $message) ;
					printf( 'fill the fields');
					break;

				case 'titleexists' :
					$message = __( 'This title exists', 'filoxposterb' );
					error_log( $message) ;
					printf('exists');
					break;
				case 1 :
					$message = __( 'Success!', 'filoxposterb');
					$message = $_GET['filo-newpost-id'];
					$message1 = get_preview_post_link( $_GET['filo-newpost-id'] );
					$post_array = get_post( $_GET['filo-newpost-id'], ARRAY_A );
					error_log( print_r( $post_array, 1 )) ;
					printf( '<div class="error"><p><a href=' .$message1. '>' . $post_array['post_title']. '</a></p></div>' );

					break;

				default :
					$message = __( 'Something went wrong.', 'msk' );
					break;
			}
					
			
		}
	?>
	<div class="form-group">
				<div>
					<label for="post_title" class="post-title-label"><?php _e( 'Please enter the post title', 'filoxposterb' ); ?></label>

					<div><input type="text" class="form-post-title" name="post_title"  id="post_title_id" /></div>
				</div>
				<div>
					<label for="post_content" class="post-content-label"><?php _e( 'Please enter the post content', 'filoxposterb' ); ?></label>
                	<div><textarea class="form-post-content" name="post_content_name" rows="5" cols="33"  id="post_content_id"></textarea></div>
				</div>
            </div>
			<input type="hidden" name="ID" value=""/>
            <input type="hidden" name="post_author" class="post_author1" value="<?php echo $current_user->ID; ?>"/>

	<input id="submit" type="submit" name="filo-submit-button-name" id="submit" class="submit" value="<?php esc_attr_e( 'Submit', 'filoxposterb' ); ?>" />
	<div class="login-link"><a href="<?php echo esc_url( wp_logout_url( get_permalink() ) ); ?>">Logout</a></div>
</form>
<?php $html = ob_get_clean();
    if (is_user_logged_in()) {

	return $html;
	} else { 
		echo "<a href='" . esc_url( wp_login_url( get_permalink() ) ). "'>" . __('Login First', 'filoxposterb') . "</a>"; 
	}
}

function handle_form() {
	
	if ( ! isset( $_POST['filo-submit-button-name'] ) || ! isset( $_POST['filo-nonce'] ) )  {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['filo-nonce'], 'filo-nonce-value' ) ) {
		return;
	}
	
	$clear_url = parse_url(wp_get_referer(), PHP_URL_PATH);

	
	$postarr = array(
		'ID'          => '', // If ID stays empty the post will be created.
		'post_author' => '',
		'post_title'  =>  'My test title',
		'post_status' => 'publish',
		'post_type'   => 'post',
    );

	if ( ! empty( $_POST['post_title'] ) ) {
		$filo_author  = $_POST['post_author'];
		$filo_title   = $_POST['post_title'];
		$filo_content = $_POST['post_content'];

		$post_data = array('post_author' => $filo_author, 'post_title'=>$filo_title, 'post_content'=>$filo_content, );
		$postarr =  array_merge( $postarr, $post_data );

		$args = array(
			'title' => $filo_title,
			'post_status' => 'draft',
		);

		// check if title exists
		$posts = get_posts($args);
		// if $posts count = 0 then title does not exist
		if ( count( $posts ) == 0 ) {
			$new_post = wp_insert_post( $postarr, true);
			$url = add_query_arg( array (
				'filo-message' => 1,
				'filo-newpost-id' => $new_post,
				)
			);	
		} else {
			$url = add_query_arg( 'filo-message', 'titleexists', $clear_url);		
		}
	} 
	else  {
		$url = add_query_arg( 'filo-message', 'emptyfields', $clear_url);
	} 
	wp_safe_redirect( $url);
	exit();
}

add_action( 'template_redirect', 'handle_form' );


add_action( 'rest_api_init', function() {
	
	// GET request
	register_rest_route( 'filoxposter/v1', 'check-post-title/(?P<postTitle>[a-zA-Z0-9-]+)', array(
		'methods'   => 'GET',
		'callback' => 'filoxposter_get_rest_route',
		)
	);

	// POST request
	register_rest_route( 'filoxposter/v1', 'create-post', [
		'methods'   => 'POST',
		'callback' => 'filoxposter_post_rest_route',
		],
	);
});
 
function filoxposter_get_rest_route( WP_REST_Request $request ) {

	$post_title =  sanitize_text_field( $request->get_param( 'postTitle' ) ) ;

	$args = array(
		'title' => $post_title,
		'post_status' => 'draft',
	);

	// get posts returns array of posts if title found
	$posts_array = get_posts( $args );


	// $posts_array is empty, title does not exist, send 200 response 
	if ( count( $posts_array ) == 0 ) {

		return new WP_REST_Response( [
			'message1' => 'Post title does not exist',
		], 200 );
	}

	return new WP_REST_Response( [
		'message1' => 'Post title exists',
	], 500 );


}
function filoxposter_post_rest_route( WP_REST_Request $request ) {

  	$parameters = $request->get_params();
	
	$postarr = array(
		'ID'          => '', // If ID stays empty the post will be created.
		'post_author' => '',
		'post_title'  =>  'My test title',
		'post_status' => 'draft',
		'post_type'   => 'post',
	);
	
	 $postarr  = array_merge( $postarr, $parameters);
	 $new_post = wp_insert_post( $postarr, true);
	 $post_id  = $new_post;
	
	//returns URL 
	$preview_link = get_preview_post_link( $post_id );

	// Gets post info 
	$post_array = get_post( $post_id, ARRAY_A );

	//error_log( print_r($parameters,1) );
	
	return new WP_REST_Response( $post_array, 200 );
}
