<?php
/**
 * KV Navigation — Native Mega Menu Builder.
 *
 * Structured metabox on the kv_mega_menu CPT (tabs → links + featured + promo),
 * stored in the `_kv_mega_data` post meta. The nav widget renders this data for
 * both desktop (tabbed panel) and mobile (drilldown), replacing the Elementor
 * template approach. Elementor-built megas still work as a fallback.
 *
 * @package KV_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class KV_Mega_Builder {

	private static $_instance = null;

	const META  = '_kv_mega_data';
	const NONCE = 'kv_mega_builder';

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
		add_action( 'save_post_kv_mega_menu', [ $this, 'save' ], 10, 2 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	/**
	 * Return the structured mega data for a post, or null if none/empty.
	 */
	public static function get_data( $post_id ) {
		$data = get_post_meta( $post_id, self::META, true );
		if ( empty( $data ) || empty( $data['tabs'] ) || ! is_array( $data['tabs'] ) ) {
			return null;
		}
		return $data;
	}

	public function add_meta_box() {
		add_meta_box(
			'kv_mega_builder',
			esc_html__( 'KV Mega Menu Content', 'kv-menu' ),
			[ $this, 'render_box' ],
			'kv_mega_menu',
			'normal',
			'high'
		);
	}

	public function enqueue( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen || 'kv_mega_menu' !== $screen->post_type || 'post' !== $screen->base ) {
			return;
		}
		wp_enqueue_media();

		// Load Font Awesome so icons render in the picker. Elementor's handles are
		// not reliably registered in wp-admin, so also load a CDN fallback.
		foreach ( [ 'elementor-icons-fa-solid', 'elementor-icons-fa-regular', 'elementor-icons-fa-brands', 'elementor-icons-shared-0' ] as $fa ) {
			if ( wp_style_is( $fa, 'registered' ) ) {
				wp_enqueue_style( $fa );
			}
		}
		wp_enqueue_style( 'kv-fa-cdn', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', [], '5.15.4' );

		wp_enqueue_style( 'kv-mega-builder', KV_NAV_URL . 'assets/css/admin-mega-builder.css', [], KV_NAV_VERSION );
		wp_enqueue_script( 'kv-mega-builder', KV_NAV_URL . 'assets/js/admin-mega-builder.js', [ 'jquery' ], KV_NAV_VERSION, true );
		wp_localize_script( 'kv-mega-builder', 'kvMegaBuilder', [
			'selectImage' => esc_html__( 'Select SVG / Image', 'kv-menu' ),
			'useImage'    => esc_html__( 'Use this image', 'kv-menu' ),
			'pickIcon'    => esc_html__( 'Choose Icon', 'kv-menu' ),
			'search'      => esc_html__( 'Search icons...', 'kv-menu' ),
			'library'     => esc_html__( 'Icon Library', 'kv-menu' ),
			'custom'      => esc_html__( 'Custom SVG / Image', 'kv-menu' ),
			'useUrl'      => esc_html__( 'Use URL', 'kv-menu' ),
			'urlPlaceholder' => esc_html__( 'https://...svg or image URL', 'kv-menu' ),
		] );
	}

	/**
	 * Preview HTML for an icon value (FA class or image/SVG URL).
	 */
	public static function icon_preview_html( $value ) {
		if ( '' === $value ) {
			return '';
		}
		if ( preg_match( '#^https?://#', $value ) ) {
			return '<img src="' . esc_url( $value ) . '" alt="" />';
		}
		return '<i class="' . esc_attr( $value ) . '"></i>';
	}

	/**
	 * Reusable icon picker control (button + hidden input).
	 */
	private function icon_control( $name, $value ) {
		ob_start();
		?>
		<span class="kvmega-icon-field">
			<button type="button" class="button kvmega-icon-btn">
				<span class="kvmega-icon-preview"><?php echo self::icon_preview_html( $value ); // phpcs:ignore ?></span>
				<span class="kvmega-icon-text"><?php esc_html_e( 'Icon', 'kv-menu' ); ?></span>
			</button>
			<input type="hidden" class="kvmega-in-icon" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" />
		</span>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render one link row.
	 */
	private function render_link( $tid, $lid, $link = [] ) {
		$icon  = isset( $link['icon'] ) ? $link['icon'] : '';
		$title = isset( $link['title'] ) ? $link['title'] : '';
		$url   = isset( $link['url'] ) ? $link['url'] : '';
		$base  = "kvmega[tabs][{$tid}][links][{$lid}]";
		ob_start();
		?>
		<div class="kvmega-link">
			<?php echo $this->icon_control( "{$base}[icon]", $icon ); // phpcs:ignore ?>
			<input type="text" class="kvmega-in-title" name="<?php echo esc_attr( $base ); ?>[title]" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php esc_attr_e( 'Link title', 'kv-menu' ); ?>" />
			<input type="text" class="kvmega-in-url" name="<?php echo esc_attr( $base ); ?>[url]" value="<?php echo esc_attr( $url ); ?>" placeholder="https://" />
			<button type="button" class="button-link kvmega-remove-link" aria-label="<?php esc_attr_e( 'Remove link', 'kv-menu' ); ?>">&times;</button>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render one tab block.
	 */
	private function render_tab( $tid, $tab = [] ) {
		$g = function ( $k, $d = '' ) use ( $tab ) {
			return isset( $tab[ $k ] ) ? $tab[ $k ] : $d;
		};
		$base  = "kvmega[tabs][{$tid}]";
		$links = ( isset( $tab['links'] ) && is_array( $tab['links'] ) ) ? $tab['links'] : [];
		ob_start();
		?>
		<div class="kvmega-tab" data-tid="<?php echo esc_attr( $tid ); ?>">
			<div class="kvmega-tab-header">
				<span class="dashicons dashicons-menu"></span>
				<input type="text" class="kvmega-tab-title-in" name="<?php echo esc_attr( $base ); ?>[tab_title]" value="<?php echo esc_attr( $g( 'tab_title' ) ); ?>" placeholder="<?php esc_attr_e( 'Tab title (sidebar)', 'kv-menu' ); ?>" />
				<button type="button" class="button kvmega-toggle-tab"><?php esc_html_e( 'Toggle', 'kv-menu' ); ?></button>
				<button type="button" class="button kvmega-remove-tab"><?php esc_html_e( 'Remove', 'kv-menu' ); ?></button>
			</div>

			<div class="kvmega-tab-body">
				<div class="kvmega-row">
					<label><?php esc_html_e( 'Content Main Title', 'kv-menu' ); ?>
						<input type="text" name="<?php echo esc_attr( $base ); ?>[main_title]" value="<?php echo esc_attr( $g( 'main_title' ) ); ?>" />
					</label>
					<label><?php esc_html_e( 'Subtitle', 'kv-menu' ); ?>
						<input type="text" name="<?php echo esc_attr( $base ); ?>[subtitle]" value="<?php echo esc_attr( $g( 'subtitle' ) ); ?>" />
					</label>
				</div>

				<p class="kvmega-sub"><?php esc_html_e( 'Links (icon class | title | URL)', 'kv-menu' ); ?></p>
				<div class="kvmega-links" data-tid="<?php echo esc_attr( $tid ); ?>">
					<?php
					if ( ! empty( $links ) ) {
						foreach ( array_values( $links ) as $lid => $link ) {
							echo $this->render_link( $tid, $lid, $link ); // phpcs:ignore
						}
					}
					?>
				</div>
				<button type="button" class="button kvmega-add-link" data-tid="<?php echo esc_attr( $tid ); ?>">+ <?php esc_html_e( 'Add Link', 'kv-menu' ); ?></button>

				<div class="kvmega-row kvmega-featured">
					<div>
						<label><?php esc_html_e( 'Featured Image', 'kv-menu' ); ?></label><br>
						<input type="hidden" class="kvmega-featured-input" name="<?php echo esc_attr( $base ); ?>[featured_image]" value="<?php echo esc_attr( $g( 'featured_image' ) ); ?>" />
						<span class="kvmega-featured-preview">
							<?php if ( $g( 'featured_image' ) ) : ?>
								<img src="<?php echo esc_url( $g( 'featured_image' ) ); ?>" alt="" />
							<?php endif; ?>
						</span>
						<button type="button" class="button kvmega-media-select"><?php esc_html_e( 'Select', 'kv-menu' ); ?></button>
						<button type="button" class="button kvmega-media-remove"><?php esc_html_e( 'Remove', 'kv-menu' ); ?></button>
					</div>
					<label><?php esc_html_e( 'Featured Image Link (optional)', 'kv-menu' ); ?>
						<input type="text" name="<?php echo esc_attr( $base ); ?>[featured_url]" value="<?php echo esc_attr( $g( 'featured_url' ) ); ?>" placeholder="https://" />
					</label>
				</div>

				<div class="kvmega-promo">
					<label class="kvmega-promo-toggle">
						<input type="checkbox" name="<?php echo esc_attr( $base ); ?>[promo_show]" value="yes" <?php checked( $g( 'promo_show' ), 'yes' ); ?> />
						<strong><?php esc_html_e( 'Show Promo Box', 'kv-menu' ); ?></strong>
					</label>
					<div class="kvmega-row">
						<div class="kvmega-icon-label-wrap">
							<label><?php esc_html_e( 'Promo Icon', 'kv-menu' ); ?></label><br>
							<?php echo $this->icon_control( "{$base}[promo_icon]", $g( 'promo_icon' ) ); // phpcs:ignore ?>
						</div>
						<label><?php esc_html_e( 'Promo Badge', 'kv-menu' ); ?>
							<input type="text" name="<?php echo esc_attr( $base ); ?>[promo_badge]" value="<?php echo esc_attr( $g( 'promo_badge' ) ); ?>" placeholder="NEW" />
						</label>
					</div>
					<label class="kvmega-full"><?php esc_html_e( 'Promo Title', 'kv-menu' ); ?>
						<input type="text" name="<?php echo esc_attr( $base ); ?>[promo_title]" value="<?php echo esc_attr( $g( 'promo_title' ) ); ?>" />
					</label>
					<label class="kvmega-full"><?php esc_html_e( 'Promo Description', 'kv-menu' ); ?>
						<textarea name="<?php echo esc_attr( $base ); ?>[promo_desc]" rows="2"><?php echo esc_textarea( $g( 'promo_desc' ) ); ?></textarea>
					</label>
					<div class="kvmega-row">
						<label><?php esc_html_e( 'Promo Link Text', 'kv-menu' ); ?>
							<input type="text" name="<?php echo esc_attr( $base ); ?>[promo_link_text]" value="<?php echo esc_attr( $g( 'promo_link_text' ) ); ?>" />
						</label>
						<label><?php esc_html_e( 'Promo Link URL', 'kv-menu' ); ?>
							<input type="text" name="<?php echo esc_attr( $base ); ?>[promo_link_url]" value="<?php echo esc_attr( $g( 'promo_link_url' ) ); ?>" placeholder="https://" />
						</label>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_box( $post ) {
		wp_nonce_field( self::NONCE, self::NONCE . '_nonce' );
		$data = get_post_meta( $post->ID, self::META, true );
		$tabs = ( ! empty( $data['tabs'] ) && is_array( $data['tabs'] ) ) ? array_values( $data['tabs'] ) : [];
		?>
		<div class="kvmega-builder">
			<p class="description"><?php esc_html_e( 'Build the mega menu content. It renders as a tabbed panel on desktop and a drill-down list on mobile.', 'kv-menu' ); ?></p>

			<div class="kvmega-tabs">
				<?php
				foreach ( $tabs as $tid => $tab ) {
					echo $this->render_tab( $tid, $tab ); // phpcs:ignore
				}
				?>
			</div>

			<button type="button" class="button button-primary kvmega-add-tab">+ <?php esc_html_e( 'Add Tab', 'kv-menu' ); ?></button>

			<hr>
			<h4><?php esc_html_e( 'Sidebar Bottom', 'kv-menu' ); ?></h4>
			<div class="kvmega-row">
				<label><?php esc_html_e( 'Bottom Title', 'kv-menu' ); ?>
					<input type="text" name="kvmega[bottom_title]" value="<?php echo esc_attr( isset( $data['bottom_title'] ) ? $data['bottom_title'] : '' ); ?>" placeholder="Get In Touch" />
				</label>
				<label><?php esc_html_e( 'Bottom Info', 'kv-menu' ); ?>
					<input type="text" name="kvmega[bottom_info]" value="<?php echo esc_attr( isset( $data['bottom_info'] ) ? $data['bottom_info'] : '' ); ?>" placeholder="01873-116666" />
				</label>
			</div>

			<script type="text/html" id="kvmega-tab-template">
				<?php echo $this->render_tab( '__TID__', [] ); // phpcs:ignore ?>
			</script>
			<script type="text/html" id="kvmega-link-template">
				<?php echo $this->render_link( '__TID__', '__LID__', [] ); // phpcs:ignore ?>
			</script>
		</div>
		<?php
	}

	public function save( $post_id, $post ) {
		if ( ! isset( $_POST[ self::NONCE . '_nonce' ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE . '_nonce' ] ) ), self::NONCE ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$raw   = isset( $_POST['kvmega'] ) ? wp_unslash( $_POST['kvmega'] ) : []; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$clean = [ 'tabs' => [], 'bottom_title' => '', 'bottom_info' => '' ];

		if ( ! empty( $raw['tabs'] ) && is_array( $raw['tabs'] ) ) {
			foreach ( array_values( $raw['tabs'] ) as $t ) {
				if ( ! is_array( $t ) ) {
					continue;
				}
				$tab = [
					'tab_title'       => sanitize_text_field( isset( $t['tab_title'] ) ? $t['tab_title'] : '' ),
					'main_title'      => sanitize_text_field( isset( $t['main_title'] ) ? $t['main_title'] : '' ),
					'subtitle'        => sanitize_text_field( isset( $t['subtitle'] ) ? $t['subtitle'] : '' ),
					'links'           => [],
					'featured_image'  => esc_url_raw( isset( $t['featured_image'] ) ? $t['featured_image'] : '' ),
					'featured_url'    => esc_url_raw( isset( $t['featured_url'] ) ? $t['featured_url'] : '' ),
					'promo_show'      => ( ! empty( $t['promo_show'] ) ) ? 'yes' : 'no',
					'promo_icon'      => sanitize_text_field( isset( $t['promo_icon'] ) ? $t['promo_icon'] : '' ),
					'promo_badge'     => sanitize_text_field( isset( $t['promo_badge'] ) ? $t['promo_badge'] : '' ),
					'promo_title'     => sanitize_text_field( isset( $t['promo_title'] ) ? $t['promo_title'] : '' ),
					'promo_desc'      => sanitize_textarea_field( isset( $t['promo_desc'] ) ? $t['promo_desc'] : '' ),
					'promo_link_text' => sanitize_text_field( isset( $t['promo_link_text'] ) ? $t['promo_link_text'] : '' ),
					'promo_link_url'  => esc_url_raw( isset( $t['promo_link_url'] ) ? $t['promo_link_url'] : '' ),
				];

				if ( ! empty( $t['links'] ) && is_array( $t['links'] ) ) {
					foreach ( array_values( $t['links'] ) as $l ) {
						if ( ! is_array( $l ) ) {
							continue;
						}
						$icon  = sanitize_text_field( isset( $l['icon'] ) ? $l['icon'] : '' );
						$title = sanitize_text_field( isset( $l['title'] ) ? $l['title'] : '' );
						$url   = esc_url_raw( isset( $l['url'] ) ? $l['url'] : '' );
						if ( '' === $title && '' === $icon && '' === $url ) {
							continue;
						}
						$tab['links'][] = [ 'icon' => $icon, 'title' => $title, 'url' => $url ];
					}
				}

				// Skip fully-empty tabs.
				if ( '' === $tab['tab_title'] && empty( $tab['links'] ) && '' === $tab['main_title'] ) {
					continue;
				}
				$clean['tabs'][] = $tab;
			}
		}

		$clean['bottom_title'] = sanitize_text_field( isset( $raw['bottom_title'] ) ? $raw['bottom_title'] : '' );
		$clean['bottom_info']  = sanitize_text_field( isset( $raw['bottom_info'] ) ? $raw['bottom_info'] : '' );

		if ( empty( $clean['tabs'] ) ) {
			delete_post_meta( $post_id, self::META );
		} else {
			update_post_meta( $post_id, self::META, $clean );
		}
	}
}
