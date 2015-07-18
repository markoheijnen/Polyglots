<?php
/**
 * @package Polyglots
 */

// Make sure we don't expose any info if called directly
if ( ! defined('ABSPATH') ) {
	exit;
}

class Polyglots_GlotPress {
	private $locale;
	private $variant;

	public function __construct( $locale, $variant = 'default' ) {
		$this->locale  = $locale;
		$this->variant = $variant;
	}


	public function get_core_projects() {
		if ( false === ( $percentage = Polyglots_Cache::get_value( 'core', Polyglots_Cache_Groups::general ) ) ) {
			$percentage = 0;
			$url        = 'https://translate.wordpress.org/api/languages/' . $this->locale_to_slug();

			$response = wp_remote_get( $url );
			$body     = wp_remote_retrieve_body( $response );

			if ( $body ) {
				$data = json_decode( $body );
				
				if ( isset( $data->{"1"} ) ) {
					$core_project = $data->{"1"}->{"2"};

					$current_count = $all_count = 0;

					foreach ( $core_project->sets as $set ) {
						if ( ! in_array( $set->project_path, array( 'wp/dev', 'wp/dev/admin', 'wp/dev/admin/network', 'wp/dev/cc' ) ) ) {
							continue;
						}

						if ( $set->slug == 'formal' || $set->slug == 'informal' ) {
							continue;
						}

						$current_count += $set->current_count;
						$all_count     += $set->all_count;
					}

					if ( $all_count ) {
						$percentage = floor( $current_count / $all_count * 100 ) / 100;
						Polyglots_Cache::set_value( 'core', $percentage, Polyglots_Cache_Groups::general );
					}
				}
			}
		}

		return $percentage;
	}


	public function get_theme_project( $slug ) {
		if ( ! isset( $slug ) ) {
			return false;
		}

		if ( false === ( $data = Polyglots_Cache::get_value( $slug, Polyglots_Cache_Groups::theme ) ) ) {
			$data = array( 'url' => false, 'dot_org' => false );

			if ( in_array( $slug, array( 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentytwelve', 'twentyeleven', 'twentyten' ) ) ) {
				$project_slug = 'wp/dev/' . $slug;
			}
			else {
				$project_slug = 'wp-themes/' . $slug;
			}

			$possible_repository = sprintf( 'https://translate.wordpress.org/api/projects/%s', $project_slug );
			$resp                = wp_remote_get( $possible_repository );

			if ( ! is_wp_error( $resp ) && wp_remote_retrieve_response_code( $resp ) == 200 ) {
				$data['url']     = sprintf( 'https://translate.wordpress.org/projects/%s', $project_slug );
				$data['dot_org'] = true;
				$data['sets']    = json_decode( wp_remote_retrieve_body( $resp ) );
			}

			Polyglots_Cache::set_value( $slug, $data, Polyglots_Cache_Groups::theme );
		}

		return $data;
	}


	public function get_plugin_project( $slug ) {
		if ( ! isset( $slug ) ) {
			return false;
		}

		if ( false === ( $data = Polyglots_Cache::get_value( $slug, Polyglots_Cache_Groups::plugin ) ) ) {
			$data = array( 'url' => false, 'dot_org' => false );

			$possible_repository = sprintf( 'https://translate.wordpress.org/api/projects/wp-plugins/%s/%s', $slug, Polyglots_Config::project_variant() );
			$resp                = wp_remote_get( $possible_repository );

			if ( ! is_wp_error( $resp ) && wp_remote_retrieve_response_code( $resp ) == 200 ) {
				$data['url']     = sprintf( 'https://translate.wordpress.org/projects/wp-plugins/%s/%s', $slug, Polyglots_Config::project_variant() );
				$data['dot_org'] = true;
				$data['sets']    = json_decode( wp_remote_retrieve_body( $resp ) );
			}

			Polyglots_Cache::set_value( $slug, $data, Polyglots_Cache_Groups::plugin );
		}

		return $data;
	}



	public function locale_to_slug() {
		$locale = Polyglots_Locales::by_field( 'wp_locale', $this->locale );
		$slug   = $locale->slug;

		return $slug;
	}

}