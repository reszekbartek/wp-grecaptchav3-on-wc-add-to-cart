<?php

function grev3atc_settings_init() {

	register_setting( 'grev3atc_option_group', 'grev3atc_keys' );


	add_settings_section(
		'grev3atc_settings_section',
		__( 'Your google recaptcha v3 for "Add to cart" - settings', 'grev3atc' ), 'grev3atc_section_callback',
		'grev3atc'
	);

	
	add_settings_field(
		'grev3atc_sitekey',
		__( 'Google SiteKey', 'grev3atc' ),
		'grev3atc_sitekey_field_callback',
		'grev3atc',
		'grev3atc_settings_section',
		array(
			'label_for'         => 'grev3atc_sitekey',
			'class'             => 'regular-text',
		)
	);


	add_settings_field(
		'grev3atc_secretkey',
		__( 'Google SecretKey', 'grev3atc' ), 
        'grev3atc_secretkey_field_callback',
		'grev3atc',
		'grev3atc_settings_section',
        array(
			'label_for'         => 'grev3atc_secretkey',
			'class'             => 'regular-text',
		)
	);
}


add_action( 'admin_init', 'grev3atc_settings_init' );

function grev3atc_secretkey_field_callback($args) {

	$options = get_option('grev3atc_keys');

	?>
	<input class="<?php echo esc_attr( $args['class'] ); ?>" id="<?php echo esc_attr( $args['label_for'] ); ?>" type="text" name="grev3atc_keys[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo isset( $options[ $args['label_for'] ] ) ? esc_attr(  $options[ $args['label_for'] ]  ) : ''; ?>">
    <p class="description">
		<?php esc_html_e( 'Wpisz swój secretKey', 'grev3atc' ); ?>
	</p>
    <?php
}


function grev3atc_sitekey_field_callback($args) {

	$options = get_option('grev3atc_keys');

	?>
	<input class="<?php echo esc_attr( $args['class'] ); ?>" id="<?php echo esc_attr( $args['label_for'] ); ?>" type="text" name="grev3atc_keys[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo isset( $options[ $args['label_for'] ] ) ? esc_attr(  $options[ $args['label_for'] ]  ) : ''; ?>">
    <p class="description">
		<?php esc_html_e( 'Wpisz swój siteKey', 'grev3atc' ); ?>
	</p>
    <?php
}

function grev3atc_section_callback( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Wpisz wartosci z ustawień panelu google reCaptcha.', 'grev3atc' ); ?></p>
	<?php
}


function grev3atc_options_page() {
	add_menu_page(
		'Woocommerce Google recaptcha v3 on Add to cart Options',
		'WC-grev3-atc',
		'manage_options',
		'grev3atc',
		'grev3atc_options_page_html'
	);
}



add_action( 'admin_menu', 'grev3atc_options_page' );



function grev3atc_options_page_html() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error( 'grev3atc_messages', 'grev3atc_message', __( 'Settings Saved', 'grev3atc' ), 'updated' );
	}

	settings_errors( 'grev3atc_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'grev3atc_option_group' );

			do_settings_sections( 'grev3atc' );

			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php
}


?>