<?php
/**
 * Plugin Name: Instant Countdown Banner
 * Description: A lightweight countdown banner for promotions and deadlines — now with live preview, color picker, and URL targeting.
 * Version: 1.2.0
 * Author: WPezo
 * Author URI: https://www.wpezo.com
 * Plugin URI: https://www.wpezo.com
 * Text Domain: instant-countdown-banner
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License: GPLv2 or later
 *
 * @package Instant_Countdown_Banner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ICB_VERSION', '1.2.0' );
define( 'ICB_PLUGIN_FILE', __FILE__ );
define( 'ICB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ICB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ICB_BRAND_NAME', 'WPezo' );
define( 'ICB_BRAND_URL', 'https://www.wpezo.com' );

/**
 * Load textdomain for translations.
 */
function icb_load_textdomain() {
	load_plugin_textdomain( 'instant-countdown-banner', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'icb_load_textdomain' );

/**
 * Default options.
 */
function icb_default_options() {
	return array(
		'enabled'            => 0,
		'end_timestamp'      => 0, // UTC timestamp (int).
		'message_before'     => __( 'Offer ends in {time}', 'instant-countdown-banner' ),
		'message_after'      => __( 'Offer ended', 'instant-countdown-banner' ),
		'position'           => 'top', // top|bottom.
		'sticky'             => 1,
		'body_offset'        => 1, // Add body margin offset when sticky.
		'bg_color'           => '#111827',
		'text_color'         => '#ffffff',
		'accent_color'       => '#f59e0b',
		'cta_label'          => '',
		'cta_url'            => '',
		'dismissible'        => 1,
		'dismiss_days'       => 7,
		'show_after_expire'  => 0,
		'hide_for_logged_in' => 0,
		'target_mode'        => 'everywhere', // Allowed values: everywhere, include, exclude.
		'target_patterns'    => '', // One per line. Wildcards * supported. Path or full URLs.
	);
}

/**
 * Get merged options.
 */
function icb_get_options() {
	$opts = get_option( 'icb_options', array() );
	return wp_parse_args( is_array( $opts ) ? $opts : array(), icb_default_options() );
}

/**
 * Activation: set defaults if not present.
 */
function icb_activate() {
	if ( false === get_option( 'icb_options', false ) ) {
		add_option( 'icb_options', icb_default_options() );
	}
}
register_activation_hook( __FILE__, 'icb_activate' );

/**
 * Admin UI.
 */
require_once ICB_PLUGIN_DIR . 'includes/admin-page.php';

/**
 * Helper: check if current request matches a list of patterns.
 *
 * @param string $patterns Multiline string, one pattern per line. * wildcard supported.
 * @return bool
 */
function icb_request_matches( $patterns ) {
	$patterns = is_string( $patterns ) ? $patterns : '';
	$patterns = array_filter( array_map( 'trim', preg_split( "/\r\n|\n|\r/", $patterns ) ) );
	if ( empty( $patterns ) ) {
		return false;
	}

	$home = home_url();
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
	$url  = ( is_ssl() ? 'https://' : 'http://' ) . $host . $uri; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$path = wp_parse_url( $url, PHP_URL_PATH );

	foreach ( $patterns as $p ) {
		$needle = $p;
		if ( empty( $needle ) ) {
			continue;
		}
		// Convert wildcard to regex safely.
		$quoted = str_replace( '\*', '.*', preg_quote( $needle, '/' ) );
		$regex  = '/^' . $quoted . '$/i';

		// Check against full URL and path and home+path combinations.
		if ( preg_match( $regex, $url ) ) {
			return true;
		}
		if ( preg_match( $regex, $path ) ) {
			return true;
		}
		$home_path = wp_parse_url( $home, PHP_URL_PATH );
		$full      = trailingslashit( untrailingslashit( $home ) ) . ltrim( $path, '/' );
		if ( preg_match( $regex, $full ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Decide whether the banner should render on this request according to targeting.
 *
 * @param array $opts Options.
 * @return bool
 */
function icb_should_render( $opts ) {
	if ( empty( $opts['enabled'] ) ) {
		return false;
	}

	// Hide for logged-in users if requested.
	if ( ! empty( $opts['hide_for_logged_in'] ) && is_user_logged_in() ) {
		return false;
	}

	$mode     = isset( $opts['target_mode'] ) ? $opts['target_mode'] : 'everywhere';
	$patterns = isset( $opts['target_patterns'] ) ? $opts['target_patterns'] : '';

	if ( 'everywhere' === $mode ) {
		return true;
	}

	$matches = icb_request_matches( $patterns );

	if ( 'include' === $mode ) {
		return (bool) $matches;
	}

	if ( 'exclude' === $mode ) {
		return ! $matches;
	}

	return true;
}

/**
 * Frontend enqueue and render.
 */
function icb_enqueue_frontend() {
	$opts = icb_get_options();

	if ( ! icb_should_render( $opts ) ) {
		return;
	}

	$script = file_exists( ICB_PLUGIN_DIR . 'assets/js/instant-countdown-banner.min.js' ) ? 'instant-countdown-banner.min.js' : 'instant-countdown-banner.js';
	$style  = file_exists( ICB_PLUGIN_DIR . 'assets/css/instant-countdown-banner.min.css' ) ? 'instant-countdown-banner.min.css' : 'instant-countdown-banner.css';

	wp_enqueue_style( 'icb-style', ICB_PLUGIN_URL . 'assets/css/' . $style, array(), ICB_VERSION );
	wp_enqueue_script( 'icb-script', ICB_PLUGIN_URL . 'assets/js/' . $script, array(), ICB_VERSION, true );

	$data = array(
		'endTimestamp'    => absint( $opts['end_timestamp'] ),
		'messageBefore'   => (string) $opts['message_before'],
		'messageAfter'    => (string) $opts['message_after'],
		'position'        => in_array( $opts['position'], array( 'top', 'bottom' ), true ) ? $opts['position'] : 'top',
		'sticky'          => ! empty( $opts['sticky'] ),
		'bodyOffset'      => ! empty( $opts['body_offset'] ),
		'bgColor'         => (string) $opts['bg_color'],
		'textColor'       => (string) $opts['text_color'],
		'accentColor'     => (string) $opts['accent_color'],
		'ctaLabel'        => (string) $opts['cta_label'],
		'ctaUrl'          => esc_url_raw( $opts['cta_url'] ),
		'dismissible'     => ! empty( $opts['dismissible'] ),
		'dismissDays'     => max( 1, absint( $opts['dismiss_days'] ) ),
		'showAfterExpire' => ! empty( $opts['show_after_expire'] ),
		'cookieKey'       => 'icb_dismissed',
		'now'             => time(),
		'brand'           => ICB_BRAND_NAME,
	);

	wp_localize_script( 'icb-script', 'ICB_DATA', $data );
}
add_action( 'wp_enqueue_scripts', 'icb_enqueue_frontend' );

/**
 * Auto-render container in footer.
 */
function icb_render_banner_container() {
	$opts = icb_get_options();
	if ( ! icb_should_render( $opts ) ) {
		return;
	}

	echo '<div id="icb-banner-root" class="icb-hidden" aria-live="polite" data-brand="' . esc_attr( ICB_BRAND_NAME ) . '"></div>';
}
add_action( 'wp_footer', 'icb_render_banner_container' );

/**
 * Shortcode: [instant_countdown_banner]
 *
 * Outputs a banner placeholder wherever used. It still follows global settings.
 */
function icb_shortcode() {
	ob_start();
	echo '<div class="icb-shortcode"><div class="icb-banner" data-icb-shortcode="1"></div></div>';
	return ob_get_clean();
}
add_shortcode( 'instant_countdown_banner', 'icb_shortcode' );
