<?php
/**
 * KV Navigation — Per-page Header Settings.
 *
 * Adds a "KV Header Settings" meta box to public post types so each page can
 * choose its header skin (Inherit / Transparent Dark / Solid Light) and hide
 * the header entirely. The resolved mode is exposed to the frontend as a body
 * class (kv-nav-dark / kv-nav-light) that drives the widget's skin system.
 *
 * @package KV_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class KV_Nav_Page_Settings {

	private static $_instance = null;

	const META_MODE = '_kv_header_mode';
	const META_HIDE = '_kv_header_hide';
	const NONCE     = 'kv_nav_page_settings';

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
		add_action( 'save_post', [ $this, 'save' ], 10, 2 );
		add_filter( 'body_class', [ $this, 'body_class' ] );
	}

	/**
	 * Post types that should get the meta box.
	 */
	private function get_supported_post_types() {
		$types = get_post_types( [ 'public' => true ], 'names' );
		unset( $types['attachment'], $types['kv_mega_menu'], $types['elementor_library'] );
		/**
		 * Filter which post types show the KV Header Settings box.
		 */
		return apply_filters( 'kv_nav_page_settings_post_types', array_values( $types ) );
	}

	public function add_meta_box() {
		foreach ( $this->get_supported_post_types() as $post_type ) {
			add_meta_box(
				'kv_nav_header_settings',
				esc_html__( 'KV Header Settings', 'kv-menu' ),
				[ $this, 'render_box' ],
				$post_type,
				'side',
				'default'
			);
		}
	}

	public function render_box( $post ) {
		wp_nonce_field( self::NONCE, self::NONCE . '_nonce' );

		$mode = get_post_meta( $post->ID, self::META_MODE, true );
		if ( empty( $mode ) ) {
			$mode = 'inherit';
		}
		$hide = get_post_meta( $post->ID, self::META_HIDE, true );
		?>
		<p style="margin-bottom:6px;">
			<label for="kv_header_mode" style="font-weight:600; display:block; margin-bottom:4px;">
				<?php esc_html_e( 'Header Mode', 'kv-menu' ); ?>
			</label>
			<select name="kv_header_mode" id="kv_header_mode" style="width:100%;">
				<option value="inherit" <?php selected( $mode, 'inherit' ); ?>><?php esc_html_e( 'Inherit (default)', 'kv-menu' ); ?></option>
				<option value="dark" <?php selected( $mode, 'dark' ); ?>><?php esc_html_e( 'Transparent Dark', 'kv-menu' ); ?></option>
				<option value="light" <?php selected( $mode, 'light' ); ?>><?php esc_html_e( 'Solid Light', 'kv-menu' ); ?></option>
			</select>
		</p>
		<p style="color:#64748b; font-size:12px; margin:6px 0 12px;">
			<?php esc_html_e( 'Transparent Dark overlays the header on the hero with the dark skin. Solid Light forces the light skin.', 'kv-menu' ); ?>
		</p>
		<p>
			<label for="kv_header_hide" style="cursor:pointer;">
				<input type="checkbox" name="kv_header_hide" id="kv_header_hide" value="yes" <?php checked( $hide, 'yes' ); ?> />
				<?php esc_html_e( 'Hide header on this page', 'kv-menu' ); ?>
			</label>
		</p>
		<?php
	}

	public function save( $post_id, $post ) {
		// Verify nonce.
		if ( ! isset( $_POST[ self::NONCE . '_nonce' ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE . '_nonce' ] ) ), self::NONCE ) ) {
			return;
		}
		// Skip autosave / revisions.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		// Capability check.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Header mode.
		$mode  = isset( $_POST['kv_header_mode'] ) ? sanitize_key( wp_unslash( $_POST['kv_header_mode'] ) ) : 'inherit';
		$valid = [ 'inherit', 'dark', 'light' ];
		if ( ! in_array( $mode, $valid, true ) ) {
			$mode = 'inherit';
		}
		if ( 'inherit' === $mode ) {
			delete_post_meta( $post_id, self::META_MODE );
		} else {
			update_post_meta( $post_id, self::META_MODE, $mode );
		}

		// Hide header.
		if ( isset( $_POST['kv_header_hide'] ) && 'yes' === $_POST['kv_header_hide'] ) {
			update_post_meta( $post_id, self::META_HIDE, 'yes' );
		} else {
			delete_post_meta( $post_id, self::META_HIDE );
		}
	}

	/**
	 * Resolve the current page's header mode.
	 *
	 * @return string 'dark' | 'light'
	 */
	private function resolve_mode() {
		$mode = '';
		if ( is_singular() ) {
			$mode = get_post_meta( get_queried_object_id(), self::META_MODE, true );
		}
		if ( empty( $mode ) || 'inherit' === $mode ) {
			/**
			 * Global default header mode for pages set to Inherit.
			 * Backed by the option set on the KV Menu dashboard, still filterable.
			 */
			$mode = apply_filters( 'kv_nav_default_header_mode', get_option( 'kv_nav_default_header_mode', 'light' ) );
		}
		return ( 'dark' === $mode ) ? 'dark' : 'light';
	}

	public function body_class( $classes ) {
		$classes[] = 'kv-nav-' . $this->resolve_mode();

		if ( is_singular() && 'yes' === get_post_meta( get_queried_object_id(), self::META_HIDE, true ) ) {
			$classes[] = 'kv-nav-hide-header';
		}
		return $classes;
	}
}
