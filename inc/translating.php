<?php
/**
 * @package Polyglots
 */

// Make sure we don't expose any info if called directly
if ( ! defined('ABSPATH') ) {
	exit;
}

class Polyglots_Translating {

	public function __construct() {

	}

	public function get_plugins() {
		$plugins = get_plugins();
		$plugins_with_installations = array_filter( wp_list_pluck( $plugins, 'Translation Project' ) );

		$plugins = array_intersect_key( $plugins, $plugins_with_installations );

		return $plugins;
	}

}