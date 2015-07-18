<?php
/**
 * @package Polyglots
 */

// Make sure we don't expose any info if called directly
if ( ! defined('ABSPATH') ) {
	exit;
}

class Polyglots_Config {

	public static function get_locale() {
		return get_locale();
	}

	public static function get_locale_variant() {
		return 'default';
	}

	public static function project_variant() {
		return 'stable';
	}

	/**
	 * Returns the locales this installation supports
	 *
	 * @return array A list of locale codes
	 *
	 * @since 0.1.0
	 *
	 */
	public static function get_supported_locales() {
		$locales = array(
			get_locale()
		);

		// Find other locales


		// No need to return en_US
		$en_us_index = array_search( 'en_US', $locales );

		if ( $en_us_index !== false ) {
			unset( $locales[ $en_us_index ] );
		}

		return $locales;
	}

}