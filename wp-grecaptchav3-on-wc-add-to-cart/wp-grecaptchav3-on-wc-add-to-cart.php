<?php

/**
 * Plugin Name: reCaptcha Google v3 on Add to cart
 * Description: Plugin dodający google recaptcha v3 dla dodawania produktu do koszyka
 * Version: 1.0.0
 * Author: Reszek
 * Author URI: https://github.com/reszekbartek
 * Text Domain: grev3atc
 * Domain Path: /languages
 */


add_action( 'init', function() {
		load_plugin_textdomain( 'grev3atc', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	});

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), function($settings) {
		$settings[] = '<a href="'. admin_url( 'admin.php?page=grev3atc' ) .'">'.__('Settings').'</a>';
		return $settings;
	});

require_once( plugin_dir_path( __FILE__ ) . 'wp-grecaptchav3-on-wc-add-to-cart-admin-options.php');

function woocommerce_add_to_cart_recaptcha_load_scripts(){
		
		$GOOGLE_RECAPTCHA_SITE_KEY = get_option('grev3atc_keys')['grev3atc_sitekey'];
		
		if ( is_shop() || is_product_category() || is_product_tag() || is_product() || is_cart() ) {
			
			wp_enqueue_script( 'recaptchav3', 'https://www.google.com/recaptcha/api.js?render='.$GOOGLE_RECAPTCHA_SITE_KEY);

			wp_register_script( 'wp-grecaptchav3-on-wc-add-to-cart', plugin_dir_url( __FILE__ ) . 'wp-grecaptchav3-on-wc-add-to-cart.js', array('jquery','recaptchav3'), filemtime( plugin_dir_url( __FILE__ ) . 'wp-grecaptchav3-on-wc-add-to-cart.js'), false  );
			
			wp_register_script( 'wp-grecaptchav3-on-wc-add-to-cart-on-cart-page', plugin_dir_url( __FILE__ ) . 'wp-grecaptchav3-on-wc-add-to-cart-on-cart-page.js', array('jquery','recaptchav3'), filemtime( plugin_dir_url( __FILE__ ) . 'wp-grecaptchav3-on-wc-add-to-cart-on-cart-page.js'), false  );
			
		}
		
		$jsarray = array(
				'sitekey' => $GOOGLE_RECAPTCHA_SITE_KEY
			);

			
		if(is_cart()){

				wp_localize_script( 'wp-grecaptchav3-on-wc-add-to-cart-on-cart-page', 'php_vars', $jsarray );
				wp_enqueue_script( 'wp-grecaptchav3-on-wc-add-to-cart-on-cart-page');
			} else if( is_shop() || is_product_category() || is_product_tag() || is_product() ) {
			

				wp_localize_script( 'wp-grecaptchav3-on-wc-add-to-cart', 'php_vars', $jsarray );
				wp_enqueue_script( 'wp-grecaptchav3-on-wc-add-to-cart');
			}
    }

add_action( 'wp_enqueue_scripts', 'woocommerce_add_to_cart_recaptcha_load_scripts', 9999 );

function woocommerce_add_to_cart_recaptcha_validation( $passed, $product_id, $quantity ) {

	if ( isset( WC()->session ) && WC()->session->get( 'captcha_validated' ) === true ) {
        return $passed;
    }

	$GOOGLE_RECAPTCHA_SITE_KEY  = get_option('grev3atc_keys')['grev3atc_sitekey'];
	$GOOGLE_RECAPTCHA_SECRET_KEY  = get_option('grev3atc_keys')['grev3atc_secretkey'];
	
	$reCaptchaToken = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
	
    if(!$reCaptchaToken){
			$passed = false;
			wc_add_notice( __( 'Error captcha (empty token).', 'grev3atc' ), 'error' );
		} else {
			$postArray = array(
				'secret' => $GOOGLE_RECAPTCHA_SECRET_KEY,
				'response' => $reCaptchaToken
			);

			$postJSON = http_build_query($postArray);

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postJSON);
			$response = curl_exec($curl);
			curl_close($curl);
			
			$curlResponseArray = json_decode($response, true);
			if ($curlResponseArray["success"] == true && !empty($curlResponseArray["action"]) && $curlResponseArray["score"] >= 0.5) {
					WC()->session->set( 'captcha_validated', true );	
					return $passed;
				} else {
					$passed = false;
					wc_add_notice( __( 'Error captcha (spam suspicion).', 'grev3atc' ), 'error' );
					return $passed;
				}
			
		}

}

add_filter( 'woocommerce_add_to_cart_validation', 'woocommerce_add_to_cart_recaptcha_validation', 9, 3 );

function reset_captcha_after_cart_update() {
		if ( isset( WC()->session ) ) {
				WC()->session->set( 'captcha_validated', false );
			}
	}
add_action( 'woocommerce_before_calculate_totals', 'reset_captcha_after_cart_update' );

?>