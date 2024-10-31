<?php

/**
 * The core behaviuor for the admin part of the plugin
 *
 * @since      1.0.0
 * @package    Serious_Daily_Writing_Habit\admin
 */


class Serious_Daily_Writing_Habit_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	private $version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->load_dependencies();
	}


	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/partials/class-serious-daily-writing-habit-dashboard-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/partials/class-serious-daily-writing-habit-settings-page.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/partials/serious-daily-writing-habit-admin-display.php';
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		// We don't add any CSS-specific styles as part of the plugin
	//	wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/serious-daily-writing-habit-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		//adding the min version of the library wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/chartjs/Chart.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/chartjs/Chart.min.js', array( 'jquery' ), $this->version, false );
	}

	public function init_admin_menu() {
		//adding the top menu
		add_menu_page(
			'dwh',
			'Writing Habit',
			'manage_options',
			'dwh',
			'results_page_layout',  //top menu option links also to the results page directly
			'dashicons-edit',
			null
		);

		//adding the results page
		add_submenu_page(
			'dwh',
			'Daily Writing Habit reports',
			'Report',
			'manage_options',
			'dwh',
			'results_page_layout'
		);

		//adding the options page
		add_submenu_page(
			'dwh',
			'Daily Writing goals - Configuration',
			'Writing goals',
			'manage_options',
			'dwh-options',
			array(new Serious_Daily_Writing_Habit_Settings_Page, 'settings_page_layout')
		);
	}


	public static function get_latests_increments($ndays) {
		$count=[];
		for ($i = 0; $i < $ndays; $i++) {
			$today = new DateTime('today');
			$day_to_count=$ndays-$i-1;
			$today->modify("-$day_to_count day"); // we move today a few days back
			$count[$i]=Serious_Daily_Writing_Habit_Admin::get_writing_increment($today);
		}
		return $count;
	}


	public static function get_today_writing_increment() {
		return Serious_Daily_Writing_Habit_Admin::get_writing_increment( new DateTime('today'));
	}

	//Word count for the given day
	public static function get_writing_increment($writing_day) {

		$writing_day_parsed=date_parse($writing_day->format("Ymd"));

		$args = array(
			'post_type' => 'post',
			'post_status' => 'any', // we also want the drafts
			'nopaging'=>true,
			'date_query' => array(
				'inclusive' =>true,
				'relation' => 'OR',
				'after' => array(    // returns posts created on the $writing_day
					'year'  => $writing_day_parsed['year'],
					'month' => $writing_day_parsed['month'],
					'day'   => $writing_day_parsed['day'],
					)
				,
				'after' => 	array(    // returns posts modified on the $writing_day
					'column' => 'post_modified',
					'year'  => $writing_day_parsed['year'],
					'month' => $writing_day_parsed['month'],
					'day'   => $writing_day_parsed['day'],
					)
			)
		);

		$query_day_posts = new WP_Query( $args );  //We try to narrow down the posts for which an increment may exist

		//adding current writing counts per post
		$posts=$query_day_posts->get_posts();
		$count=0;
		foreach( $posts as $post ){
			$day_post_inc=0;
			$meta_incs= get_post_meta($post->ID, 'increment',false);
			if ( !empty( $meta_incs ) ) {
				foreach ( $meta_incs as $inc ) //the number of increments associated to a post will be typically rather small
				{
					if ( $inc['d'] == $writing_day->format("Ymd")) {
						$day_post_inc = $inc['v'];
					}
				}
				$count = $count + $day_post_inc;
			}
		}
		return $count;
	}

	//Action called every time a modification of the post is stored in the database (after save, update,...)
	public function post_inserted_count_callback( $post_id, $post_after, $update)
	{
		if (!$update && $post_after->post_status=='draft') { // the post is not an updated version of an old post BUT it could still be a new draft of an unpublished post

			$this->post_updated_count_callback($post_id, $post_after,NULL);
		}
	}

	//Action called every time a modification of the post is stored in the database (after save, update,...)
	public function post_updated_count_callback( $post_id, $post_after, $post_before) {

		if($post_after->post_type=='post') {
			$new_word_length = str_word_count( wp_strip_all_tags( $post_after->post_content ) );
			$today = date( "Ymd" );
			if ( ! isset( $post_before ) ) //it's a new post, the whole length of the body is the increment
			{
				add_post_meta( $post_id, 'increment', [ 'd' => $today, 'v' => $new_word_length ], false );
			} else {
				$old_word_length = str_word_count( wp_strip_all_tags( $post_before->post_content ) );
				$diff            = $new_word_length - $old_word_length;
				if ( $diff < 0 ) {
					$diff = 0;
				} //we don't penalize removing words
				if ( $diff != 0 ) { //if something has changed
					//we check if there is already metadata to be updated or we need to create a new entry for today's increment
					$meta_incs = get_post_meta( $post_before->ID, 'increment', false );
					if ( ! empty( $meta_incs ) ) {
						$previous_today_inc = 0;
						foreach ( $meta_incs as $inc ) //the number of increments associated to a post will be typically rather small
						{
							if ( $inc['d'] == date( "Ymd" ) ) {
								$previous_today_inc = $inc['v'];
							}
						}
					}
					if ( $previous_today_inc != 0 ) //if not, this will be the first time we modify the post today
					{
						delete_post_meta( $post_id, 'increment', array( "d" => $today, "v" => $previous_today_inc ) );
						add_post_meta( $post_id, 'increment', array(
							"d" => $today,
							"v" => $diff + $previous_today_inc
						) );
					} else //the diff is the complete addition to the post we have done so far today
					{
						add_post_meta( $post_id, 'increment', array( "d" => $today, "v" => $diff ) );
					}
				}
			}
		}
	}


}
