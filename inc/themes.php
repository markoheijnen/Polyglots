<?php
/**
 * @package Polyglots
 */

// Make sure we don't expose any info if called directly
if ( ! defined('ABSPATH') ) {
	exit;
}

class Polyglots_Themes {
	private $glotPress;

	public function __construct() {
		add_filter( 'polyglots_dashboard_counts', array( $this, 'get_active_theme_count' ), 9 );

		$this->glotpress = new Polyglots_GlotPress;
	}

	public function get_active_theme_count( $counts = array() ) {
		$theme = wp_get_theme();
		$slug  = $theme->stylesheet;
		$data  = $this->glotpress->get_theme_project( $slug );

		if ( isset( $data['sets'] ) ) {
			$sets = wp_list_filter( $data['sets']->translation_sets, array( 'wp_locale' => Polyglots_Config::get_locale(), 'slug' => Polyglots_Config::get_locale_variant() ) );
			$set  = reset( $sets );

			if ( $set ) {
				$counts[ 'theme' ] = array(
					'title' => $theme,
					'count' => $set->percent_translated
				);
			}
		}

		return $counts;
	}
}