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

	public function __construct( $locale = '', $variant = 'default' ) {
		if ( $locale ) {
			$this->locale  = $locale;
			$this->variant = $variant;
		}
		else {
			$this->locale  = Polyglots_Config::get_locale();
			$this->variant = Polyglots_Config::get_locale_variant();
		}
	}


	public function get_core_projects() {
		if ( false === ( $percentage = Polyglots_Cache::get_value( 'core', Polyglots_Cache_Groups::general ) ) ) {
			$percentage = 0;
			$slug       = $this->locale_to_slug();

			if ( ! $slug ) {
				return $percentage;
			}

			$url      = 'https://translate.wordpress.org/api/languages/' . $slug;
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

						if ( $set->slug != $this->variant ) {
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


	public function get_plugin_project( $plugin_data ) {
		if ( ! isset( $plugin_data['slug'] ) ) {
			return false;
		}

		if ( false === ( $data = Polyglots_Cache::get_value( $plugin_data['slug'], Polyglots_Cache_Groups::plugin ) ) ) {
			$gp   = $this->get_glotpress_data( $plugin_data['slug'], 'wp-plugins', $plugin_data );
			$data = $this->get_project_data( $gp );

			Polyglots_Cache::set_value( $plugin_data['slug'], $data, Polyglots_Cache_Groups::plugin );
		}

		return $data;
	}


	public function locale_to_slug() {
		$locale = Polyglots_Locales::by_field( 'wp_locale', $this->locale );

		if ( $locale ) {
			return $locale->slug;
		}

		return false;
	}


	private function get_glotpress_data( $slug, $type, $args = array() ) {
		if ( isset( $args['Translation Site'] ) && isset( $args['Translation Project'] ) ) {
			return array(
				'url'     => esc_url_raw( $args['Translation Site'] ),
				'project' => $args['Translation Project']
			);
		}
		else {
			return array(
				'url'     => 'https://translate.wordpress.org',
				'project' => sprintf( 'wp-plugins/%s/%s', $plugin_data['slug'], Polyglots_Config::project_variant() )
			);
		}
	}

	private function get_project_data( $glotpress ) {
		$data = array( 'url' => false, 'dot_org' => false );

		$possible_repository = sprintf( '%s/api/projects/%s', $glotpress['url'], $glotpress['project'] );
		$resp                = wp_remote_get( $possible_repository );

		if ( ! is_wp_error( $resp ) && wp_remote_retrieve_response_code( $resp ) == 200 ) {
			$data['url']     = sprintf( '%s/projects/%s', $glotpress['url'], $glotpress['project'] );
			$data['dot_org'] = true;
			$data['sets']    = json_decode( wp_remote_retrieve_body( $resp ) );
		}

		return $data;
	}

}