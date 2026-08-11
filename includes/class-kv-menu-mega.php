<?php
/**
 * KV Menu Mega Menu Admin Controller.
 *
 * @package KV_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class KV_Menu_Mega {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		// Render custom fields in menus admin screen
		add_action( 'wp_nav_menu_item_custom_fields', [ $this, 'add_custom_menu_fields' ], 10, 4 );

		// Save custom fields
		add_action( 'wp_update_nav_menu_item', [ $this, 'save_custom_menu_fields' ], 10, 3 );

		// Enqueue admin assets
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );

		// AJAX callback to create a new Elementor section template
		add_action( 'wp_ajax_kv_menu_create_mega_template', [ $this, 'ajax_create_mega_template' ] );

		// Add custom classes to menu items on frontend
		add_filter( 'nav_menu_css_class', [ $this, 'add_mega_menu_class' ], 10, 4 );
	}

	/**
	 * Enqueue admin scripts & styles on nav-menus.php page.
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( 'nav-menus.php' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'kv-menu-admin-menus', plugins_url( '/assets/css/admin-menus.css', dirname( __DIR__ ) . '/kv-menu.php' ), [], '1.0.0' );
		wp_enqueue_script( 'kv-menu-admin-menus', plugins_url( '/assets/js/admin-menus.js', dirname( __DIR__ ) . '/kv-menu.php' ), [ 'jquery' ], '1.0.0', true );

		wp_localize_script( 'kv-menu-admin-menus', 'kvMenuAdmin', [
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
			'security' => wp_create_nonce( 'kv_menu_mega_nonce' ),
			'loading'  => esc_html__( 'Creating template...', 'kv-menu' ),
		] );
	}

	/**
	 * Output custom inputs for menu items inside WP Admin page.
	 */
	public function add_custom_menu_fields( $item_id, $item, $depth, $args ) {
		// Only allow Mega Menu settings on top-level menu items
		if ( $depth > 0 ) {
			return;
		}

		$mega_enabled         = get_post_meta( $item_id, '_kv_menu_mega_enabled', true );
		$template_id          = get_post_meta( $item_id, '_kv_menu_mega_template_id', true );
		$mega_keep_mobile_sub = get_post_meta( $item_id, '_kv_menu_mega_keep_mobile_sub', true );

		$mega_width_type      = get_post_meta( $item_id, '_kv_menu_mega_width_type', true );
		if ( empty( $mega_width_type ) ) {
			$mega_width_type = 'full';
		}
		$mega_custom_width    = get_post_meta( $item_id, '_kv_menu_mega_custom_width', true );
		if ( empty( $mega_custom_width ) ) {
			$mega_custom_width = '1000px';
		}
		$mega_alignment       = get_post_meta( $item_id, '_kv_menu_mega_alignment', true );
		if ( empty( $mega_alignment ) ) {
			$mega_alignment = 'center';
		}

		// Fetch classic Elementor Section Templates (for backward compatibility)
		$elementor_templates = get_posts( [
			'post_type'      => 'elementor_library',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => [
				[
					'key'     => '_elementor_template_type',
					'value'   => 'section',
					'compare' => '=',
				],
			],
		] );

		// Fetch new Mega Menus CPT
		$mega_menus = get_posts( [
			'post_type'      => 'kv_mega_menu',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		] );

		$templates = array_merge( $mega_menus, $elementor_templates );

		$edit_url = '';
		if ( ! empty( $template_id ) ) {
			$edit_url = admin_url( 'post.php?post=' . $template_id . '&action=elementor' );
		}
		?>
		<div class="kv-menu-mega-fields-container description-wide" style="margin: 10px 0; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; background-color: #f8fafc; box-sizing: border-box;">
			<h4 style="margin: 0 0 10px 0; font-size: 13px; color: #1e293b; display: flex; align-items: center; gap: 6px;">
				<span class="dashicons dashicons-menu" style="font-size: 18px; width: 18px; height: 18px; color: #6366f1;"></span>
				<strong><?php esc_html_e( 'KV Mega Menu Settings', 'kv-menu' ); ?></strong>
			</h4>
			
			<p class="field-kv-menu-mega-enabled description description-wide" style="margin: 0 0 10px 0;">
				<label for="edit-menu-item-kv-menu-mega-enabled-<?php echo esc_attr( $item_id ); ?>" style="cursor: pointer;">
					<input type="checkbox" id="edit-menu-item-kv-menu-mega-enabled-<?php echo esc_attr( $item_id ); ?>" class="code kv-menu-mega-enabled-checkbox" value="yes" name="kv-menu-mega-enabled[<?php echo esc_attr( $item_id ); ?>]"<?php checked( $mega_enabled, 'yes' ); ?> />
					<strong><?php esc_html_e( 'Enable Mega Menu', 'kv-menu' ); ?></strong>
				</label>
			</p>

			<div class="kv-menu-mega-options-wrapper" style="<?php echo ( 'yes' === $mega_enabled ) ? '' : 'display: none;'; ?> margin-top: 10px; padding-top: 10px; border-top: 1px dashed #cbd5e1;">
				
				<p class="field-kv-menu-mega-keep-mobile-sub description description-wide" style="margin: 0 0 12px 0;">
					<label for="edit-menu-item-kv-menu-mega-keep-mobile-sub-<?php echo esc_attr( $item_id ); ?>" style="cursor: pointer;">
						<input type="checkbox" id="edit-menu-item-kv-menu-mega-keep-mobile-sub-<?php echo esc_attr( $item_id ); ?>" class="code kv-menu-mega-keep-mobile-sub-checkbox" value="yes" name="kv-menu-mega-keep-mobile-sub[<?php echo esc_attr( $item_id ); ?>]"<?php checked( $mega_keep_mobile_sub, 'yes' ); ?> />
						<strong><?php esc_html_e( 'Keep default submenus on mobile', 'kv-menu' ); ?></strong>
					</label>
				</p>

				<p class="field-kv-menu-mega-template description description-wide" style="margin: 0 0 10px 0;">
					<label for="edit-menu-item-kv-menu-mega-template-<?php echo esc_attr( $item_id ); ?>" style="display: block; font-weight: 600; margin-bottom: 6px; color: #475569;">
						<?php esc_html_e( 'Select Elementor Section Template', 'kv-menu' ); ?>
					</label>
					<select id="edit-menu-item-kv-menu-mega-template-<?php echo esc_attr( $item_id ); ?>" name="kv-menu-mega-template-id[<?php echo esc_attr( $item_id ); ?>]" class="widefat kv-menu-mega-template-select" style="max-width: 100%;">
						<option value=""><?php esc_html_e( '— Select Template —', 'kv-menu' ); ?></option>
						<?php foreach ( $templates as $tmpl ) : ?>
							<option value="<?php echo esc_attr( $tmpl->ID ); ?>"<?php selected( $template_id, $tmpl->ID ); ?>><?php echo esc_html( $tmpl->post_title ); ?> (#<?php echo esc_attr( $tmpl->ID ); ?>)</option>
						<?php endforeach; ?>
					</select>
				</p>

				<p class="field-kv-menu-mega-width-type description description-wide" style="margin: 0 0 10px 0;">
					<label for="edit-menu-item-kv-menu-mega-width-type-<?php echo esc_attr( $item_id ); ?>" style="display: block; font-weight: 600; margin-bottom: 6px; color: #475569;">
						<?php esc_html_e( 'Mega Menu Width Type', 'kv-menu' ); ?>
					</label>
					<select id="edit-menu-item-kv-menu-mega-width-type-<?php echo esc_attr( $item_id ); ?>" name="kv-menu-mega-width-type[<?php echo esc_attr( $item_id ); ?>]" class="widefat kv-menu-mega-width-type-select" style="max-width: 100%;">
						<option value="full"<?php selected( $mega_width_type, 'full' ); ?>><?php esc_html_e( 'Full Width (Content)', 'kv-menu' ); ?></option>
						<option value="custom"<?php selected( $mega_width_type, 'custom' ); ?>><?php esc_html_e( 'Custom Width', 'kv-menu' ); ?></option>
					</select>
				</p>

				<div class="kv-menu-mega-custom-width-wrapper" style="<?php echo ( 'custom' === $mega_width_type ) ? '' : 'display: none;'; ?> margin-bottom: 10px;">
					<p class="field-kv-menu-mega-custom-width description description-wide" style="margin: 0 0 10px 0;">
						<label for="edit-menu-item-kv-menu-mega-custom-width-<?php echo esc_attr( $item_id ); ?>" style="display: block; font-weight: 600; margin-bottom: 6px; color: #475569;">
							<?php esc_html_e( 'Custom Width (e.g. 800px or 80%)', 'kv-menu' ); ?>
						</label>
						<input type="text" id="edit-menu-item-kv-menu-mega-custom-width-<?php echo esc_attr( $item_id ); ?>" class="widefat" name="kv-menu-mega-custom-width[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $mega_custom_width ); ?>" placeholder="e.g. 1000px" />
					</p>

					<p class="field-kv-menu-mega-alignment description description-wide" style="margin: 0 0 10px 0;">
						<label for="edit-menu-item-kv-menu-mega-alignment-<?php echo esc_attr( $item_id ); ?>" style="display: block; font-weight: 600; margin-bottom: 6px; color: #475569;">
							<?php esc_html_e( 'Position Alignment', 'kv-menu' ); ?>
						</label>
						<select id="edit-menu-item-kv-menu-mega-alignment-<?php echo esc_attr( $item_id ); ?>" name="kv-menu-mega-alignment[<?php echo esc_attr( $item_id ); ?>]" class="widefat" style="max-width: 100%;">
							<option value="left"<?php selected( $mega_alignment, 'left' ); ?>><?php esc_html_e( 'Left', 'kv-menu' ); ?></option>
							<option value="center"<?php selected( $mega_alignment, 'center' ); ?>><?php esc_html_e( 'Center', 'kv-menu' ); ?></option>
							<option value="right"<?php selected( $mega_alignment, 'right' ); ?>><?php esc_html_e( 'Right', 'kv-menu' ); ?></option>
						</select>
					</p>
				</div>

				<div class="kv-menu-mega-actions" style="margin-top: 12px; display: flex; gap: 8px; align-items: center;">
					<a href="<?php echo esc_url( $edit_url ); ?>" class="button button-primary kv-menu-edit-mega-btn" target="_blank" style="<?php echo ( ! empty( $template_id ) ) ? '' : 'display: none;'; ?>">
						<span class="dashicons dashicons-edit" style="vertical-align: middle; margin-right: 4px; font-size: 16px; width: 16px; height: 16px;"></span>
						<?php esc_html_e( 'Edit with Elementor', 'kv-menu' ); ?>
					</a>

					<button type="button" class="button button-secondary kv-menu-create-mega-btn" data-item-id="<?php echo esc_attr( $item_id ); ?>" style="<?php echo ( empty( $template_id ) ) ? '' : 'display: none;'; ?>">
						<span class="dashicons dashicons-plus-alt2" style="vertical-align: middle; margin-right: 4px; font-size: 16px; width: 16px; height: 16px;"></span>
						<?php esc_html_e( 'Create & Edit with Elementor', 'kv-menu' ); ?>
					</button>
					
					<span class="spinner kv-menu-mega-spinner" style="float: none; margin: 0 8px;"></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save custom menu settings when the menu is updated.
	 */
	public function save_custom_menu_fields( $menu_id, $menu_item_db_id, $args ) {
		// Verify capability
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		// Save Enable state
		if ( isset( $_POST['kv-menu-mega-enabled'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_kv_menu_mega_enabled', 'yes' );
		} else {
			delete_post_meta( $menu_item_db_id, '_kv_menu_mega_enabled' );
		}

		// Save Keep Mobile Submenus state
		if ( isset( $_POST['kv-menu-mega-keep-mobile-sub'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_kv_menu_mega_keep_mobile_sub', 'yes' );
		} else {
			delete_post_meta( $menu_item_db_id, '_kv_menu_mega_keep_mobile_sub' );
		}

		// Save Template ID
		if ( isset( $_POST['kv-menu-mega-template-id'][ $menu_item_db_id ] ) ) {
			$template_id = sanitize_text_field( $_POST['kv-menu-mega-template-id'][ $menu_item_db_id ] );
			if ( ! empty( $template_id ) ) {
				update_post_meta( $menu_item_db_id, '_kv_menu_mega_template_id', $template_id );
			} else {
				delete_post_meta( $menu_item_db_id, '_kv_menu_mega_template_id' );
			}
		} else {
			delete_post_meta( $menu_item_db_id, '_kv_menu_mega_template_id' );
		}

		// Save Width Type
		if ( isset( $_POST['kv-menu-mega-width-type'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_kv_menu_mega_width_type', sanitize_text_field( $_POST['kv-menu-mega-width-type'][ $menu_item_db_id ] ) );
		} else {
			delete_post_meta( $menu_item_db_id, '_kv_menu_mega_width_type' );
		}

		// Save Custom Width
		if ( isset( $_POST['kv-menu-mega-custom-width'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_kv_menu_mega_custom_width', sanitize_text_field( $_POST['kv-menu-mega-custom-width'][ $menu_item_db_id ] ) );
		} else {
			delete_post_meta( $menu_item_db_id, '_kv_menu_mega_custom_width' );
		}

		// Save Alignment
		if ( isset( $_POST['kv-menu-mega-alignment'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_kv_menu_mega_alignment', sanitize_text_field( $_POST['kv-menu-mega-alignment'][ $menu_item_db_id ] ) );
		} else {
			delete_post_meta( $menu_item_db_id, '_kv_menu_mega_alignment' );
		}
	}

	/**
	 * AJAX endpoint to create a new Elementor Section template and link it.
	 */
	public function ajax_create_mega_template() {
		// Check nonce
		check_ajax_referer( 'kv_menu_mega_nonce', 'security' );

		// Check capability
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( esc_html__( 'Unauthorized user.', 'kv-menu' ) );
		}

		$item_id = isset( $_POST['item_id'] ) ? intval( $_POST['item_id'] ) : 0;
		if ( ! $item_id ) {
			wp_send_json_error( esc_html__( 'Invalid menu item ID.', 'kv-menu' ) );
		}

		// Get the title of the menu item to name the template nicely
		$item_title = get_post_field( 'post_title', $item_id );
		$template_title = sprintf( esc_html__( 'Mega Menu - %s', 'kv-menu' ), $item_title );

		// Create template post
		$post_id = wp_insert_post( [
			'post_title'  => $template_title,
			'post_status' => 'publish',
			'post_type'   => 'kv_mega_menu',
		] );

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( $post_id->get_error_message() );
		}

		// Set template type to section (so Elementor loads it as section)
		update_post_meta( $post_id, '_elementor_template_type', 'section' );

		// Link to menu item
		update_post_meta( $item_id, '_kv_menu_mega_template_id', $post_id );
		update_post_meta( $item_id, '_kv_menu_mega_enabled', 'yes' );

		$edit_url = admin_url( 'post.php?post=' . $post_id . '&action=elementor' );

		wp_send_json_success( [
			'template_id' => $post_id,
			'edit_url'    => $edit_url,
			'title'       => $template_title,
		] );
	}

	/**
	 * Filter menu items classes to append menu-item-has-mega-menu
	 */
	public function add_mega_menu_class( $classes, $item, $args, $depth ) {
		// Only apply to top level items
		if ( 0 === $depth ) {
			$mega_enabled = get_post_meta( $item->ID, '_kv_menu_mega_enabled', true );
			if ( 'yes' === $mega_enabled ) {
				$classes[] = 'menu-item-has-mega-menu';

				$keep_mobile_sub = get_post_meta( $item->ID, '_kv_menu_mega_keep_mobile_sub', true );
				if ( 'yes' === $keep_mobile_sub ) {
					$classes[] = 'kv-menu-mega-keep-mobile-sub';
				}
			}
		}
		return $classes;
	}
}
