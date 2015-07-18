<?php
/**
 * @package Polyglots
 */

// Make sure we don't expose any info if called directly
if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Polyglots_Cache_Groups {
    const general = 'polyglot';
    const plugin  = 'polyglot_plugin';
    const theme   = 'polyglot_theme';
}

class Polyglots_Cache {

	public static function get_value( $cache_key, $cache_group ) {
		if ( self::is_valid_group( $cache_group ) ) {
			if ( wp_using_ext_object_cache() ) {
				return wp_cache_get( $cache_key, $cache_group );
			}
			else {
				$info = get_transient( $cache_group );

				if ( $info && is_array( $info ) && isset( $info[ $cache_key ] ) ) {
					return $info[ $cache_key ];
				}
			}
		}

		return false;
	}

	public static function set_value( $cache_key, $value, $cache_group ) {
		if ( self::is_valid_group( $cache_group ) ) {
			if ( wp_using_ext_object_cache() ) {
				return wp_cache_set( $cache_key, $value, $cache_group, 12 * HOUR_IN_SECONDS );
			}
			else {
				$info = get_transient( $cache_group );

				if ( ! $info || ! is_array( $info ) ) {
					$info = array();
				}

				$info[ $cache_key ] = $value;

				return set_transient( $cache_group, $info, 12 * HOUR_IN_SECONDS );
			}
		}

		return false;
	}


	public static function is_valid_group( $cache_group ) {
		$reflect   = new ReflectionClass('Polyglots_Cache_Groups');
		$constants = $reflect->getConstants();

		if ( array_search( $cache_group, $constants ) ) {
			return true;
		}

		return false;
	}

}