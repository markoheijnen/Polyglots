<?php
/**
 * @package Polyglots
 */

/*
	Plugin Name: Polyglots
	Plugin URI: https://make.wordpress.org/Polyglots
	Description: A helper tool for our Polyglots team
	Version: 0.1
	Author: Marko Heijnen
	Author URI: http://markoheijnen.com
	License: GPLv2 or later
	Text Domain: polyglots
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Make sure we don't expose any info if called directly
if ( ! defined('ABSPATH') ) {
	exit;
}

include_once 'inc/developer.php';
include_once 'inc/glotpress.php';
include_once 'inc/translating.php';

class Polyglots {

	/**
	 *
	 *
	 * @since 0.1.0
	 *
	 */
	public function __construct() {
		
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

new Polyglots;