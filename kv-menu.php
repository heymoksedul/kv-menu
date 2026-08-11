<?php
/**
 * Plugin Name: KV Menu Elementor Widget
 * Description: A highly customizable responsive menu widget for Elementor.
 * Version:     1.0.0
 * Author:      Antigravity
 * Text Domain: kv-menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class KV_Menu_Extension {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	public function init() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}

		// Load Admin Dashboard
		require_once( __DIR__ . '/includes/class-kv-menu-admin-dashboard.php' );
		\KV_Menu_Admin_Dashboard::instance();

		// Load Mega Menu controller
		require_once( __DIR__ . '/includes/class-kv-menu-mega.php' );
		\KV_Menu_Mega::instance();

		// Register widget
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

		// Register assets
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'kv-menu' ),
			'<strong>' . esc_html__( 'KV Menu Elementor Widget', 'kv-menu' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'kv-menu' ) . '</strong>'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function register_widgets( $widgets_manager ) {
		require_once( __DIR__ . '/widgets/class-kv-menu-widget.php' );
		require_once( __DIR__ . '/widgets/class-kv-mega-menu-tabs-widget.php' );
		$widgets_manager->register( new \KV_Menu_Widget() );
		$widgets_manager->register( new \KV_Mega_Menu_Tabs_Widget() );
	}

	public function enqueue_assets() {
		wp_enqueue_style( 'kv-menu-css', plugins_url( '/assets/css/kv-menu.css', __FILE__ ), [], '1.1.1' );
		wp_enqueue_script( 'kv-menu-js', plugins_url( '/assets/js/kv-menu.js', __FILE__ ), [ 'jquery' ], '1.1.1', true );
	}
}

KV_Menu_Extension::instance();
