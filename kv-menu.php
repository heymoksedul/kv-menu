<?php
/**
 * Plugin Name: KV Navigation
 * Description: A complete navigation header system for Elementor — logo, menu, right CTA, editable mega menu, and a per-page transparent/dark + sticky-light skin system.
 * Version:     1.1.0
 * Author:      Antigravity
 * Text Domain: kv-menu
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'KV_NAV_VERSION', '1.1.30' );
define( 'KV_NAV_FILE', __FILE__ );
define( 'KV_NAV_PATH', plugin_dir_path( __FILE__ ) );
define( 'KV_NAV_URL', plugin_dir_url( __FILE__ ) );

final class KV_Menu_Extension {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action( 'init', [ $this, 'load_textdomain' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Load translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'kv-menu', false, dirname( plugin_basename( KV_NAV_FILE ) ) . '/languages' );
	}

	public function init() {
		// Check if Elementor installed and activated.
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}

		// Load Admin Dashboard.
		require_once KV_NAV_PATH . 'includes/class-kv-menu-admin-dashboard.php';
		\KV_Menu_Admin_Dashboard::instance();

		// Load Mega Menu controller (per nav-menu-item settings).
		require_once KV_NAV_PATH . 'includes/class-kv-menu-mega.php';
		\KV_Menu_Mega::instance();

		// Load per-page header settings (meta box + body class skin resolver).
		require_once KV_NAV_PATH . 'includes/class-kv-nav-page-settings.php';
		\KV_Nav_Page_Settings::instance();

		// Load native Mega Menu builder (structured content for kv_mega_menu CPT).
		require_once KV_NAV_PATH . 'includes/class-kv-mega-builder.php';
		\KV_Mega_Builder::instance();

		// Register widgets.
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

		// Register assets.
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		// Add mega menu classes to menu items.
		add_filter( 'nav_menu_css_class', [ $this, 'add_mega_menu_classes' ], 10, 4 );
	}

	public function add_mega_menu_classes( $classes, $item, $args, $depth ) {
		if ( 0 === $depth ) {
			$mega_enabled = get_post_meta( $item->ID, '_kv_menu_mega_enabled', true );
			if ( 'yes' === $mega_enabled ) {
				$classes[] = 'menu-item-has-mega-menu';
				
				$width_type = get_post_meta( $item->ID, '_kv_menu_mega_width_type', true );
				if ( empty( $width_type ) ) {
					$width_type = 'full';
				}
				if ( 'full' === $width_type ) {
					$classes[] = 'menu-item-mega-full';
				} else {
					$align = get_post_meta( $item->ID, '_kv_menu_mega_alignment', true );
					$align = empty( $align ) ? 'center' : $align;
					if ( 'center' === $align ) {
						$classes[] = 'menu-item-mega-center';
					}
				}
			}
		}
		return $classes;
	}

	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'kv-menu' ),
			'<strong>' . esc_html__( 'KV Navigation', 'kv-menu' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'kv-menu' ) . '</strong>'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function register_widgets( $widgets_manager ) {
		// New full navigation header widget.
		require_once KV_NAV_PATH . 'widgets/class-kv-navigation-widget.php';
		$widgets_manager->register( new \KV_Navigation_Widget() );

		// Legacy menu widget (kept for backward compatibility).
		require_once KV_NAV_PATH . 'widgets/class-kv-menu-widget.php';
		$widgets_manager->register( new \KV_Menu_Widget() );

		// Mega menu tabs content widget.
		require_once KV_NAV_PATH . 'widgets/class-kv-mega-menu-tabs-widget.php';
		$widgets_manager->register( new \KV_Mega_Menu_Tabs_Widget() );
	}

	public function enqueue_assets() {
		// Legacy widget assets.
		wp_enqueue_style( 'kv-menu-css', KV_NAV_URL . 'assets/css/kv-menu.css', [], KV_NAV_VERSION );
		wp_enqueue_script( 'kv-menu-js', KV_NAV_URL . 'assets/js/kv-menu.js', [ 'jquery' ], KV_NAV_VERSION, true );

		// New navigation header assets.
		wp_enqueue_style( 'kv-navigation-css', KV_NAV_URL . 'assets/css/kv-navigation.css', [], KV_NAV_VERSION );
		wp_enqueue_script( 'kv-navigation-js', KV_NAV_URL . 'assets/js/kv-navigation.js', [ 'jquery' ], KV_NAV_VERSION, true );

		// Ensure Font Awesome is available for submenu arrows and mega-menu icons.
		$fa_handles = [ 'elementor-icons-fa-solid', 'elementor-icons-fa-brands', 'elementor-icons-fa-regular' ];
		$fa_loaded  = false;
		foreach ( $fa_handles as $h ) {
			if ( wp_style_is( $h, 'registered' ) ) {
				wp_enqueue_style( $h );
				$fa_loaded = true;
			}
		}
		if ( ! $fa_loaded ) {
			wp_enqueue_style( 'kv-fa-cdn', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', [], '5.15.4' );
		}
	}
}

KV_Menu_Extension::instance();
