<?php

/**
 * Provide a widget admin dashboard area view for the plugin
 *
 * @since      1.0.0
 * @package    Serious_Daily_Writing_Habit\admin
 * @subpackage Serious_Daily_Writing_Habit\admin\partials
 */

class Serious_Daily_Writing_Habit_Dashboard_Widget {

	private $plugin_name;
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       Name of this plugin.
	 * @param      string    $version    Version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}


	public function add_dashboard_widget() {
		wp_add_dashboard_widget('dhdw','Writing Daily Habit: How are you doing today?', array($this,'render_dashboard_widget'));
	}

	public function render_dashboard_widget() {

		$inc=Serious_Daily_Writing_Habit_Admin::get_today_writing_increment();
		$options = get_option( 'dwh_options' );
		$target=$options['target_number_words'];

		if (isset($target)) {

			if ( $inc <= 0 ) {
				$html = ' ( 0 / ' . $target . ' ) - Time is running! Get to work and start writing!';
			} elseif ( $inc >= $target ) {
				$html = '( ' . $inc . ' / ' . $target . ' ) - Well done! You are good to go. Make sure you come back tomorrow for more writing';
			} else{
				$html = '( ' . $inc . ' / ' . $target . ' ) - You are getting there! Keep pushing your writing. Still time to reach your goal. Do NOT disappoint yourself';
			}

		}else{
			$html='Go to the Daily Writing Habit Plugin settings page to set your writing target goals';
		}

		echo $html;

	}

}
