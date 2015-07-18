<?php
/**
 * @package Polyglots
 */

// Make sure we don't expose any info if called directly
if ( ! defined('ABSPATH') ) {
	exit;
}

class Polyglots_Config {
	private static $locale  = false;
	private static $variant = false;

	public static function get_locale() {
		self::set_locale_and_variant();

		return self::$locale;
	}

	public static function get_locale_variant() {
		self::set_locale_and_variant();

		return self::$variant;
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


	private static function set_locale_and_variant() {
		if ( ! self::$locale ) {
			self::$locale  = get_locale();
			self::$variant = 'default';

			switch ( self::$locale ) {
				case 'de_DE_formal':
					self::$locale  = 'de_DE';
					self::$variant = 'formal';
			}
		}
	}

}