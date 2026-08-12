<?php
/**
 * KV Navigation — Full Header Widget.
 *
 * Logo + Menu + Right CTA with a 3-skin system:
 *   - Light  (default, top of normal pages)
 *   - Dark   (transparent, top of pages flagged via the per-page meta box; body.kv-nav-dark)
 *   - Sticky (solid/light, after scroll; .kv-header-sticky)
 *
 * Skins are driven by CSS custom properties so the markup renders once and
 * only the active variables/classes change — no duplicate DOM, no flicker.
 *
 * @package KV_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class KV_Navigation_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'kv-navigation';
	}

	public function get_title() {
		return esc_html__( 'KV Navigation', 'kv-menu' );
	}

	public function get_icon() {
		return 'eicon-header';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	public function get_keywords() {
		return [ 'menu', 'nav', 'navigation', 'header', 'mega', 'sticky', 'transparent', 'kv' ];
	}

	private function get_available_menus() {
		$menus   = wp_get_nav_menus();
		$options = [ '' => esc_html__( 'Select Menu', 'kv-menu' ) ];
		foreach ( $menus as $menu ) {
			$options[ $menu->slug ] = $menu->name;
		}
		return $options;
	}

	/**
	 * The three skins and their labels.
	 */
	private function get_kv_skins() {
		return [
			'light'  => esc_html__( 'Header — Light Skin (default)', 'kv-menu' ),
			'dark'   => esc_html__( 'Header — Transparent / Dark Skin', 'kv-menu' ),
			'sticky' => esc_html__( 'Header — Sticky Skin', 'kv-menu' ),
		];
	}

	private function get_elementor_templates() {
		$options = [ '0' => esc_html__( '— Select Template —', 'kv-menu' ) ];
		$templates = get_posts( [
			'post_type'      => 'elementor_library',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		] );
		if ( ! empty( $templates ) ) {
			foreach ( $templates as $template ) {
				$options[ $template->ID ] = $template->post_title;
			}
		}
		return $options;
	}

	protected function register_controls() {

		// ==========================================================
		// CONTENT: LAYOUT
		// ==========================================================
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Layout', 'kv-menu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'container_width',
			[
				'label'   => esc_html__( 'Container Width', 'kv-menu' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'full'  => esc_html__( 'Full Width', 'kv-menu' ),
					'boxed' => esc_html__( 'Boxed', 'kv-menu' ),
				],
				'default' => 'boxed',
			]
		);

		$this->add_responsive_control(
			'boxed_max_width',
			[
				'label'      => esc_html__( 'Boxed Max Width', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 600, 'max' => 1920 ] ],
				'default'    => [ 'size' => 1200, 'unit' => 'px' ],
				'condition'  => [ 'container_width' => 'boxed' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-nav-inner' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				],
			]
		);

		$this->add_responsive_control(
			'header_height',
			[
				'label'      => esc_html__( 'Header Height', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 40, 'max' => 200 ] ],
				'selectors'  => [
					'{{WRAPPER}} .kv-nav-inner' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'header_padding',
			[
				'label'      => esc_html__( 'Header Padding', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [ 'top' => 0, 'right' => 24, 'bottom' => 0, 'left' => 24, 'unit' => 'px', 'isLinked' => false ],
				'selectors'  => [
					'{{WRAPPER}} .kv-nav-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'menu_position',
			[
				'label'     => esc_html__( 'Menu Position', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => [
					'center' => esc_html__( 'Center', 'kv-menu' ),
					'left'   => esc_html__( 'Left (next to logo)', 'kv-menu' ),
					'right'  => esc_html__( 'Right (next to CTA)', 'kv-menu' ),
				],
				'default'   => 'center',
			]
		);

		$this->add_control(
			'breakpoint',
			[
				'label'   => esc_html__( 'Mobile Breakpoint', 'kv-menu' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'none'   => esc_html__( 'None (Always Desktop)', 'kv-menu' ),
					'tablet' => esc_html__( 'Tablet & Mobile (< 1025px)', 'kv-menu' ),
					'mobile' => esc_html__( 'Mobile Only (< 768px)', 'kv-menu' ),
				],
				'default' => 'tablet',
			]
		);

		$this->add_control(
			'panel_animation',
			[
				'label'     => esc_html__( 'Drawer Animation', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => [
					'slide-right' => esc_html__( 'Slide Right', 'kv-menu' ),
					'slide-left'  => esc_html__( 'Slide Left', 'kv-menu' ),
					'fade-in'     => esc_html__( 'Fade In', 'kv-menu' ),
				],
				'default'   => 'slide-right',
				'condition' => [ 'breakpoint!' => 'none' ],
			]
		);

		$this->end_controls_section();

		// ==========================================================
		// CONTENT: LOGO
		// ==========================================================
		$this->start_controls_section(
			'section_logo',
			[
				'label' => esc_html__( 'Logo', 'kv-menu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_logo',
			[
				'label'        => esc_html__( 'Show Logo', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'logo_source',
			[
				'label'     => esc_html__( 'Logo Source', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => [
					'site'   => esc_html__( 'Site Logo (Customizer)', 'kv-menu' ),
					'custom' => esc_html__( 'Custom Images', 'kv-menu' ),
				],
				'default'   => 'custom',
				'condition' => [ 'show_logo' => 'yes' ],
			]
		);

		$this->add_control(
			'logo_default',
			[
				'label'       => esc_html__( 'Default Logo', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'default'     => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
				'description' => esc_html__( 'Shown on light headers and inner pages.', 'kv-menu' ),
				'condition'   => [ 'show_logo' => 'yes', 'logo_source' => 'custom' ],
			]
		);

		$this->add_control(
			'logo_dark',
			[
				'label'       => esc_html__( 'Dark-mode Logo', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'description' => esc_html__( 'Optional light/white logo for transparent dark headers. Falls back to the default logo.', 'kv-menu' ),
				'condition'   => [ 'show_logo' => 'yes', 'logo_source' => 'custom' ],
			]
		);

		$this->add_control(
			'logo_sticky',
			[
				'label'       => esc_html__( 'Sticky Logo', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'description' => esc_html__( 'Optional logo for the sticky (scrolled) header. Falls back to the default logo.', 'kv-menu' ),
				'condition'   => [ 'show_logo' => 'yes', 'logo_source' => 'custom' ],
			]
		);

		$this->add_control(
			'logo_url',
			[
				'label'     => esc_html__( 'Logo Link', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => [ 'url' => '' ],
				'placeholder' => home_url( '/' ),
				'condition' => [ 'show_logo' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'logo_height',
			[
				'label'      => esc_html__( 'Logo Height', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 10, 'max' => 160 ] ],
				'default'    => [ 'size' => 40, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-nav-logo-img' => 'height: {{SIZE}}{{UNIT}}; width: auto;',
				],
				'condition'  => [ 'show_logo' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// ==========================================================
		// CONTENT: MENU
		// ==========================================================
		$this->start_controls_section(
			'section_menu',
			[
				'label' => esc_html__( 'Menu', 'kv-menu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'menu',
			[
				'label'   => esc_html__( 'Choose Menu', 'kv-menu' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $this->get_available_menus(),
				'default' => '',
			]
		);

		$this->add_control(
			'show_underline',
			[
				'label'        => esc_html__( 'Hover/Active Underline', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'submenu_indicator_icon',
			[
				'label'   => esc_html__( 'Desktop Indicator Icon', 'kv-menu' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => [ 'value' => 'fas fa-angle-down', 'library' => 'fa-solid' ],
			]
		);

		$this->add_control(
			'submenu_indicator_icon_mobile',
			[
				'label'     => esc_html__( 'Mobile Indicator Icon', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [ 'value' => 'fas fa-chevron-right', 'library' => 'fa-solid' ],
				'condition' => [ 'breakpoint!' => 'none' ],
			]
		);

		$this->end_controls_section();

		// ==========================================================
		// CONTENT: RIGHT SIDE (CTA)
		// ==========================================================
		$this->start_controls_section(
			'section_cta',
			[
				'label' => esc_html__( 'Right Side / CTA', 'kv-menu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_secondary_link',
			[
				'label'        => esc_html__( 'Show Secondary Link', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'secondary_link_text',
			[
				'label'     => esc_html__( 'Secondary Link Text', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Contact Us', 'kv-menu' ),
				'condition' => [ 'show_secondary_link' => 'yes' ],
			]
		);

		$this->add_control(
			'secondary_link_url',
			[
				'label'     => esc_html__( 'Secondary Link URL', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => [ 'url' => '#' ],
				'condition' => [ 'show_secondary_link' => 'yes' ],
			]
		);

		$this->add_control(
			'show_cta',
			[
				'label'        => esc_html__( 'Show CTA Button', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'cta_type',
			[
				'label'   => esc_html__( 'CTA Type', 'kv-menu' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'button',
				'options' => [
					'button'   => esc_html__( 'Default Button', 'kv-menu' ),
					'template' => esc_html__( 'Saved Template', 'kv-menu' ),
				],
				'condition' => [ 'show_cta' => 'yes' ],
			]
		);

		$this->add_control(
			'cta_template_id',
			[
				'label'     => esc_html__( 'Select Template', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::SELECT2,
				'options'   => $this->get_elementor_templates(),
				'condition' => [
					'show_cta' => 'yes',
					'cta_type' => 'template',
				],
			]
		);

		$this->add_control(
			'cta_text',
			[
				'label'     => esc_html__( 'CTA Text', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Request A Proposal', 'kv-menu' ),
				'condition' => [
					'show_cta' => 'yes',
					'cta_type' => 'button',
				],
			]
		);

		$this->add_control(
			'cta_url',
			[
				'label'     => esc_html__( 'CTA URL', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => [ 'url' => '#' ],
				'condition' => [
					'show_cta' => 'yes',
					'cta_type' => 'button',
				],
			]
		);

		$this->add_control(
			'cta_icon',
			[
				'label'     => esc_html__( 'CTA Icon', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [ 'value' => 'fas fa-arrow-right', 'library' => 'fa-solid' ],
				'condition' => [
					'show_cta' => 'yes',
					'cta_type' => 'button',
				],
			]
		);

		$this->add_control(
			'cta_show_in_drawer',
			[
				'label'        => esc_html__( 'Show CTA inside Mobile Drawer', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'show_cta' => 'yes', 'breakpoint!' => 'none' ],
			]
		);

		$this->end_controls_section();

		// ==========================================================
		// CONTENT: MOBILE DRAWER FOOTER (Sign In / Contact Sales / info)
		// ==========================================================
		$this->start_controls_section(
			'section_drawer_footer',
			[
				'label'     => esc_html__( 'Mobile Drawer Footer', 'kv-menu' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [ 'breakpoint!' => 'none' ],
			]
		);

		$this->add_control(
			'show_drawer_footer',
			[
				'label'        => esc_html__( 'Show Footer Info', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$footer_repeater = new \Elementor\Repeater();

		$footer_repeater->add_control(
			'link_text',
			[
				'label'   => esc_html__( 'Text', 'kv-menu' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Sign In', 'kv-menu' ),
			]
		);

		$footer_repeater->add_control(
			'link_url',
			[
				'label'   => esc_html__( 'URL', 'kv-menu' ),
				'type'    => \Elementor\Controls_Manager::URL,
				'default' => [ 'url' => '#' ],
			]
		);

		$this->add_control(
			'drawer_footer_links',
			[
				'label'       => esc_html__( 'Footer Links', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $footer_repeater->get_controls(),
				'default'     => [
					[ 'link_text' => esc_html__( 'Sign In', 'kv-menu' ), 'link_url' => [ 'url' => '#' ] ],
					[ 'link_text' => esc_html__( 'Contact Sales', 'kv-menu' ), 'link_url' => [ 'url' => '#' ] ],
				],
				'title_field' => '{{{ link_text }}}',
				'condition'   => [ 'show_drawer_footer' => 'yes' ],
			]
		);

		$this->add_control(
			'drawer_footer_info',
			[
				'label'       => esc_html__( 'Info Text (e.g. phone)', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '+1.888.799.9666',
				'condition'   => [ 'show_drawer_footer' => 'yes' ],
			]
		);

		$this->add_control(
			'drawer_footer_info_url',
			[
				'label'       => esc_html__( 'Info Link (optional, e.g. tel:)', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'tel:+18887999666',
				'condition'   => [ 'show_drawer_footer' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// ==========================================================
		// CONTENT: HEADER BEHAVIOR
		// ==========================================================
		$this->start_controls_section(
			'section_behavior',
			[
				'label' => esc_html__( 'Header Behavior', 'kv-menu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'behavior_note',
			[
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Transparent / dark mode is controlled per page from the "KV Header Settings" box on each page or post. This widget defines how each skin looks.', 'kv-menu' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_control(
			'is_sticky_header',
			[
				'label'        => esc_html__( 'Enable Sticky Header', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'sticky_offset',
			[
				'label'     => esc_html__( 'Sticky Trigger Offset (px)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 80,
				'condition' => [ 'is_sticky_header' => 'yes' ],
			]
		);

		$this->add_control(
			'hide_on_scroll',
			[
				'label'        => esc_html__( 'Hide on Scroll Down', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
				'description'  => esc_html__( 'Header slides up when scrolling down and reappears when scrolling up.', 'kv-menu' ),
				'condition'    => [ 'is_sticky_header' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// ==========================================================
		// STYLE: THREE SKINS (light / dark / sticky) — generated in a loop
		// ==========================================================
		foreach ( $this->get_kv_skins() as $skin => $label ) {
			$this->start_controls_section(
				'section_skin_' . $skin,
				[
					'label' => $label,
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			if ( 'dark' === $skin ) {
				$this->add_control(
					'skin_dark_note',
					[
						'type'            => \Elementor\Controls_Manager::RAW_HTML,
						'raw'             => esc_html__( 'Leave background empty/transparent to overlay the hero. Active on pages set to "Transparent Dark".', 'kv-menu' ),
						'content_classes' => 'elementor-descriptor',
					]
				);
			}

			$this->add_control(
				'skin_' . $skin . '_bg',
				[
					'label'     => esc_html__( 'Background', 'kv-menu' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .kv-navigation-wrapper' => '--kvn-bg-' . $skin . ': {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'skin_' . $skin . '_blur',
				[
					'label'      => esc_html__( 'Backdrop Blur (px)', 'kv-menu' ),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range'      => [
						'px' => [
							'min' => 0,
							'max' => 50,
						],
					],
					'selectors'  => [
						'{{WRAPPER}} .kv-navigation-wrapper' => '--kvn-backdrop-' . $skin . ': blur({{SIZE}}{{UNIT}});',
					],
				]
			);

			if ( 'sticky' === $skin ) {
				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'skin_sticky_shadow',
						'selector' => '{{WRAPPER}} .kv-navigation-wrapper.kv-header-sticky',
					]
				);
			}

			$this->add_control(
				'skin_' . $skin . '_link',
				[
					'label'     => esc_html__( 'Menu Link Color', 'kv-menu' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .kv-navigation-wrapper' => '--kvn-link-' . $skin . ': {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'skin_' . $skin . '_link_hover',
				[
					'label'     => esc_html__( 'Menu Link Hover', 'kv-menu' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .kv-navigation-wrapper' => '--kvn-link-hover-' . $skin . ': {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'skin_' . $skin . '_link_active',
				[
					'label'     => esc_html__( 'Menu Link Active', 'kv-menu' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .kv-navigation-wrapper' => '--kvn-link-active-' . $skin . ': {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'skin_' . $skin . '_toggle',
				[
					'label'     => esc_html__( 'Toggle Icon Color', 'kv-menu' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .kv-navigation-wrapper' => '--kvn-toggle-' . $skin . ': {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'skin_' . $skin . '_secondary',
				[
					'label'     => esc_html__( 'Secondary Link Color', 'kv-menu' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .kv-navigation-wrapper' => '--kvn-secondary-' . $skin . ': {{VALUE}};',
					],
				]
			);

			$this->end_controls_section();
		}

		// ==========================================================
		// STYLE: MENU TYPOGRAPHY & SPACING
		// ==========================================================
		$this->start_controls_section(
			'section_style_menu',
			[
				'label' => esc_html__( 'Menu Items', 'kv-menu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'menu_typography',
				'selector' => '{{WRAPPER}} .kvn-menu > .menu-item > a',
			]
		);

		$this->add_responsive_control(
			'menu_item_gap',
			[
				'label'      => esc_html__( 'Gap Between Items', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'selectors'  => [
					'{{WRAPPER}} .kv-nav--desktop .kvn-menu' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_item_padding',
			[
				'label'      => esc_html__( 'Item Padding', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .kvn-menu > .menu-item > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================================
		// STYLE: DESKTOP SUBMENU (dropdown)
		// ==========================================================
		$this->start_controls_section(
			'section_style_dropdown',
			[
				'label' => esc_html__( 'Desktop Submenu', 'kv-menu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'dropdown_bg',
			[
				'label'     => esc_html__( 'Background', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .kvn-menu .sub-menu' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_item_color',
			[
				'label'     => esc_html__( 'Item Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kvn-menu .sub-menu a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_item_hover_color',
			[
				'label'     => esc_html__( 'Item Hover Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kvn-menu .sub-menu a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_item_hover_bg',
			[
				'label'     => esc_html__( 'Item Hover Background', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kvn-menu .sub-menu a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dropdown_shadow',
				'selector' => '{{WRAPPER}} .kvn-menu .sub-menu',
			]
		);

		$this->add_control(
			'dropdown_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .kvn-menu .sub-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================================
		// STYLE: CTA BUTTON
		// ==========================================================
		$this->start_controls_section(
			'section_style_cta',
			[
				'label'     => esc_html__( 'CTA Button', 'kv-menu' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_cta' => 'yes' ],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'cta_typography',
				'selector' => '{{WRAPPER}} .kv-nav-cta',
			]
		);

		$this->start_controls_tabs( 'tabs_cta' );

		$this->start_controls_tab( 'tab_cta_normal', [ 'label' => esc_html__( 'Normal', 'kv-menu' ) ] );

		$this->add_control(
			'cta_color',
			[
				'label'     => esc_html__( 'Text Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .kv-nav-cta' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'cta_bg',
				'label'    => esc_html__( 'Background', 'kv-menu' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .kv-nav-cta',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_cta_hover', [ 'label' => esc_html__( 'Hover', 'kv-menu' ) ] );

		$this->add_control(
			'cta_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .kv-nav-cta:hover' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'cta_hover_bg',
				'label'    => esc_html__( 'Background', 'kv-menu' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .kv-nav-cta:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'cta_padding',
			[
				'label'      => esc_html__( 'Padding', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24, 'unit' => 'px', 'isLinked' => false ],
				'selectors'  => [
					'{{WRAPPER}} .kv-nav-cta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'cta_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [ 'top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50, 'unit' => 'px', 'isLinked' => true ],
				'selectors'  => [
					'{{WRAPPER}} .kv-nav-cta' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================================
		// STYLE: MOBILE DRAWER
		// ==========================================================
		$this->start_controls_section(
			'section_style_drawer',
			[
				'label'     => esc_html__( 'Mobile Drawer', 'kv-menu' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [ 'breakpoint!' => 'none' ],
			]
		);

		$this->add_control(
			'drawer_bg',
			[
				'label'     => esc_html__( 'Drawer Background', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}}' => '--kvn-drawer-bg: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'drawer_link_color',
			[
				'label'     => esc_html__( 'Drawer Link Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1e293b',
				'selectors' => [ '{{WRAPPER}}' => '--kvn-drawer-link: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'drawer_link_hover',
			[
				'label'     => esc_html__( 'Drawer Link Hover', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#845CFF',
				'selectors' => [ '{{WRAPPER}}' => '--kvn-drawer-link-hover: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render a single logo <img> for a given slot.
	 */
	private function render_logo_slot( $settings, $slot ) {
		$class = 'kv-nav-logo-img kv-nav-logo--' . $slot;

		if ( 'site' === $settings['logo_source'] ) {
			$custom_logo_id = get_theme_mod( 'custom_logo' );
			$logo           = wp_get_attachment_image_src( $custom_logo_id, 'full' );
			if ( has_custom_logo() && ! empty( $logo ) ) {
				return '<img src="' . esc_url( $logo[0] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="' . esc_attr( $class ) . '">';
			}
			return '<span class="kv-nav-logo-text kv-nav-logo--' . esc_attr( $slot ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
		}

		// Custom images with fallbacks to the default logo.
		$default_url = ! empty( $settings['logo_default']['url'] ) ? $settings['logo_default']['url'] : '';
		$map         = [
			'light'  => $default_url,
			'dark'   => ! empty( $settings['logo_dark']['url'] ) ? $settings['logo_dark']['url'] : $default_url,
			'sticky' => ! empty( $settings['logo_sticky']['url'] ) ? $settings['logo_sticky']['url'] : $default_url,
		];

		$url = isset( $map[ $slot ] ) ? $map[ $slot ] : $default_url;
		if ( empty( $url ) ) {
			return '';
		}
		return '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="' . esc_attr( $class ) . '">';
	}

	/**
	 * Output the logo block (all three slots; CSS shows the active one).
	 */
	private function render_logo( $settings ) {
		if ( 'yes' !== $settings['show_logo'] ) {
			return;
		}
		$link = ! empty( $settings['logo_url']['url'] ) ? $settings['logo_url']['url'] : home_url( '/' );
		echo '<div class="kv-nav-zone kv-nav-zone--logo">';
		echo '<a href="' . esc_url( $link ) . '" class="kv-nav-logo-link">';
		echo $this->render_logo_slot( $settings, 'light' );
		echo $this->render_logo_slot( $settings, 'dark' );
		echo $this->render_logo_slot( $settings, 'sticky' );
		echo '</a>';
		echo '</div>';
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['menu'] ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="elementor-widget-empty-icon" style="text-align:center; padding:20px; border:1px dashed #ccc;">';
				echo '<i class="eicon-header" style="font-size:30px; margin-bottom:10px; display:block;"></i>';
				echo esc_html__( 'Please select a menu in the widget settings.', 'kv-menu' );
				echo '</div>';
			}
			return;
		}

		$breakpoint = ! empty( $settings['breakpoint'] ) ? $settings['breakpoint'] : 'tablet';
		$is_responsive = ( 'none' !== $breakpoint );

		$this->add_render_attribute( 'wrapper', 'class', 'kv-navigation-wrapper' );
		$this->add_render_attribute( 'wrapper', 'class', 'kv-nav--menu-' . $settings['menu_position'] );
		$this->add_render_attribute( 'wrapper', 'class', 'kv-nav--breakpoint-' . $breakpoint );
		$this->add_render_attribute( 'wrapper', 'class', 'kv-nav--width-' . $settings['container_width'] );
		if ( $is_responsive ) {
			$this->add_render_attribute( 'wrapper', 'class', 'kv-nav--anim-' . $settings['panel_animation'] );
		}
		if ( 'yes' !== $settings['show_underline'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'kv-nav--no-underline' );
		}
		if ( 'yes' === $settings['is_sticky_header'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'kv-nav-sticky-enabled' );
			$this->add_render_attribute( 'wrapper', 'data-sticky-offset', (int) $settings['sticky_offset'] );
			if ( 'yes' === $settings['hide_on_scroll'] ) {
				$this->add_render_attribute( 'wrapper', 'data-hide-on-scroll', '1' );
			}
		}

		$nav_id = 'kv-nav-panel-' . $this->get_id();
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="kv-nav-inner">

				<?php $this->render_logo( $settings ); ?>

				<div class="kv-nav-zone kv-nav-zone--menu">
					<nav id="<?php echo esc_attr( $nav_id ); ?>" class="kv-nav-panel" aria-label="<?php echo esc_attr__( 'Main navigation', 'kv-menu' ); ?>">

						<?php if ( $is_responsive ) : ?>
							<div class="kv-nav-drawer-header">
								<button class="kv-nav-back-btn" aria-label="<?php echo esc_attr__( 'Back', 'kv-menu' ); ?>" style="display:none;">
									<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
								</button>
								<button class="kv-nav-close-btn" aria-label="<?php echo esc_attr__( 'Close menu', 'kv-menu' ); ?>">
									<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
								</button>
							</div>
						<?php endif; ?>

						<div class="kv-nav-menu-container">
							<?php $this->render_menu( $settings ); ?>
						</div>

						<?php if ( $is_responsive && 'yes' === $settings['show_drawer_footer'] ) : ?>
							<div class="kv-nav-drawer-info">
								<?php if ( ! empty( $settings['drawer_footer_links'] ) ) : ?>
									<?php foreach ( $settings['drawer_footer_links'] as $index => $flink ) : ?>
										<?php if ( ! empty( $flink['link_text'] ) ) :
											$key = 'drawer_flink_' . $index;
											if ( ! empty( $flink['link_url']['url'] ) ) {
												$this->add_link_attributes( $key, $flink['link_url'] );
											}
											?>
											<?php if ( ! empty( $flink['link_url']['url'] ) ) : ?>
												<a <?php $this->print_render_attribute_string( $key ); ?> class="kv-nav-drawer-info-link"><?php echo esc_html( $flink['link_text'] ); ?></a>
											<?php else : ?>
												<span class="kv-nav-drawer-info-link"><?php echo esc_html( $flink['link_text'] ); ?></span>
											<?php endif; ?>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>

								<?php if ( ! empty( $settings['drawer_footer_info'] ) ) : ?>
									<?php if ( ! empty( $settings['drawer_footer_info_url'] ) ) : ?>
										<a href="<?php echo esc_url( $settings['drawer_footer_info_url'] ); ?>" class="kv-nav-drawer-info-text"><?php echo esc_html( $settings['drawer_footer_info'] ); ?></a>
									<?php else : ?>
										<span class="kv-nav-drawer-info-text"><?php echo esc_html( $settings['drawer_footer_info'] ); ?></span>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( $is_responsive && 'yes' === $settings['show_cta'] && 'yes' === $settings['cta_show_in_drawer'] ) :
								if ( 'button' === $settings['cta_type'] && ! empty( $settings['cta_text'] ) ) {
									$this->add_render_attribute( 'cta_drawer', 'class', 'kv-nav-cta kv-nav-cta--drawer' );
									if ( ! empty( $settings['cta_link']['url'] ) ) {
										$this->add_link_attributes( 'cta_drawer', $settings['cta_link'] );
									}
									?>
									<div class="kv-nav-drawer-cta">
										<a <?php $this->print_render_attribute_string( 'cta_drawer' ); ?>><?php echo esc_html( $settings['cta_text'] ); ?></a>
									</div>
									<?php
								} elseif ( 'template' === $settings['cta_type'] && ! empty( $settings['cta_template_id'] ) ) {
									?>
									<div class="kv-nav-drawer-cta kv-nav-drawer-cta-template">
										<?php echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $settings['cta_template_id'] ); ?>
									</div>
									<?php
								}
						endif; ?>
					</nav>
				</div>

				<div class="kv-nav-zone kv-nav-zone--cta">

					<?php if ( 'yes' === $settings['show_secondary_link'] && ! empty( $settings['secondary_link_text'] ) ) :
						$this->add_link_attributes( 'secondary_link', $settings['secondary_link_url'] );
						$this->add_render_attribute( 'secondary_link', 'class', 'kv-nav-secondary-link' );
						?>
						<a <?php $this->print_render_attribute_string( 'secondary_link' ); ?>><?php echo esc_html( $settings['secondary_link_text'] ); ?></a>
					<?php endif; ?>

					<?php if ( 'yes' === $settings['show_cta'] ) :
						if ( 'button' === $settings['cta_type'] && ! empty( $settings['cta_text'] ) ) {
							$this->add_link_attributes( 'cta', $settings['cta_url'] );
							$this->add_render_attribute( 'cta', 'class', 'kv-nav-cta' );
							?>
							<a <?php $this->print_render_attribute_string( 'cta' ); ?>>
								<span class="kv-nav-cta-text"><?php echo esc_html( $settings['cta_text'] ); ?></span>
								<?php if ( ! empty( $settings['cta_icon']['value'] ) ) : ?>
									<span class="kv-nav-cta-icon"><?php \Elementor\Icons_Manager::render_icon( $settings['cta_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
								<?php endif; ?>
							</a>
							<?php
						} elseif ( 'template' === $settings['cta_type'] && ! empty( $settings['cta_template_id'] ) ) {
							?>
							<div class="kv-nav-cta-template-wrapper">
								<?php echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $settings['cta_template_id'] ); ?>
							</div>
							<?php
						}
					endif; ?>

					<?php if ( $is_responsive ) : ?>
						<button class="kv-nav-toggle-btn" aria-expanded="false" aria-controls="<?php echo esc_attr( $nav_id ); ?>" 
aria-label="<?php echo esc_attr__( 'Open menu', 'kv-menu' ); ?>">
							<span class="kv-nav-hamburger"><span></span><span></span><span></span></span>
						</button>
					<?php endif; ?>
					
					<!-- DEBUG: breakpoint=<?php echo esc_attr($breakpoint); ?>, is_responsive=<?php echo $is_responsive ? 'true' : 'false'; ?> -->
					<?php if ( ! $is_responsive ) : ?>
						<div style="background:red; color:white; padding: 10px; position:fixed; top:50px; right:10px; z-index: 999999;">
							DEBUG: BREAKPOINT IS NONE!
						</div>
					<?php endif; ?>
				</div>

			</div>
			<div class="kv-nav-overlay" tabindex="-1" aria-hidden="true"></div>
		</div>
		<?php
	}

	/**
	 * Render the WP menu with submenu indicators + mega-menu panels.
	 */
	private function render_menu( $settings ) {
		ob_start();
		\Elementor\Icons_Manager::render_icon( $settings['submenu_indicator_icon'], [ 'aria-hidden' => 'true' ] );
		$desktop_icon = ob_get_clean();
		if ( '' === trim( (string) $desktop_icon ) ) {
			$desktop_icon = '<i class="fas fa-angle-down" aria-hidden="true"></i>';
		}

		$mobile_icon = '';
		if ( ! empty( $settings['submenu_indicator_icon_mobile']['value'] ) ) {
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['submenu_indicator_icon_mobile'], [ 'aria-hidden' => 'true' ] );
			$mobile_icon = ob_get_clean();
		}
		if ( '' === trim( (string) $mobile_icon ) ) {
			$mobile_icon = '<i class="fas fa-chevron-right" aria-hidden="true"></i>';
		}

		$filter_callback = function ( $item_output, $item, $depth, $args ) use ( $desktop_icon, $mobile_icon ) {
			$has_children = in_array( 'menu-item-has-children', $item->classes, true );
			$mega_enabled = get_post_meta( $item->ID, '_kv_menu_mega_enabled', true );
			$has_mega     = ( 0 === $depth && 'yes' === $mega_enabled );

			if ( $has_children || $has_mega ) {
				$indicator_html  = '<span class="kv-nav-sub-indicator kv-nav-sub-indicator-desktop">' . $desktop_icon . '</span>';
				$indicator_html .= '<span class="kv-nav-sub-indicator kv-nav-sub-indicator-mobile">' . $mobile_icon . '</span>';

				$pos = strrpos( $item_output, '</a>' );
				if ( false !== $pos ) {
					$item_output = substr_replace( $item_output, $indicator_html, $pos, 0 );
				} else {
					$item_output .= $indicator_html;
				}
			}

			if ( $has_mega ) {
				$template_id = absint( get_post_meta( $item->ID, '_kv_menu_mega_template_id', true ) );
				if ( ! empty( $template_id ) ) {
					$mega_content = '';

					// Prefer native structured mega data; fall back to Elementor template.
					$native = class_exists( 'KV_Mega_Builder' ) ? \KV_Mega_Builder::get_data( $template_id ) : null;

					if ( $native ) {
						$mega_content = $this->render_native_mega( $native, $item->ID );
					} else {
						if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
							$css_file = new \Elementor\Core\Files\CSS\Post( $template_id );
							$css_file->enqueue();
						}

						$mega_content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id );

						if ( empty( $mega_content ) && ( current_user_can( 'edit_theme_options' ) || \Elementor\Plugin::$instance->editor->is_edit_mode() ) ) {
							$mega_content  = '<div class="kv-nav-mega-empty" style="padding:24px; text-align:center; background:#f8fafc; border:2px dashed #cbd5e1; border-radius:8px;">';
							$mega_content .= '<p style="margin:0 0 10px; font-weight:600; color:#64748b;">' . esc_html__( 'Mega Menu is empty. Add content in the KV Mega Menu Content box.', 'kv-menu' ) . '</p>';
							$mega_content .= '<a href="' . esc_url( admin_url( 'post.php?post=' . $template_id . '&action=edit' ) ) . '" target="_blank" style="background:#845CFF; color:#fff; text-decoration:none; padding:8px 16px; border-radius:6px; display:inline-block;">' . esc_html__( 'Edit Mega Menu', 'kv-menu' ) . '</a>';
							$mega_content .= '</div>';
						}
					}

					if ( ! empty( $mega_content ) ) {
						$width_type = get_post_meta( $item->ID, '_kv_menu_mega_width_type', true );
						$custom_w   = get_post_meta( $item->ID, '_kv_menu_mega_custom_width', true );
						$align      = get_post_meta( $item->ID, '_kv_menu_mega_alignment', true );

						$width_type = empty( $width_type ) ? 'full' : $width_type;
						$custom_w   = empty( $custom_w ) ? '1000px' : $custom_w;
						$align      = empty( $align ) ? 'center' : $align;

						$panel_classes = [ 'kv-nav-mega-panel' ];
						$panel_style   = '';

						if ( 'custom' === $width_type ) {
							$panel_classes[] = 'kv-nav-mega-panel--custom-width';
							$panel_classes[] = 'kv-nav-mega-panel--align-' . $align;
							$clean_width     = trim( $custom_w );
							if ( ! empty( $clean_width ) ) {
								if ( is_numeric( $clean_width ) ) {
									$clean_width .= 'px';
								}
								$panel_style = ' style="max-width:' . esc_attr( $clean_width ) . '; width:100%;"';
							}
						} else {
							$panel_classes[] = 'kv-nav-mega-panel--full-width';
						}

						$item_output .= '<div class="' . esc_attr( implode( ' ', $panel_classes ) ) . '"' . $panel_style . '>' . $mega_content . '</div>';
					}
				}
			}

			return $item_output;
		};

		add_filter( 'walker_nav_menu_start_el', $filter_callback, 10, 4 );

		wp_nav_menu(
			[
				'menu'        => $settings['menu'],
				'container'   => false,
				'menu_class'  => 'kvn-menu',
				'depth'       => 3,
				'fallback_cb' => '__return_empty_string',
			]
		);

		remove_filter( 'walker_nav_menu_start_el', $filter_callback, 10 );
	}

	/**
	 * Render native structured mega content (tabbed on desktop, drilldown on mobile).
	 *
	 * @param array $data    Structured mega data.
	 * @param int   $item_id Menu item id (for unique DOM ids).
	 * @return string HTML.
	 */
	/**
	 * Icon markup: <img> for image/SVG URLs, otherwise an <i> font icon.
	 */
	private static function icon_markup( $value ) {
		if ( '' === $value ) {
			return '';
		}
		if ( preg_match( '#^https?://#', $value ) ) {
			return '<img class="kv-mega-icon-img" src="' . esc_url( $value ) . '" alt="" aria-hidden="true" />';
		}
		return '<i class="' . esc_attr( $value ) . '" aria-hidden="true"></i>';
	}

	private function render_native_mega( $data, $item_id ) {
		$tabs = isset( $data['tabs'] ) ? array_values( $data['tabs'] ) : [];
		if ( empty( $tabs ) ) {
			return '';
		}
		$id   = 'kv-mega-' . $item_id;
		$html = '<div class="kv-mega" id="' . esc_attr( $id ) . '">';

		// Sidebar (tab list + bottom).
		$html .= '<div class="kv-mega-sidebar"><ul class="kv-mega-tablist">';
		foreach ( $tabs as $i => $tab ) {
			$active = ( 0 === $i ) ? ' is-active' : '';
			$title  = isset( $tab['tab_title'] ) ? $tab['tab_title'] : '';
			$html  .= '<li class="kv-mega-tab' . $active . '" data-target="' . esc_attr( $id . '-' . $i ) . '" role="button" tabindex="0">';
			$html  .= '<span class="kv-mega-tab-label">' . esc_html( $title ) . '</span>';
			$html  .= '<i class="fas fa-chevron-right kv-mega-tab-arrow" aria-hidden="true"></i>';
			$html  .= '</li>';
		}
		$html .= '</ul>';

		$b_title = isset( $data['bottom_title'] ) ? $data['bottom_title'] : '';
		$b_info  = isset( $data['bottom_info'] ) ? $data['bottom_info'] : '';
		if ( '' !== $b_title || '' !== $b_info ) {
			$html .= '<div class="kv-mega-bottom">';
			if ( '' !== $b_title ) {
				$html .= '<strong>' . esc_html( $b_title ) . '</strong>';
			}
			if ( '' !== $b_info ) {
				$html .= '<span>' . esc_html( $b_info ) . '</span>';
			}
			$html .= '</div>';
		}
		$html .= '</div>'; // sidebar

		// Content panes.
		$html .= '<div class="kv-mega-content">';
		foreach ( $tabs as $i => $tab ) {
			$current = ( 0 === $i ) ? ' kv-mega-pane--current' : '';
			$open    = '';
			$t_title = isset( $tab['tab_title'] ) ? $tab['tab_title'] : '';
			$html   .= '<div class="kv-mega-pane' . $current . $open . '" id="' . esc_attr( $id . '-' . $i ) . '">';
			$html   .= '<button type="button" class="kv-mega-acc-head"><span class="kv-mega-acc-title">' . esc_html( $t_title ) . '</span><i class="fas fa-chevron-down kv-mega-acc-arrow" aria-hidden="true"></i></button>';
			$html   .= '<div class="kv-mega-pane-inner">';
			$html   .= '<div class="kv-mega-pane-left">';

			$main = isset( $tab['main_title'] ) ? $tab['main_title'] : '';
			$sub  = isset( $tab['subtitle'] ) ? $tab['subtitle'] : '';
			if ( '' !== $main || '' !== $sub ) {
				$html .= '<div class="kv-mega-pane-head">';
				if ( '' !== $main ) {
					$html .= '<h3>' . esc_html( $main ) . '</h3>';
				}
				if ( '' !== $sub ) {
					$html .= '<p>' . esc_html( $sub ) . '</p>';
				}
				$html .= '</div>';
			}

			// Links.
			$links = ( isset( $tab['links'] ) && is_array( $tab['links'] ) ) ? $tab['links'] : [];
			if ( ! empty( $links ) ) {
				$html .= '<ul class="kv-mega-links">';
				foreach ( $links as $link ) {
					$l_url   = ! empty( $link['url'] ) ? $link['url'] : '#';
					$l_icon  = isset( $link['icon'] ) ? $link['icon'] : '';
					$l_title = isset( $link['title'] ) ? $link['title'] : '';
					$html   .= '<li><a href="' . esc_url( $l_url ) . '">';
					if ( '' !== $l_icon ) {
						$html .= self::icon_markup( $l_icon );
					}
					$html .= '<span>' . esc_html( $l_title ) . '</span></a></li>';
				}
				$html .= '</ul>';
			}
			$html .= '</div>'; // pane-left

			// Media column (featured image + promo).
			$featured = isset( $tab['featured_image'] ) ? $tab['featured_image'] : '';
			$promo_on = ( isset( $tab['promo_show'] ) && 'yes' === $tab['promo_show'] );
			if ( '' !== $featured || $promo_on ) {
				$html .= '<div class="kv-mega-media">';
				if ( '' !== $featured ) {
					$f_url = ! empty( $tab['featured_url'] ) ? $tab['featured_url'] : '';
					$img   = '<img src="' . esc_url( $featured ) . '" alt="' . esc_attr( $main ) . '" />';
					$html .= ( '' !== $f_url ) ? '<a href="' . esc_url( $f_url ) . '" class="kv-mega-featured">' . $img . '</a>' : '<div class="kv-mega-featured">' . $img . '</div>';
				}
				if ( $promo_on ) {
					$html .= '<div class="kv-mega-promo">';
					$html .= '<div class="kv-mega-promo-head">';
					if ( ! empty( $tab['promo_icon'] ) ) {
						$html .= self::icon_markup( $tab['promo_icon'] );
					}
					if ( ! empty( $tab['promo_badge'] ) ) {
						$html .= '<span class="kv-mega-promo-badge">' . esc_html( $tab['promo_badge'] ) . '</span>';
					}
					$html .= '</div>';
					if ( ! empty( $tab['promo_title'] ) ) {
						$html .= '<h4>' . esc_html( $tab['promo_title'] ) . '</h4>';
					}
					if ( ! empty( $tab['promo_desc'] ) ) {
						$html .= '<p>' . esc_html( $tab['promo_desc'] ) . '</p>';
					}
					if ( ! empty( $tab['promo_link_text'] ) ) {
						$p_url = ! empty( $tab['promo_link_url'] ) ? $tab['promo_link_url'] : '#';
						$html .= '<a href="' . esc_url( $p_url ) . '" class="kv-mega-promo-link">' . esc_html( $tab['promo_link_text'] ) . '</a>';
					}
					$html .= '</div>';
				}
				$html .= '</div>'; // media
			}

			$html .= '</div>'; // pane-inner
			$html .= '</div>'; // pane
		}
		$html .= '</div>'; // content
		$html .= '</div>'; // kv-mega

		return $html;
	}
}
