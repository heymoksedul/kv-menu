<?php
/**
 * KV Menu Admin Dashboard and CPT Registration.
 *
 * @package KV_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class KV_Menu_Admin_Dashboard {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action( 'init', [ $this, 'register_cpt' ] );
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
		add_action( 'admin_init', [ $this, 'ensure_elementor_support' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	/**
	 * Register the global default header mode setting.
	 */
	public function register_settings() {
		register_setting(
			'kv_nav_settings',
			'kv_nav_default_header_mode',
			[
				'type'              => 'string',
				'sanitize_callback' => [ $this, 'sanitize_default_mode' ],
				'default'           => 'light',
			]
		);
	}

	public function sanitize_default_mode( $value ) {
		return in_array( $value, [ 'light', 'dark' ], true ) ? $value : 'light';
	}

	/**
	 * Register the Mega Menu Custom Post Type.
	 */
	public function register_cpt() {
		$labels = [
			'name'                  => _x( 'Mega Menus', 'Post Type General Name', 'kv-menu' ),
			'singular_name'         => _x( 'Mega Menu', 'Post Type Singular Name', 'kv-menu' ),
			'menu_name'             => __( 'Mega Menus', 'kv-menu' ),
			'name_admin_bar'        => __( 'Mega Menu', 'kv-menu' ),
			'archives'              => __( 'Mega Menu Archives', 'kv-menu' ),
			'attributes'            => __( 'Mega Menu Attributes', 'kv-menu' ),
			'parent_item_colon'     => __( 'Parent Mega Menu:', 'kv-menu' ),
			'all_items'             => __( 'All Mega Menus', 'kv-menu' ),
			'add_new_item'          => __( 'Add New Mega Menu', 'kv-menu' ),
			'add_new'               => __( 'Add New', 'kv-menu' ),
			'new_item'              => __( 'New Mega Menu', 'kv-menu' ),
			'edit_item'             => __( 'Edit Mega Menu', 'kv-menu' ),
			'update_item'           => __( 'Update Mega Menu', 'kv-menu' ),
			'view_item'             => __( 'View Mega Menu', 'kv-menu' ),
			'view_items'            => __( 'View Mega Menus', 'kv-menu' ),
			'search_items'          => __( 'Search Mega Menu', 'kv-menu' ),
			'not_found'             => __( 'Not found', 'kv-menu' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'kv-menu' ),
			'featured_image'        => __( 'Featured Image', 'kv-menu' ),
			'set_featured_image'    => __( 'Set featured image', 'kv-menu' ),
			'remove_featured_image' => __( 'Remove featured image', 'kv-menu' ),
			'use_featured_image'    => __( 'Use as featured image', 'kv-menu' ),
			'insert_into_item'      => __( 'Insert into Mega Menu', 'kv-menu' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Mega Menu', 'kv-menu' ),
			'items_list'            => __( 'Mega Menus list', 'kv-menu' ),
			'items_list_navigation' => __( 'Mega Menus list navigation', 'kv-menu' ),
			'filter_items_list'     => __( 'Filter Mega Menus list', 'kv-menu' ),
		];
		$args = [
			'label'                 => __( 'Mega Menu', 'kv-menu' ),
			'description'           => __( 'KV Mega Menu Templates', 'kv-menu' ),
			'labels'                => $labels,
			'supports'              => [ 'title', 'elementor' ],
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => 'kv-menu-dashboard',
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true, // Needed for Elementor preview
			'capability_type'       => 'page',
		];
		register_post_type( 'kv_mega_menu', $args );
	}

	/**
	 * Register the top-level admin menu and submenus.
	 */
	public function register_admin_menu() {
		// Top Level Menu
		add_menu_page(
			__( 'KV Menu', 'kv-menu' ),
			__( 'KV Menu', 'kv-menu' ),
			'manage_options',
			'kv-menu-dashboard',
			[ $this, 'render_dashboard' ],
			'dashicons-menu',
			58
		);

		// First submenu (Dashboard)
		add_submenu_page(
			'kv-menu-dashboard',
			__( 'Dashboard', 'kv-menu' ),
			__( 'Dashboard', 'kv-menu' ),
			'manage_options',
			'kv-menu-dashboard',
			[ $this, 'render_dashboard' ]
		);

		// Shortcut to Appearance > Menus
		add_submenu_page(
			'kv-menu-dashboard',
			__( 'Nav Menus', 'kv-menu' ),
			__( 'Nav Menus', 'kv-menu' ),
			'manage_options',
			'nav-menus.php'
		);
	}

	/**
	 * Ensure Elementor supports our custom post type.
	 */
	public function ensure_elementor_support() {
		$cpt_support = get_option( 'elementor_cpt_support' );
		if ( ! $cpt_support ) {
			$cpt_support = [ 'page', 'post' ]; // Elementor default
		}

		if ( ! in_array( 'kv_mega_menu', $cpt_support ) ) {
			$cpt_support[] = 'kv_mega_menu';
			update_option( 'elementor_cpt_support', $cpt_support );
		}
	}

	/**
	 * Render the Dashboard page.
	 */
	public function render_dashboard() {
		?>
		<div class="wrap" style="max-width: 900px;">
			<h1 style="display: flex; align-items: center; gap: 10px;">
				<span class="dashicons dashicons-menu" style="font-size: 32px; width: 32px; height: 32px; color: #845CFF;"></span>
				<?php esc_html_e( 'KV Menu Dashboard', 'kv-menu' ); ?>
			</h1>

			<div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-top: 20px;">
				<h2 style="margin-top: 0;"><?php esc_html_e( 'Welcome to the Advanced Header System', 'kv-menu' ); ?></h2>
				<p style="font-size: 16px; color: #475569; line-height: 1.6;">
					<?php esc_html_e( 'This dashboard is your central hub for managing your website\'s navigation headers and Mega Menus.', 'kv-menu' ); ?>
				</p>

				<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px;">
					
					<div style="border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px;">
						<h3 style="margin-top: 0; display: flex; align-items: center; gap: 8px;">
							<span class="dashicons dashicons-welcome-widgets-menus" style="color: #845CFF;"></span>
							<?php esc_html_e( 'Manage Nav Menus', 'kv-menu' ); ?>
						</h3>
						<p style="color: #64748b; margin-bottom: 20px;"><?php esc_html_e( 'Create your menu structure, manage tabs, and set parent/child relationships.', 'kv-menu' ); ?></p>
						<a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>" class="button button-primary">
							<?php esc_html_e( 'Go to Nav Menus', 'kv-menu' ); ?>
						</a>
					</div>

					<div style="border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px;">
						<h3 style="margin-top: 0; display: flex; align-items: center; gap: 8px;">
							<span class="dashicons dashicons-layout" style="color: #845CFF;"></span>
							<?php esc_html_e( 'Manage Mega Menus', 'kv-menu' ); ?>
						</h3>
						<p style="color: #64748b; margin-bottom: 20px;"><?php esc_html_e( 'Design custom Elementor layouts to drop down from your navigation items.', 'kv-menu' ); ?></p>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=kv_mega_menu' ) ); ?>" class="button button-primary">
							<?php esc_html_e( 'Go to Mega Menus', 'kv-menu' ); ?>
						</a>
					</div>

				</div>
			</div>

			<div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-top: 20px; max-width: 900px;">
				<h2 style="margin-top: 0; display: flex; align-items: center; gap: 8px;">
					<span class="dashicons dashicons-admin-appearance" style="color: #845CFF;"></span>
					<?php esc_html_e( 'Global Header Default', 'kv-menu' ); ?>
				</h2>
				<p style="color: #64748b;">
					<?php esc_html_e( 'Default header mode for pages set to "Inherit". Override any single page from its "KV Header Settings" box.', 'kv-menu' ); ?>
				</p>
				<form method="post" action="options.php">
					<?php settings_fields( 'kv_nav_settings' ); ?>
					<?php $default_mode = get_option( 'kv_nav_default_header_mode', 'light' ); ?>
					<select name="kv_nav_default_header_mode" style="min-width: 220px;">
						<option value="light" <?php selected( $default_mode, 'light' ); ?>><?php esc_html_e( 'Solid Light', 'kv-menu' ); ?></option>
						<option value="dark" <?php selected( $default_mode, 'dark' ); ?>><?php esc_html_e( 'Transparent Dark', 'kv-menu' ); ?></option>
					</select>
					<?php submit_button( __( 'Save Default', 'kv-menu' ) ); ?>
				</form>
			</div>
		</div>
		<?php
	}
}
