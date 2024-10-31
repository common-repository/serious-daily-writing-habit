<?php


/**
 * @wordpress-plugin
 * Plugin Name:       Serious Daily Writing Habit
 * Plugin URI:        https://seriouswp.com
 * Description:       Helps in developing a writing habit by setting and tracking a daily writing goal
 * Version:           1.0.0
 * Author:            Jordi Cabot
 * Author URI:        https://seriouswp.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       serious-daily-writing-habit
 * Domain Path:       /languages
 *
 * This plugin is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or  any later version.
 *
 * This plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

/**
 * @since      1.0.0
 * @package    Serious_Daily_Writing_Habit\admin
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('SERIOUS_DAILY_WRITING_HABIT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-daily-writing-habit-activator.php
 */
function activate_serious_daily_writing_habit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-serious-daily-writing-habit-activator.php';
	Serious_Daily_Writing_Habit_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-daily-writing-habit-deactivator.php
 */
function deactivate_serious_daily_writing_habit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-serious-daily-writing-habit-deactivator.php';
	Serious_Daily_Writing_Habit_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_serious_daily_writing_habit' );
register_deactivation_hook( __FILE__, 'deactivate_serious_daily_writing_habit' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-serious-daily-writing-habit.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_serious_daily_writing_habit() {

	$plugin = new Serious_Daily_Writing_Habit(); //Creating the plugin main object
	$plugin->run();

}

run_serious_daily_writing_habit(); //start of the plugin execution
