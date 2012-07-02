<?php 
/*
 * Plugin Name: Admin Greeting Customizer
 * Plugin URI: https://github.com/lkwdwrd/wpc-admin-greeting
 * Description: Adds a setting in General Settings that allows you to customize the greeting in your admin panel.
 * Version: 1.2
 * Author: Luke Woodward
 * Author URI: http://luke-woodward.com
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
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
 * Setup Plugin Actions
 * ---------------------------------------------------------------------------------*/

add_action( 'plugins_loaded', 'wpc_admin_greeting_init' );
add_action( 'admin_init', 'wpc_admin_settings_init' );
add_action( 'admin_bar_menu', 'wpc_admin_bar_filter' );

/**
 * l10n - action: 'plugins_loaded'
 * ---------------------------------------------------------------------------------*/

if ( !function_exists( 'wpc_admin_greeting_init' ) ){
	function wpc_admin_greeting_init() {
	  load_plugin_textdomain( 'wpc_admin_greeting', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' ); 
	}
}// wpc_admin_greeting_init

/**
 * Plugin Settings Setup using the Settings API - action: 'admin_init'
 * ---------------------------------------------------------------------------------*/

//Initialize the Settings
if ( !function_exists( 'wpc_admin_settings_init' ) ){
	function wpc_admin_settings_init() {
		// Add the section to general settings
		add_settings_section('wpc_greeting_section',
			__('Admin Bar Greeting', 'wpc_admin_greeting'),
			'wpc_admin_settings_section',
			'general');
		
		// Add the field with the names and function
		add_settings_field('wpc_admin_greeting',
			__('Admin Greeting', 'wpc_admin_greeting'),
			'wpc_admin_setting',
			'general',
			'wpc_greeting_section');
		
		// Register the wpc_admin_greeting setting
		register_setting('general', 'wpc_admin_greeting', 'wpc_greeting_filter');
	}
} //wpc_admin_settings_init 
//Limit to greeting to 30 characters, also add a filter to allow others to hook in if desired.
if ( !function_exists( 'wpc_greeting_filter' ) ){
	function wpc_greeting_filter( $greeting ){
		$filtered = $greeting;
		if ( strlen( $greeting ) >= 30 )
			$filtered = substr( $greeting, 0, 30 );
		return apply_filters( 'wpc_greeting', $filtered, $greeting );
	}
}//wpc_greeting_filter
//Add a short description to the settings section.
if ( !function_exists( 'wpc_admin_settings_section' ) ){
	function wpc_admin_settings_section() {
		echo '<p>'.__('Set a custom greeting for your admin bar.', 'wpc_admin_greeting') . '</p>';
	}
} //wpc_admin_settings_section
//Print the setting option and default value
if ( !function_exists( 'wpc_admin_setting' ) ){
	function wpc_admin_setting() {
		$setting = get_option('wpc_admin_greeting' );
		$default_greeting = __( 'Howdy', 'wpc_admin_greeting' );
		$setting = ( $setting == '' ) ? $default_greeting : $setting;
		echo '<input name="wpc_admin_greeting" id="wpc_admin_greeting" type="text" value="' . $setting . '" />';
		echo '<p class="description">' . __('Greeting is limited to 30 characters.', 'wpc_admin_greeting' ) . '</p>';
	}
}//wpc_admin_setting

/**
 * Greeting Filter - action: 'admin-bar-menu'
 * ---------------------------------------------------------------------------------*/

//Filters the admin bar greeting so that it doesn't always say 'Howdy'
if ( !function_exists( 'wpc_admin_bar_filter' ) ){
	function wpc_admin_bar_filter( $admin_bar ){
		$admin_menu = $admin_bar->get_node('my-account');
		$greeting = get_option('wpc_admin_greeting' );
		$default_greeting = __( 'Howdy', 'wpc_admin_greeting' );
		$greeting = ( $greeting == '' ) ? $default_greeting : $greeting;
		$admin_menu->title = preg_replace( "/^{$default_greeting}/", $greeting, $admin_menu->title );
		$admin_bar->remove_node('my-account');
		$admin_bar->add_node($admin_menu);
	}
}//wpc_acmin_bar_filter