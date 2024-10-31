<?php


/**
 * The file that defines the core plugin class
 *
 * @since      1.0.0
 * @package    Serious_Daily_Writing_Habit\includes
 */


class Serious_Daily_Writing_Habit {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Daily_Writing_Habit_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SERIOUS_DAILY_WRITING_HABIT_VERSION' ) ) {
			$this->version = SERIOUS_DAILY_WRITING_HABIT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'serious-daily-writing-habit';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		// $this->define_public_hooks();  -- No need for public hooks in this plugin

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Plugin_Name_Loader. Orchestrates the hooks of the plugin.
	 * - Plugin_Name_i18n. Defines internationalization functionality.
	 * - Plugin_Name_Admin. Defines all hooks for the admin area.
	 * - Plugin_Name_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-serious-daily-writing-habit-loader.php';

		/**		 * The class responsible for defining internationalization functionality of the plugin.		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-serious-daily-writing-habit-i18n.php';

		/**		 * The class responsible for defining all actions that occur in the admin area.		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-serious-daily-writing-habit-admin.php';

		$this->loader = new Serious_Daily_Writing_Habit_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Daily_Writing_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Serious_Daily_Writing_Habit_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Serious_Daily_Writing_Habit_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        // Registering also the main plugin menu
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'init_admin_menu' );

		//Hooking into post_updated to be able to recalculate the writing count
		$this->loader->add_action( 'post_updated', $plugin_admin, 'post_updated_count_callback',2, 3); // we want to execute our function before others can modify the text
		//Hooking into post_updated to be able to recalculate the writing count
		$this->loader->add_action( 'wp_insert_post', $plugin_admin, 'post_inserted_count_callback',2, 3); // we want to execute our function before others can modify the text


		// Hooking into the admin dashboard creation to render our own widget
		// We don't need to require_once the plugin file since it´s already loaded as part of the load_dependencies of the main plugin_admin class
		$plugin_widget = new Serious_Daily_Writing_Habit_Dashboard_Widget( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_widget, 'add_dashboard_widget' );


		$plugin_settings = new Serious_Daily_Writing_Habit_Settings_Page( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_init', $plugin_settings, 'init_settings_page' );

	}



	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
