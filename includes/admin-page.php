<?php
/**
 * Admin page for Instant Countdown Banner.
 *
 * @package Instant_Countdown_Banner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register settings page.
 */
function icb_register_settings() {
	register_setting( 'icb_options_group', 'icb_options', 'icb_sanitize_options' );

	add_options_page(
		__( 'Instant Countdown Banner', 'instant-countdown-banner' ),
		__( 'Instant Countdown Banner', 'instant-countdown-banner' ),
		'manage_options',
		'icb-settings',
		'icb_render_settings_page'
	);
}
add_action( 'admin_menu', 'icb_register_settings' );

/**
 * Enqueue admin assets (color picker + live preview).
 *
 * @param string $hook Hook suffix.
 */
function icb_admin_enqueue( $hook ) {
	if ( 'settings_page_icb-settings' !== $hook ) {
		return;
	}
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'icb-admin', ICB_PLUGIN_URL . 'assets/admin/css/admin.css', array(), ICB_VERSION );
	wp_enqueue_script( 'icb-admin', ICB_PLUGIN_URL . 'assets/admin/js/admin.js', array( 'jquery', 'wp-color-picker' ), ICB_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'icb_admin_enqueue' );

/**
 * Sanitize options.
 *
 * @param array $input Raw input.
 * @return array
 */
function icb_sanitize_options( $input ) {
	$defaults = icb_default_options();
	$out      = array();

	$out['enabled']            = empty( $input['enabled'] ) ? 0 : 1;
	$out['position']           = ( isset( $input['position'] ) && in_array( $input['position'], array( 'top', 'bottom' ), true ) ) ? $input['position'] : $defaults['position'];
	$out['sticky']             = empty( $input['sticky'] ) ? 0 : 1;
	$out['body_offset']        = empty( $input['body_offset'] ) ? 0 : 1;
	$out['bg_color']           = sanitize_hex_color( $input['bg_color'] ?? $defaults['bg_color'] );
	$out['text_color']         = sanitize_hex_color( $input['text_color'] ?? $defaults['text_color'] );
	$out['accent_color']       = sanitize_hex_color( $input['accent_color'] ?? $defaults['accent_color'] );
	$out['message_before']     = sanitize_text_field( $input['message_before'] ?? $defaults['message_before'] );
	$out['message_after']      = sanitize_text_field( $input['message_after'] ?? $defaults['message_after'] );
	$out['cta_label']          = sanitize_text_field( $input['cta_label'] ?? '' );
	$out['cta_url']            = esc_url_raw( $input['cta_url'] ?? '' );
	$out['dismissible']        = empty( $input['dismissible'] ) ? 0 : 1;
	$out['dismiss_days']       = max( 1, absint( $input['dismiss_days'] ?? 7 ) );
	$out['show_after_expire']  = empty( $input['show_after_expire'] ) ? 0 : 1;
	$out['hide_for_logged_in'] = empty( $input['hide_for_logged_in'] ) ? 0 : 1;

	// Targeting.
	$mode               = isset( $input['target_mode'] ) ? $input['target_mode'] : 'everywhere';
	$out['target_mode'] = in_array( $mode, array( 'everywhere', 'include', 'exclude' ), true ) ? $mode : 'everywhere';
	$patterns           = isset( $input['target_patterns'] ) ? (string) $input['target_patterns'] : '';
	// Normalize line endings and trim lines.
	$lines                  = array_map( 'trim', preg_split( "/\r\n|\n|\r/", $patterns ) );
	$out['target_patterns'] = implode( "\n", array_filter( $lines ) );

	// Date/time handling: expect local site time via datetime-local input.
	if ( ! empty( $input['end_datetime'] ) ) {
		$dt_local = sanitize_text_field( $input['end_datetime'] );
		try {
			$tz                   = wp_timezone();
			$dt                   = new DateTime( $dt_local, $tz );
			$out['end_timestamp'] = $dt->getTimestamp();
		} catch ( Exception $e ) {
			$out['end_timestamp'] = 0;
		}
	} else {
		$out['end_timestamp'] = absint( $input['end_timestamp'] ?? 0 );
	}

	return wp_parse_args( $out, $defaults );
}

/**
 * Render settings page with live preview.
 */
function icb_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$opts = icb_get_options();

	// Build local datetime for input value.
	$end_local = '';
	if ( ! empty( $opts['end_timestamp'] ) ) {
		$tz = wp_timezone();
		$dt = new DateTime( '@' . $opts['end_timestamp'] );
		$dt->setTimezone( $tz );
		$end_local = esc_attr( $dt->format( 'Y-m-d\TH:i' ) );
	}
	?>
	<div class="wrap icb-wrap">
		<h1><?php esc_html_e( 'Instant Countdown Banner', 'instant-countdown-banner' ); ?></h1>
		<p class="description">
			<strong><?php echo esc_html( ICB_BRAND_NAME ); ?></strong> •
			<a href="<?php echo esc_url( ICB_BRAND_URL ); ?>" target="_blank" rel="noopener">wpezo.com</a>
		</p>
		<div class="icb-grid">
			<div class="icb-col icb-col-form">
				<form method="post" action="options.php" id="icb-settings-form">
					<?php settings_fields( 'icb_options_group' ); ?>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><?php esc_html_e( 'Enable banner', 'instant-countdown-banner' ); ?></th>
							<td><label><input type="checkbox" name="icb_options[enabled]" value="1" <?php checked( ! empty( $opts['enabled'] ) ); ?>> <?php esc_html_e( 'Show the banner sitewide', 'instant-countdown-banner' ); ?></label></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Deadline', 'instant-countdown-banner' ); ?></th>
							<td>
								<input type="datetime-local" name="icb_options[end_datetime]" value="<?php echo esc_attr( $end_local ); ?>" />
								<p class="description"><?php esc_html_e( 'Uses the site timezone (Settings → General).', 'instant-countdown-banner' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Message before deadline', 'instant-countdown-banner' ); ?></th>
							<td>
								<input type="text" class="regular-text icb-field" data-icb-field="message_before" name="icb_options[message_before]" value="<?php echo esc_attr( $opts['message_before'] ); ?>" />
								<p class="description"><?php esc_html_e( 'Use {time} as placeholder (e.g., "Ends in {time}").', 'instant-countdown-banner' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Message after deadline', 'instant-countdown-banner' ); ?></th>
							<td><input type="text" class="regular-text icb-field" data-icb-field="message_after" name="icb_options[message_after]" value="<?php echo esc_attr( $opts['message_after'] ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Position', 'instant-countdown-banner' ); ?></th>
							<td>
								<select name="icb_options[position]" class="icb-field" data-icb-field="position">
									<option value="top" <?php selected( $opts['position'], 'top' ); ?>><?php esc_html_e( 'Top', 'instant-countdown-banner' ); ?></option>
									<option value="bottom" <?php selected( $opts['position'], 'bottom' ); ?>><?php esc_html_e( 'Bottom', 'instant-countdown-banner' ); ?></option>
								</select>
								<label style="margin-left:12px"><input type="checkbox" class="icb-field" data-icb-field="sticky" name="icb_options[sticky]" value="1" <?php checked( ! empty( $opts['sticky'] ) ); ?>> <?php esc_html_e( 'Sticky', 'instant-countdown-banner' ); ?></label>
								<label style="margin-left:12px"><input type="checkbox" class="icb-field" data-icb-field="body_offset" name="icb_options[body_offset]" value="1" <?php checked( ! empty( $opts['body_offset'] ) ); ?>> <?php esc_html_e( 'Add body offset when sticky', 'instant-countdown-banner' ); ?></label>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Colors', 'instant-countdown-banner' ); ?></th>
							<td>
								<label><?php esc_html_e( 'Background', 'instant-countdown-banner' ); ?> <input type="text" class="icb-color" data-default-color="#111827" name="icb_options[bg_color]" value="<?php echo esc_attr( $opts['bg_color'] ); ?>" /></label><br>
								<label><?php esc_html_e( 'Text', 'instant-countdown-banner' ); ?> <input type="text" class="icb-color" data-default-color="#ffffff" name="icb_options[text_color]" value="<?php echo esc_attr( $opts['text_color'] ); ?>" /></label><br>
								<label><?php esc_html_e( 'Accent', 'instant-countdown-banner' ); ?> <input type="text" class="icb-color" data-default-color="#f59e0b" name="icb_options[accent_color]" value="<?php echo esc_attr( $opts['accent_color'] ); ?>" /></label>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'CTA (optional)', 'instant-countdown-banner' ); ?></th>
							<td>
								<label><?php esc_html_e( 'Button label', 'instant-countdown-banner' ); ?> <input type="text" class="regular-text icb-field" data-icb-field="cta_label" name="icb_options[cta_label]" value="<?php echo esc_attr( $opts['cta_label'] ); ?>" /></label><br>
								<label><?php esc_html_e( 'Button URL', 'instant-countdown-banner' ); ?> <input type="url" class="regular-text icb-field" data-icb-field="cta_url" name="icb_options[cta_url]" value="<?php echo esc_attr( $opts['cta_url'] ); ?>" placeholder="https://..." /></label>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Behavior', 'instant-countdown-banner' ); ?></th>
							<td>
								<label><input type="checkbox" class="icb-field" data-icb-field="dismissible" name="icb_options[dismissible]" value="1" <?php checked( ! empty( $opts['dismissible'] ) ); ?>> <?php esc_html_e( 'Allow dismiss', 'instant-countdown-banner' ); ?></label><br>
								<label><?php esc_html_e( 'Silence days after dismiss', 'instant-countdown-banner' ); ?> <input type="number" min="1" step="1" class="icb-field" data-icb-field="dismiss_days" name="icb_options[dismiss_days]" value="<?php echo esc_attr( $opts['dismiss_days'] ); ?>" style="width:80px" /></label><br>
								<label><input type="checkbox" class="icb-field" data-icb-field="show_after_expire" name="icb_options[show_after_expire]" value="1" <?php checked( ! empty( $opts['show_after_expire'] ) ); ?>> <?php esc_html_e( 'Show message after deadline', 'instant-countdown-banner' ); ?></label><br>
								<label><input type="checkbox" class="icb-field" data-icb-field="hide_for_logged_in" name="icb_options[hide_for_logged_in]" value="1" <?php checked( ! empty( $opts['hide_for_logged_in'] ) ); ?>> <?php esc_html_e( 'Hide for logged-in users', 'instant-countdown-banner' ); ?></label>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Targeting', 'instant-countdown-banner' ); ?></th>
							<td>
								<select name="icb_options[target_mode]" class="icb-field" data-icb-field="target_mode">
									<option value="everywhere" <?php selected( $opts['target_mode'], 'everywhere' ); ?>><?php esc_html_e( 'Everywhere', 'instant-countdown-banner' ); ?></option>
									<option value="include" <?php selected( $opts['target_mode'], 'include' ); ?>><?php esc_html_e( 'Only on URLs matching patterns', 'instant-countdown-banner' ); ?></option>
									<option value="exclude" <?php selected( $opts['target_mode'], 'exclude' ); ?>><?php esc_html_e( 'Everywhere except URLs matching patterns', 'instant-countdown-banner' ); ?></option>
								</select>
								<p><textarea name="icb_options[target_patterns]" class="large-text code icb-field" data-icb-field="target_patterns" rows="6" placeholder="/sale/*&#10;https://example.com/black-friday&#10;/category/*"><?php echo esc_textarea( $opts['target_patterns'] ); ?></textarea></p>
								<p class="description"><?php esc_html_e( 'One pattern per line. Wildcards (*) supported. You can use full URLs or paths (e.g., /sale/*).', 'instant-countdown-banner' ); ?></p>
							</td>
						</tr>
					</table>
					<?php submit_button(); ?>
				</form>
				<p>
					<button class="button" id="icb-copy-shortcode" type="button" data-shortcode="[instant_countdown_banner]"><?php esc_html_e( 'Copy shortcode', 'instant-countdown-banner' ); ?></button>
					<span class="description" id="icb-copy-msg" style="margin-left:8px;"></span>
				</p>
			</div>
			<div class="icb-col icb-col-preview">
				<h2><?php esc_html_e( 'Live preview', 'instant-countdown-banner' ); ?></h2>
				<div id="icb-preview-frame">
					<div class="icb-site-sim">
						<div id="icb-preview-root" class="icb-preview-root"></div>
						<div class="icb-site-content">
							<h3><?php esc_html_e( 'Site preview', 'instant-countdown-banner' ); ?></h3>
							<p><?php esc_html_e( 'This simulates a site UI to preview your banner in real-time before saving.', 'instant-countdown-banner' ); ?></p>
						</div>
					</div>
				</div>
				<p class="description"><?php esc_html_e( 'Changes update in real-time. Remember to save to apply them sitewide.', 'instant-countdown-banner' ); ?></p>
			</div>
		</div>
	</div>
	<?php
}
