<?php

// Example file to help with the installation of the "Envato Market" plugin 
// This can be used instead of TGMP
// This could be automatically added to existing Themes/Plugins

// This needs to be added to the Theme/Plugin file:
// require_once 'envato-market-helper.php'; 

if ( ! class_exists( 'Envato_Market' ) && ! function_exists( 'envato_market_install' ) ) {
	function envato_market_install( $api, $action, $args ) {
		$download_url = 'http://some/path/to/envato-market-plugin.zip';
		if ( 'plugin_information' != $action ||
			false !== $api ||
			! isset( $args->slug ) ||
			'envato-market' != $args->slug
		) return $api;

		$api = new stdClass();
		$api->name = 'Envato Market';
		$api->version = '1.0.0';
		$api->download_link = esc_url( $download_url );
		return $api;
	}
	add_filter( 'plugins_api', 'envato_market_install', 10, 3 );
}

/**
 * Prompt the user to install Envato Market plugin
 */
if ( ! class_exists( 'Envato_Market' ) && ! function_exists( 'envato_market_install_notice' ) ) {

	/**
	 * Display a notice if the "Envato Market" plugin hasn't been installed.
	 * @return void
	 */
	function envato_market_install_notice() {
		$active_plugins = apply_filters( 'active_plugins', get_option('active_plugins' ) );
		if ( in_array( 'envato-market/envato-market.php', $active_plugins ) ) return;

		$slug = 'envato-market';
		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ), 'install-plugin_' . $slug );
		$activate_url = 'plugins.php?action=activate&plugin=' . urlencode( 'envato-market/envato-market.php' ) . '&plugin_status=all&paged=1&s&_wpnonce=' . urlencode( wp_create_nonce( 'activate-plugin_envato-market/envato-market.php' ) );

    // Use the word "CodeCanyon" or "ThemeForest" here as buyers may not know what "Envato" is.
		$message = '<a href="' . esc_url( $install_url ) . '">Install the Envato Market plugin</a> to get updates for your ThemeForest themes or CodeCanyon plugins.';
		$is_downloaded = false;
		$plugins = array_keys( get_plugins() );
		foreach ( $plugins as $plugin ) {
			if ( strpos( $plugin, 'envato-market.php' ) !== false ) {
				$is_downloaded = true;
				$message = '<a href="' . esc_url( admin_url( $activate_url ) ) . '">Activate the Envato Market plugin</a> to get updates for your ThemeForest themes or CodeCanyon plugins.';
			}
		}
		echo '<div class="updated fade"><p>' . $message . '</p></div>' . "\n";
	}

	add_action( 'admin_notices', 'envato_market_install_notice' );
}

