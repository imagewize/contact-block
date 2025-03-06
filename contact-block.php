<?php
/**
 * Plugin Name:       Contact Block
 * Description:       A customizable contact form block for WordPress.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Jasper Frumau
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       contact-block
 *
 * @package CreateBlock
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
function create_block_contact_block_block_init() {
	register_block_type( __DIR__ . '/build/contact-block' );
	
	// Enqueue script data
	add_action( 'wp_enqueue_scripts', 'contact_block_enqueue_view_script_data' );
	
	// Register AJAX handlers
	add_action( 'wp_ajax_contact_form_submission', 'contact_form_submission_handler' );
	add_action( 'wp_ajax_nopriv_contact_form_submission', 'contact_form_submission_handler' );
}

/**
 * Enqueue script data for the view script
 */
function contact_block_enqueue_view_script_data() {
	wp_localize_script(
		'create-block-contact-block-view-script',
		'contactFormData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'contact_form_nonce' ),
		)
	);
}

/**
 * Handle form submissions via AJAX
 */
function contact_form_submission_handler() {
	// Verify nonce (uncomment in production)
	// check_ajax_referer( 'contact_form_nonce', 'nonce' );
	
	$name = sanitize_text_field( $_POST['contact-name'] ?? '' );
	$email = sanitize_email( $_POST['contact-email'] ?? '' );
	$message = sanitize_textarea_field( $_POST['contact-message'] ?? '' );
	
	// Basic validation
	if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
		wp_send_json_error( array(
			'message' => __( 'All fields are required.', 'contact-block' ),
		) );
		return;
	}
	
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array(
			'message' => __( 'Please enter a valid email address.', 'contact-block' ),
		) );
		return;
	}
	
	// Get admin email as default recipient
	$to = get_option( 'admin_email' );
	$subject = sprintf( __( 'New Contact Form Submission from %s', 'contact-block' ), get_bloginfo( 'name' ) );
	
	$body = sprintf(
		/* translators: %1$s: Name, %2$s: Email, %3$s: Message */
		__( "Name: %1\$s\nEmail: %2\$s\nMessage: %3\$s", 'contact-block' ),
		$name,
		$email,
		$message
	);
	
	$headers = array( 'Reply-To: ' . $name . ' <' . $email . '>' );
	
	// Send email
	$sent = wp_mail( $to, $subject, $body, $headers );
	
	if ( $sent ) {
		wp_send_json_success( array(
			'message' => __( 'Thank you for your message. We\'ll get back to you soon!', 'contact-block' ),
		) );
	} else {
		wp_send_json_error( array(
			'message' => __( 'An error occurred while sending your message. Please try again.', 'contact-block' ),
		) );
	}
}

add_action( 'init', 'create_block_contact_block_block_init' );
