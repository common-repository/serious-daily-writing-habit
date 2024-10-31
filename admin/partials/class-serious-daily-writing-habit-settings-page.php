<?php
/**
 * Provide a widget admin dashboard area view for the plugin
 *
 * @since      1.0.0
 * @package    Serious_Daily_Writing_Habit\admin
 * @subpackage Serious_Daily_Writing_Habit\admin\partials
 */

class Serious_Daily_Writing_Habit_Settings_Page{

	public function init_settings_page() {

		register_setting(
			'settings_dwh_group',
			'dwh_options'
		);

		add_settings_section(
			'dwh_section',
			'',
			false,
			'dwh-options'
		);

		add_settings_field(
			'target_number_words',
			'Target number of words to write every day',
			array($this,'render_target_number_words_field'),
			'dwh-options',
			'dwh_section'
		);

		add_settings_field(
			'number_days_show_habit',
			'Number of days to track',
			array($this,'render_number_days_show_habit_field'),
			'dwh-options',
			'dwh_section'
		);

	}


	public function settings_page_layout() {

		// Check required user capability
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}

		// Admin Page Layout
		echo '<div class="wrap">' . "\n";
		echo '	<h1>' . get_admin_page_title() . '</h1>' . "\n";
		echo '	<form action="options.php" method="post">' . "\n";

		settings_fields( 'settings_dwh_group' );
		do_settings_sections( 'dwh-options' );
		submit_button();

		echo '	</form>' . "\n";
		echo '</div>' . "\n";

	}

	public function render_target_number_words_field() {

		// Retrieve the full set of options
		$options = get_option( 'dwh_options' );

		// Set default value for this particular option in the group
		$value = isset( $options['target_number_words'] ) ? $options['target_number_words'] : '750';

		// Field output.
		echo '<input type="number" name="dwh_options[target_number_words]" size="10" type="text" value="' . esc_attr( $value ).'" />';

		//	echo '<p>' . __( 'Number of words you want to set as goal', 'daily-writing-habit' ) . '</p>';
	}



	public function render_number_days_show_habit_field() {

		// Retrieve data from the database.
		$options = get_option( 'dwh_options' );

		// Set default value.
		$value = isset( $options['number_days_show_habit'] ) ? $options['number_days_show_habit'] : '30';

		// Field output.
		echo '<input type="number" name="dwh_options[number_days_show_habit]" size="10" type="text" value="' . esc_attr( $value ).'" />';

	}

}