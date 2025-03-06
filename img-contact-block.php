<?php
/**
 * Plugin Name:       Imagewize Contact Form
 * Description:       A customizable contact form block for WordPress.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Jasper Frumau
 * Author URI:        https://imagewize.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       img-contact-block
 *
 * @package           Imagewize
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function imagewize_contact_block_init() {
	// Register the block using the block.json metadata from build directory
	register_block_type( __DIR__ . '/build/img-contact-block', array(
		'editor_script' => 'img-contact-block-editor-script',
		'editor_style'  => 'img-contact-block-editor-style',
		'style'         => 'img-contact-block-style',
		'render_callback' => 'imagewize_contact_block_render_callback',
	) );
	
	// Enqueue script data
	add_action( 'wp_enqueue_scripts', 'img_contact_block_enqueue_view_script_data' );
	
	// Register AJAX handlers
	add_action( 'wp_ajax_img_contact_form_submission', 'img_contact_form_submission_handler' );
	add_action( 'wp_ajax_nopriv_img_contact_form_submission', 'img_contact_form_submission_handler' );
}

/**
 * Render callback function
 */
function imagewize_contact_block_render_callback( $attributes, $content ) {
	// You can add server-side rendering code here if needed
	return $content;
}

/**
 * Enqueue script data for the view script
 */
function img_contact_block_enqueue_view_script_data() {
	wp_localize_script(
		'imagewize-img-contact-block-view-script',
		'imgContactFormData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'img_contact_form_nonce' ),
		)
	);
}

/**
 * Handle form submissions via AJAX
 */
function img_contact_form_submission_handler() {
	// Verify nonce (uncomment in production)
	// check_ajax_referer( 'img_contact_form_nonce', 'nonce' );
	
	$name = sanitize_text_field( $_POST['contact-name'] ?? '' );
	$email = sanitize_email( $_POST['contact-email'] ?? '' );
	$message = sanitize_textarea_field( $_POST['contact-message'] ?? '' );
	
	// Basic validation
	if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
		wp_send_json_error( array(
			'message' => __( 'All fields are required.', 'img-contact-block' ),
		) );
		return;
	}
	
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array(
			'message' => __( 'Please enter a valid email address.', 'img-contact-block' ),
		) );
		return;
	}
	
	// Get admin email as default recipient
	$to = get_option( 'admin_email' );
	$subject = sprintf( __( 'New Contact Form Submission from %s', 'img-contact-block' ), get_bloginfo( 'name' ) );
	
	$body = sprintf(
		/* translators: %1$s: Name, %2$s: Email, %3$s: Message */
		__( "Name: %1\$s\nEmail: %2\$s\nMessage: %3\$s", 'img-contact-block' ),
		$name,
		$email,
		$message
	);
	
	$headers = array( 'Reply-To: ' . $name . ' <' . $email . '>' );
	
	// Send email
	$sent = wp_mail( $to, $subject, $body, $headers );
	
	if ( $sent ) {
		wp_send_json_success( array(
			'message' => __( 'Thank you for your message. We\'ll get back to you soon!', 'img-contact-block' ),
		) );
	} else {
		wp_send_json_error( array(
			'message' => __( 'An error occurred while sending your message. Please try again.', 'img-contact-block' ),
		) );
	}
}

add_action( 'init', 'imagewize_contact_block_init' );