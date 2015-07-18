<?php
/**
 * @package Polyglots
 */

// Make sure we don't expose any info if called directly
if ( ! defined('ABSPATH') ) {
	exit;
}

class Polyglots_Plugins {
	private $glotPress;

	public function __construct() {
		add_filter( 'polyglots_dashboard_counts', array( $this, 'get_active_plugin_counts' ), 11 );

		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 3 );
		add_action( 'after_plugin_row', array( $this, 'after_plugin_row' ), 10, 3 );

		$this->glotpress = new Polyglots_GlotPress;
	}


	public function get_active_plugin_counts( $counts = array() ) {
		$plugins = get_plugins();

		foreach ( $plugins as $path => $plugin_data ) {
			if ( is_plugin_active( $path ) ) {
				$plugin_data['slug'] = dirname( $path );
				$data                = $this->glotpress->get_plugin_project( $plugin_data );

				if ( isset( $data['sets'] ) ) {
					$sets = wp_list_filter( $data['sets']->translation_sets, array( 'wp_locale' => Polyglots_Config::get_locale(), 'slug' => Polyglots_Config::get_locale_variant() ) );
					$set  = reset( $sets );

					if ( $set ) {
						$counts[ 'plugin_' . $plugin_data['slug'] ] = array(
							'title' => $plugin_data['Name'],
							'count' => $set->percent_translated
						);
					}
				}
			}
		}

		return $counts;
	}


	public function plugin_action_links( $actions, $plugin_file, $plugin_data ) {
		if ( 1 == 1 ) {
			$actions = array_merge( $actions, array(
				'<a href="' . admin_url( 'options-general.php?page=myplugin' ) . '">' . __( 'Translate', 'polyglots' ) . '</a>',
			) );
		}

		return $actions;
	}

	public function after_plugin_row( $plugin_file, $plugin_data, $status ) {
		$wp_list_table = _get_list_table('WP_Plugins_List_Table');

		$class = 'plugin-polyglots-info';

		if ( empty( $plugin_data['update'] ) ) {
			$class .= ' with-shadow';
		}

		echo '<tr class="' . $class . '"><td colspan="' . esc_attr( $wp_list_table->get_column_count() ) . '" class="colspanchange">';
		$data = $this->glotpress->get_plugin_project( $plugin_data );

		
		if ( $data && $data['url'] ) {
			echo '<div class="update-message">';

			echo '<p>' . __( 'This project can be translated.', 'polyglots' ) . ' <a href="' . $data['url'] . '" class="btn btn-primary">' . __( 'Go and translate.', 'polyglots' ) . '</a></p>';

			if ( isset( $data['sets'] ) ) {
				$sets = wp_list_filter( $data['sets']->translation_sets, array( 'wp_locale' => Polyglots_Config::get_locale() ) );

				echo '<p>';
				foreach( $sets as $set ) {
					printf( __( '%s has been translated for %g%% and %d waiting strings.' ) . '<br/>', $set->name, $set->percent_translated, $set->waiting_count );
				}
				echo '</p>';
			}

			echo '</div>';
		}

		echo '</td></tr>';
	}

}