<?php
/**
 * @package Polyglots
 */

/*
	Plugin Name: Polyglots
	Description: A helper tool for our Polyglots team
	Version: 0.1

	Plugin URI: https://make.wordpress.org/Polyglots

	Author: Marko Heijnen
	Author URI: http://markoheijnen.com
	Donate link: https://markoheijnen.com/donate

	License:     GPL2
	Text Domain: polyglots
	Domain Path: /languages
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

include_once 'inc/cache.php';
include_once 'inc/config.php';
include_once 'inc/developer.php';
include_once 'inc/glotpress.php';
include_once 'inc/locales.php';
include_once 'inc/plugins.php';
include_once 'inc/themes.php';

class Polyglots {

	public static $file;
	public static $basename;

	/**
	 * Construct
	 *
	 * @since 0.1.0
	 *
	 */
	public function __construct() {
		self::$file     = __FILE__;
		self::$basename = plugin_basename( __FILE__ );

		add_action( 'plugins_loaded', array( $this, 'load' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup' ) );
	}

	public function load() {
		if ( get_locale() != 'en_US' ) {
			new Polyglots_Developer;
			new Polyglots_Plugins;
			new Polyglots_Themes;
		}
	}

	public function admin_enqueue_styles() {
		wp_register_style( 'polyglots', plugins_url( 'css/style.css', Polyglots::$basename ), false, '1.0.0' );
		wp_enqueue_style( 'polyglots' );
	}


	public function wp_dashboard_setup() {
		/*
		wp_add_dashboard_widget(
			'polyglots_widget',                          // Widget slug.
			__( 'Polyglots information', 'polyglots' ),  // Title.
			array( $this, 'dashboard_widget' )           // Display function.
		);*/

		add_meta_box( 'polyglots_widget', __( 'Polyglots information', 'polyglots' ), array( $this, 'dashboard_widget' ), 'dashboard', 'side', 'high' );
	}

	public function dashboard_widget() {
		$glotpress = new Polyglots_GlotPress;

		$items = array(
			'core' => array( 'title' => 'Core', 'count' => $glotpress->get_core_projects() )
		);
		$items = apply_filters( 'polyglots_dashboard_counts', $items );

		echo '<ul>';
		foreach ( $items as $id => $data ) {
			echo '<li>' . $data['title'] . ' <span>' . $data['count'] . '%</span></li>';
		}	
		echo '</ul>';

		echo '<p class="morelink"><a href="http://translate.wordpress.org/locale/' . $glotpress->locale_to_slug() . '">' . sprintf( __( 'Translate other projects on %s' ), 'http://translate.wordpress.org' ) . '</a></p>';
	}

}

$GLOBALS['polyglots'] = new Polyglots;