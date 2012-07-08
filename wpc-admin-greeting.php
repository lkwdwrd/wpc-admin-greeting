<?php 
/**
 * @package Admin_Greeting_Customizer
 * @version 1.3
 */
/*
 * Plugin Name: Admin Greeting Customizer
 * Plugin URI: https://github.com/lkwdwrd/wpc-admin-greeting
 * Description: Adds a setting in General Settings that allows you to customize the greeting in your admin panel.
 * Version: 1.3
 * Author: Luke Woodward
 * Author URI: http://luke-woodward.com
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
/* 
 * Copyright (C) 2012 Woodward Multimedia, & Luke Woodward
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * Setup Plugin Actions & Settings
 * ---------------------------------------------------------------------------------*/

add_action( 'plugins_loaded', 'wpc_admin_greeting_init' );
add_action( 'admin_init', 'wpc_admin_settings_init' );
add_action( 'admin_bar_menu', 'wpc_admin_bar_filter' );

/**
 * Load the plugin text domain for l10n
 * 
 *
 * Uses action: 'plugins_loaded'
 *
 * @since 1.2
 */
if ( ! function_exists( 'wpc_admin_greeting_init' ) ){
	function wpc_admin_greeting_init() {
	  load_plugin_textdomain( 'wpc_admin_greeting', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' ); 
	}
}// wpc_admin_greeting_init

/**
 * Initialize plugin settings
 * 
 * Initializes one section and one field using the WordPress Settings API.
 * Adds the field to Settings>General in the WordPress dashboard.
 *
 * Uses action: 'admin_init'
 *
 * @since 1.0
 */
if ( ! function_exists( 'wpc_admin_settings_init' ) ){
	function wpc_admin_settings_init() {
		// Add the section to general settings
		add_settings_section( 
			'wpc_greeting_section',
			__('Admin Bar Greeting', 'wpc_admin_greeting'),
			'wpc_admin_settings_section',
			'general'
			);
		
		// Add the field with the names and function
		add_settings_field( 
			'wpc_admin_greeting',
			__('Admin Greeting', 'wpc_admin_greeting'),
			'wpc_admin_setting',
			'general',
			'wpc_greeting_section' 
			);
		
		// Register the wpc_admin_greeting setting
		register_setting( 'general', 'wpc_admin_greeting', 'wpc_greeting_filter' );
	}
} //wpc_admin_settings_init
/**
 * Custom filter to limit the greeting to 30 characters
 * 
 * Takes the greeting and chops it to 30 characters if it is longer, also adds 
 * a filter to allow others to hook in if desired to change this behavior
 *
 * @since 1.1
 * @uses apply_filters() Calls 'wpc_greeting' on the filtered greeeing and greeting.
 *
 * @param string $greeting The greeting enetered in the dashboard
 * @return int The sanitized greeting.
 */
if ( ! function_exists( 'wpc_greeting_filter' ) ){
	function wpc_greeting_filter( $greeting ){
		$greeting = sanitize_text_field( $greeting );
		$filtered = $greeting;
		if ( strlen( $greeting ) >= 30 )
			$filtered = substr( $greeting, 0, 30 );
		return apply_filters( 'wpc_greeting', $filtered, $greeting );
	}
}//wpc_greeting_filter
/**
 * Add a description to the settings section
 *
 * @since 1.0
 */
if ( ! function_exists( 'wpc_admin_settings_section' ) ){
	function wpc_admin_settings_section() {
		echo '<p>' . esc_html_e( 'Set a custom greeting for your admin bar.', 'wpc_admin_greeting' ) . '</p>';
	}
} //wpc_admin_settings_section
/**
 * Print the setting option and default value
 *
 * @since 1.0
 */
if ( ! function_exists( 'wpc_admin_setting' ) ){
	function wpc_admin_setting() {
		$setting = get_option( 'wpc_admin_greeting' );
		$default_greeting = __( 'Howdy,', 'wpc_admin_greeting' );
		$setting = ( $setting == '' ) ? $default_greeting : $setting;
		echo '<input name="wpc_admin_greeting" id="wpc_admin_greeting" type="text" value="' . esc_attr( $setting ) . '" />';
		echo '<p class="description">' . esc_html_e( 'Greeting is limited to 30 characters.', 'wpc_admin_greeting' ) . '</p>';
	}
}//wpc_admin_setting

/**
 * Fiter the Greeting
 * ---------------------------------------------------------------------------------*/

/**
 * Change the admin bar greeting text based on the user setting.
 * If nothing has changed from the default, ignore.
 *
 * @param object $admin_bar Admin bar menu to override.
 * @return void
 */
if ( ! function_exists( 'wpc_admin_bar_filter' ) ){
	function wpc_admin_bar_filter( $admin_bar ){
		$greeting = get_option( 'wpc_admin_greeting' );
		$default_greeting = __( 'Howdy,', 'wpc_admin_greeting' );
		$greeting = ( $greeting == '' ) ? $default_greeting : $greeting;
		if ( ! empty( $greeting ) && $greeting != $default_greeting ) {
			$admin_menu = $admin_bar->get_node( 'my-account' );
			$admin_menu->title = preg_replace( "/^{$default_greeting}/", $greeting, $admin_menu->title );
			$admin_bar->add_node( $admin_menu );
		}
	}
}//wpc_acmin_bar_filter