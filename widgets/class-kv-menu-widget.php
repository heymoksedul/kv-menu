<?php
/**
 * KV Menu Elementor Widget.
 *
 * @package KV_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class KV_Menu_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'kv-menu';
	}

	public function get_title() {
		return esc_html__( 'KV Menu', 'kv-menu' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	public function get_keywords() {
		return [ 'menu', 'navigation', 'responsive', 'hamburger', 'kv' ];
	}

	private function get_available_menus() {
		$menus = wp_get_nav_menus();
		$options = [ '' => esc_html__( 'Select Menu', 'kv-menu' ) ];
		foreach ( $menus as $menu ) {
			$options[ $menu->slug ] = $menu->name;
		}
		return $options;
	}

	protected function register_controls() {

		// ==========================================
		// CONTENT TAB
		// ==========================================

		$this->start_controls_section(
			'section_logo',
			[
				'label' => esc_html__( 'Logo Configuration', 'kv-menu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_logo',
			[
				'label'        => esc_html__( 'Show Logo', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'kv-menu' ),
				'label_off'    => esc_html__( 'No', 'kv-menu' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'logo_type',
			[
				'label'     => esc_html__( 'Logo Type', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => [
					'site_logo'    => esc_html__( 'Site Logo (Customizer)', 'kv-menu' ),
					'custom_image' => esc_html__( 'Custom Image', 'kv-menu' ),
				],
				'default'   => 'site_logo',
				'condition' => [
					'show_logo' => 'yes',
				],
			]
		);

		$this->add_control(
			'logo_image',
			[
				'label'     => esc_html__( 'Choose Logo', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'show_logo' => 'yes',
					'logo_type' => 'custom_image',
				],
			]
		);

		$this->add_control(
			'logo_url',
			[
				'label'       => esc_html__( 'Logo Link URL', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'default'     => [
					'url' => '',
				],
				'condition' => [
					'show_logo' => 'yes',
				],
			]
		);

		$this->end_controls_section();



		$this->start_controls_section(
			'section_menu',
			[
				'label' => esc_html__( 'Menu Configuration', 'kv-menu' ),
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
			'layout',
			[
				'label'   => esc_html__( 'Layout', 'kv-menu' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'horizontal' => esc_html__( 'Horizontal', 'kv-menu' ),
					'vertical'   => esc_html__( 'Vertical', 'kv-menu' ),
				],
				'default' => 'horizontal',
			]
		);

		$this->add_control(
			'breakpoint',
			[
				'label'   => esc_html__( 'Breakpoint (Mobile View)', 'kv-menu' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options'   => [
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
				'condition' => [
					'breakpoint!' => 'none',
				],
			]
		);

		$this->add_control(
			'submenu_indicator_icon',
			[
				'label'       => esc_html__( 'Desktop Indicator Icon', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-angle-down',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'submenu_indicator_icon_mobile',
			[
				'label'       => esc_html__( 'Mobile Indicator Icon', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				],
				'condition'   => [
					'breakpoint!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label'     => esc_html__( 'Alignment', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'kv-menu' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'     => [
						'title' => esc_html__( 'Center', 'kv-menu' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'   => [
						'title' => esc_html__( 'Right', 'kv-menu' ),
						'icon'  => 'eicon-text-align-right',
					],
					'space-between' => [
						'title' => esc_html__( 'Justified', 'kv-menu' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'   => 'flex-start',
				'selectors' => [
					'{{WRAPPER}} .kv-menu--layout-horizontal .kv-menu-list' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .kv-menu--layout-vertical .kv-menu-list'   => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'show_underline',
			[
				'label'        => esc_html__( 'Hover/Active Underline', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'kv-menu' ),
				'label_off'    => esc_html__( 'Hide', 'kv-menu' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_toggle',
			[
				'label'     => esc_html__( 'Toggle Button (Mobile)', 'kv-menu' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'breakpoint!' => 'none',
				],
			]
		);

		$this->add_control(
			'toggle_icon_style',
			[
				'label'   => esc_html__( 'Toggle Button Style', 'kv-menu' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'animated' => esc_html__( 'Animated CSS Bars (Recommended)', 'kv-menu' ),
					'custom'   => esc_html__( 'Custom Icons', 'kv-menu' ),
				],
				'default' => 'animated',
			]
		);

		$this->add_control(
			'hamburger_icon',
			[
				'label'       => esc_html__( 'Hamburger Icon', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-bars',
					'library' => 'fa-solid',
				],
				'condition'   => [
					'toggle_icon_style' => 'custom',
				],
			]
		);

		$this->add_control(
			'close_icon',
			[
				'label'       => esc_html__( 'Close Icon', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-times',
					'library' => 'fa-solid',
				],
				'condition'   => [
					'toggle_icon_style' => 'custom',
				],
			]
		);

		$this->add_control(
			'toggle_text',
			[
				'label'       => esc_html__( 'Toggle Label', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Menu', 'kv-menu' ),
				'default'     => '',
				'condition'   => [
					'toggle_icon_style' => 'custom',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_align',
			[
				'label'     => esc_html__( 'Toggle Button Alignment', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'kv-menu' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'kv-menu' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'kv-menu' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'right',
				'selectors' => [
					'{{WRAPPER}} .kv-menu-toggle-container' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_mobile_footer',
			[
				'label'     => esc_html__( 'Mobile Panel Footer', 'kv-menu' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'breakpoint!' => 'none',
				],
			]
		);

		$this->add_control(
			'show_mobile_footer',
			[
				'label'        => esc_html__( 'Show Footer Info', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'kv-menu' ),
				'label_off'    => esc_html__( 'Hide', 'kv-menu' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'footer_link_1_text',
			[
				'label'     => esc_html__( 'Link 1 Text', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Sign In', 'kv-menu' ),
				'condition' => [
					'show_mobile_footer' => 'yes',
				],
			]
		);

		$this->add_control(
			'footer_link_1_url',
			[
				'label'     => esc_html__( 'Link 1 URL', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => [
					'url' => '#',
				],
				'condition' => [
					'show_mobile_footer' => 'yes',
				],
			]
		);

		$this->add_control(
			'footer_link_2_text',
			[
				'label'     => esc_html__( 'Link 2 Text', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Contact Sales', 'kv-menu' ),
				'condition' => [
					'show_mobile_footer' => 'yes',
				],
			]
		);

		$this->add_control(
			'footer_link_2_url',
			[
				'label'     => esc_html__( 'Link 2 URL', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => [
					'url' => '#',
				],
				'condition' => [
					'show_mobile_footer' => 'yes',
				],
			]
		);

		$this->add_control(
			'footer_info_text',
			[
				'label'     => esc_html__( 'Info/Phone Text', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( '+1.888.799.9666', 'kv-menu' ),
				'condition' => [
					'show_mobile_footer' => 'yes',
				],
			]
		);

		$this->add_control(
			'footer_info_url',
			[
				'label'     => esc_html__( 'Info/Phone URL', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => 'tel:+18887999666',
				'condition' => [
					'show_mobile_footer' => 'yes',
				],
			]
		);

		$this->add_control(
			'footer_cta_text',
			[
				'label'     => esc_html__( 'CTA Button Text', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Sign Up Free', 'kv-menu' ),
				'condition' => [
					'show_mobile_footer' => 'yes',
				],
			]
		);

		$this->add_control(
			'footer_cta_url',
			[
				'label'     => esc_html__( 'CTA Button URL', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => [
					'url' => '#',
				],
				'condition' => [
					'show_mobile_footer' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLE TAB: MENU ITEMS (DESKTOP)
		// ==========================================

		// ==========================================
		// STYLE TAB: LOGO STYLE
		// ==========================================

		$this->start_controls_section(
			'section_style_logo',
			[
				'label'     => esc_html__( 'Mobile Logo', 'kv-menu' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_logo' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'logo_height',
			[
				'label'      => esc_html__( 'Logo Height', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-logo-img' => 'height: {{SIZE}}{{UNIT}}; width: auto;',
					'{{WRAPPER}} .kv-menu-logo-text' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'logo_max_width',
			[
				'label'      => esc_html__( 'Logo Max Width', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 20,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-logo-img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'logo_spacing',
			[
				'label'      => esc_html__( 'Logo Margin / Spacing', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-logo-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLE TAB: MENU CONTAINER
		// ==========================================

		$this->start_controls_section(
			'section_style_menu_container',
			[
				'label' => esc_html__( 'Main Menu Bar', 'kv-menu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'menu_container_bg',
			[
				'label'     => esc_html__( 'Background Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-wrapper' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .kv-menu-main-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_container_padding',
			[
				'label'      => esc_html__( 'Padding', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .kv-menu-main-bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'menu_container_border',
				'selector' => '{{WRAPPER}} .kv-menu-wrapper, {{WRAPPER}} .kv-menu-main-bar',
			]
		);

		$this->add_control(
			'menu_container_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .kv-menu-main-bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'menu_container_box_shadow',
				'selector' => '{{WRAPPER}} .kv-menu-wrapper, {{WRAPPER}} .kv-menu-main-bar',
			]
		);

		$this->end_controls_section();



		$this->start_controls_section(
			'section_style_main_menu',
			[
				'label' => esc_html__( 'Desktop Menu Items', 'kv-menu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_desktop_items_style',
			[
				'label'     => esc_html__( 'Menu Item Links', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'menu_typography',
				'selector' => '{{WRAPPER}} .kv-menu-list > .menu-item > a',
			]
		);

		$this->start_controls_tabs( 'tabs_menu_item_style' );

		$this->start_controls_tab(
			'tab_menu_item_normal',
			[
				'label' => esc_html__( 'Normal', 'kv-menu' ),
			]
		);

		$this->add_control(
			'menu_item_color',
			[
				'label'     => esc_html__( 'Text Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-list > .menu-item > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_item_bg',
			[
				'label'     => esc_html__( 'Background Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-list > .menu-item > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_hover',
			[
				'label' => esc_html__( 'Hover', 'kv-menu' ),
			]
		);

		$this->add_control(
			'menu_item_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-list > .menu-item:hover > a, {{WRAPPER}} .kv-menu-list > .menu-item > a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_item_hover_bg',
			[
				'label'     => esc_html__( 'Background Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-list > .menu-item:hover > a, {{WRAPPER}} .kv-menu-list > .menu-item > a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_active',
			[
				'label' => esc_html__( 'Active', 'kv-menu' ),
			]
		);

		$this->add_control(
			'menu_item_active_color',
			[
				'label'     => esc_html__( 'Text Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-list > .menu-item.current-menu-item > a, {{WRAPPER}} .kv-menu-list > .menu-item.current-menu-ancestor > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_item_active_bg',
			[
				'label'     => esc_html__( 'Background Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-list > .menu-item.current-menu-item > a, {{WRAPPER}} .kv-menu-list > .menu-item.current-menu-ancestor > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'menu_item_padding',
			[
				'label'      => esc_html__( 'Padding', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-list > .menu-item > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'menu_item_margin',
			[
				'label'      => esc_html__( 'Spacing Between Items', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu--layout-horizontal .kv-menu-list > .menu-item:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .kv-menu--layout-vertical .kv-menu-list > .menu-item:not(:last-child)'   => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'menu_item_border',
				'selector' => '{{WRAPPER}} .kv-menu-list > .menu-item > a',
			]
		);

		$this->add_control(
			'menu_item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-list > .menu-item > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'desktop_indicator_heading',
			[
				'label'     => esc_html__( 'Submenu Indicator', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'desktop_indicator_size',
			[
				'label'      => esc_html__( 'Indicator Size', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-sub-indicator-desktop' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'desktop_indicator_spacing',
			[
				'label'      => esc_html__( 'Spacing (Margin Left)', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-sub-indicator-desktop' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_desktop_indicator_style' );

		$this->start_controls_tab(
			'tab_desktop_indicator_normal',
			[
				'label' => esc_html__( 'Normal', 'kv-menu' ),
			]
		);

		$this->add_control(
			'desktop_indicator_color',
			[
				'label'     => esc_html__( 'Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-sub-indicator-desktop' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_desktop_indicator_hover',
			[
				'label' => esc_html__( 'Hover', 'kv-menu' ),
			]
		);

		$this->add_control(
			'desktop_indicator_hover_color',
			[
				'label'     => esc_html__( 'Color (Hover)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-list > .menu-item:hover > a .kv-menu-sub-indicator-desktop' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}} .kv-menu-list > .menu-item > a:hover .kv-menu-sub-indicator-desktop' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ==========================================
		// STYLE TAB: SUBMENU (DROPDOWN)
		// ==========================================

		$this->start_controls_section(
			'section_style_dropdown',
			[
				'label' => esc_html__( 'Desktop Submenus', 'kv-menu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_dropdown_container_style',
			[
				'label'     => esc_html__( 'Submenu Container', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'dropdown_typography',
				'selector' => '{{WRAPPER}} .kv-menu-list .sub-menu a',
			]
		);

		$this->add_control(
			'dropdown_bg',
			[
				'label'     => esc_html__( 'Dropdown Container Background', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.95)',
				'selectors' => [
					'{{WRAPPER}} .kv-menu-list .sub-menu' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_glassmorphism',
			[
				'label'        => esc_html__( 'Enable Glassmorphism Overlay', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'kv-menu' ),
				'label_off'    => esc_html__( 'No', 'kv-menu' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'dropdown_blur',
			[
				'label'     => esc_html__( 'Glass Backdrop Blur Amount', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 30,
					],
				],
				'default'   => [
					'size' => 10,
					'unit' => 'px',
				],
				'condition' => [
					'dropdown_glassmorphism' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .kv-menu-list .sub-menu' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);',
				],
			]
		);

		$this->add_control(
			'heading_dropdown_items_style',
			[
				'label'     => esc_html__( 'Submenu Item Links', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_dropdown_item_style' );

		$this->start_controls_tab(
			'tab_dropdown_item_normal',
			[
				'label' => esc_html__( 'Normal', 'kv-menu' ),
			]
		);

		$this->add_control(
			'dropdown_item_color',
			[
				'label'     => esc_html__( 'Text Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-list .sub-menu a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_item_bg',
			[
				'label'     => esc_html__( 'Item Background', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-list .sub-menu a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_item_hover',
			[
				'label' => esc_html__( 'Hover', 'kv-menu' ),
			]
		);

		$this->add_control(
			'dropdown_item_hover_color',
			[
				'label'     => esc_html__( 'Text Color (Hover)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-list .sub-menu li:hover > a, {{WRAPPER}} .kv-menu-list .sub-menu a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_item_hover_bg',
			[
				'label'     => esc_html__( 'Item Background (Hover)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-list .sub-menu li:hover > a, {{WRAPPER}} .kv-menu-list .sub-menu a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_item_active',
			[
				'label' => esc_html__( 'Active', 'kv-menu' ),
			]
		);

		$this->add_control(
			'dropdown_item_active_color',
			[
				'label'     => esc_html__( 'Text Color (Active)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-list .sub-menu .current-menu-item > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_item_active_bg',
			[
				'label'     => esc_html__( 'Item Background (Active)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-list .sub-menu .current-menu-item > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'dropdown_padding',
			[
				'label'      => esc_html__( 'Dropdown Container Padding', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-list .sub-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'dropdown_item_padding',
			[
				'label'      => esc_html__( 'Dropdown Item Link Padding', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-list .sub-menu a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'dropdown_border',
				'selector' => '{{WRAPPER}} .kv-menu-list .sub-menu',
			]
		);

		$this->add_control(
			'dropdown_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-list .sub-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dropdown_box_shadow',
				'selector' => '{{WRAPPER}} .kv-menu-list .sub-menu',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLE TAB: PANEL CLOSE BUTTON (MOBILE)
		// ==========================================

		$this->start_controls_section(
			'section_style_close_button',
			[
				'label'     => esc_html__( 'Mobile Panel Close Button', 'kv-menu' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'breakpoint!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_size',
			[
				'label'     => esc_html__( 'Icon Size', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .kv-menu-close-btn' => 'font-size: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .kv-menu-close-btn i' => 'font-size: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .kv-menu-close-btn svg' => 'width: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .kv-menu-close-icon svg' => 'height: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_close_button_style' );

		$this->start_controls_tab(
			'tab_close_button_normal',
			[
				'label' => esc_html__( 'Normal', 'kv-menu' ),
			]
		);

		$this->add_control(
			'close_button_color',
			[
				'label'     => esc_html__( 'Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-close-btn' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .kv-menu-close-btn i' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .kv-menu-close-btn svg' => 'stroke: {{VALUE}} !important;',
					'{{WRAPPER}} .kv-menu-close-icon svg' => 'fill: {{VALUE}} !important;',
					'{{WRAPPER}} .kv-menu-close-btn svg path' => 'stroke: {{VALUE}} !important;',
					'{{WRAPPER}} .kv-menu-close-icon svg path' => 'fill: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'close_button_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-close-btn' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_close_button_hover',
			[
				'label' => esc_html__( 'Hover', 'kv-menu' ),
			]
		);

		$this->add_control(
			'close_button_hover_color',
			[
				'label'     => esc_html__( 'Color (Hover)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-close-btn:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .kv-menu-close-btn:hover i' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .kv-menu-close-btn:hover svg' => 'stroke: {{VALUE}} !important;',
					'{{WRAPPER}} .kv-menu-close-icon:hover svg' => 'fill: {{VALUE}} !important;',
					'{{WRAPPER}} .kv-menu-close-btn:hover svg path' => 'stroke: {{VALUE}} !important;',
					'{{WRAPPER}} .kv-menu-close-icon:hover svg path' => 'fill: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'close_button_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color (Hover)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-close-btn:hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'close_button_padding',
			[
				'label'      => esc_html__( 'Padding', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-close-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'close_button_border',
				'selector' => '{{WRAPPER}} .kv-menu-close-btn',
			]
		);

		$this->add_control(
			'close_button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-close-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'close_button_box_shadow',
				'selector' => '{{WRAPPER}} .kv-menu-close-btn',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLE TAB: TOGGLE BUTTON (MOBILE)
		// ==========================================

		$this->start_controls_section(
			'section_style_toggle',
			[
				'label'     => esc_html__( 'Mobile Toggle Button', 'kv-menu' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'breakpoint!' => 'none',
				],
			]
		);

		$this->add_control(
			'toggle_size',
			[
				'label'     => esc_html__( 'Icon Size', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .kv-menu-toggle-btn' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_toggle_style' );

		$this->start_controls_tab(
			'tab_toggle_normal',
			[
				'label' => esc_html__( 'Normal', 'kv-menu' ),
			]
		);

		$this->add_control(
			'toggle_color',
			[
				'label'     => esc_html__( 'Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-toggle-btn' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}} .kv-menu-toggle-btn svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .kv-menu-toggle-btn svg path' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .kv-menu-hamburger-animated span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-toggle-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_toggle_hover',
			[
				'label' => esc_html__( 'Hover', 'kv-menu' ),
			]
		);

		$this->add_control(
			'toggle_hover_color',
			[
				'label'     => esc_html__( 'Color (Hover)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-toggle-btn:hover' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}} .kv-menu-toggle-btn:hover svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .kv-menu-toggle-btn:hover svg path' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .kv-menu-hamburger-animated:hover span' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .kv-menu-toggle-btn:hover .kv-menu-hamburger-animated span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color (Hover)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-toggle-btn:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'toggle_padding',
			[
				'label'      => esc_html__( 'Padding', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-toggle-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'toggle_border',
				'selector' => '{{WRAPPER}} .kv-menu-toggle-btn',
			]
		);

		$this->add_control(
			'toggle_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-toggle-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLE TAB: MOBILE PANEL
		// ==========================================

		$this->start_controls_section(
			'section_style_mobile_panel',
			[
				'label'     => esc_html__( 'Mobile Panel Drawer', 'kv-menu' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'breakpoint!' => 'none',
				],
			]
		);

		// Group 1: Drawer Panel Styling
		$this->add_control(
			'heading_mobile_drawer_panel_style',
			[
				'label'     => esc_html__( 'Drawer Panel', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'mobile_panel_width',
			[
				'label'      => esc_html__( 'Panel Width', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%', 'vw' ],
				'range'      => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'vw' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => 320,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-nav' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'mobile_panel_max_width',
			[
				'label'      => esc_html__( 'Panel Max Width', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%', 'vw' ],
				'range'      => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'vw' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-nav' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'mobile_panel_bg',
			[
				'label'     => esc_html__( 'Panel Background', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.98)',
				'selectors' => [
					'{{WRAPPER}}' => '--kv-mobile-panel-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'mobile_panel_padding',
			[
				'label'      => esc_html__( 'Panel Padding', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'mobile_panel_glassmorphism',
			[
				'label'        => esc_html__( 'Enable Glassmorphism', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'kv-menu' ),
				'label_off'    => esc_html__( 'No', 'kv-menu' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'mobile_panel_blur',
			[
				'label'     => esc_html__( 'Backdrop Blur Amount', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 30,
					],
				],
				'default'   => [
					'size' => 15,
					'unit' => 'px',
				],
				'condition' => [
					'mobile_panel_glassmorphism' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .kv-menu-nav' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'mobile_panel_border',
				'selector' => '{{WRAPPER}} .kv-menu-nav',
			]
		);

		$this->add_control(
			'mobile_panel_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-nav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'mobile_panel_box_shadow',
				'selector' => '{{WRAPPER}} .kv-menu-nav',
			]
		);

		// Group 2: Menu Items Links
		$this->add_control(
			'heading_mobile_drawer_items_style',
			[
				'label'     => esc_html__( 'Menu Items Links', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'mobile_panel_typography',
				'selector' => '{{WRAPPER}} .kv-menu-nav .kv-menu-list a',
			]
		);

		$this->start_controls_tabs( 'tabs_mobile_item_style' );

		$this->start_controls_tab(
			'tab_mobile_item_normal',
			[
				'label' => esc_html__( 'Normal', 'kv-menu' ),
			]
		);

		$this->add_control(
			'mobile_item_color',
			[
				'label'     => esc_html__( 'Text Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--kv-mobile-item-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mobile_item_bg',
			[
				'label'     => esc_html__( 'Background Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--kv-mobile-item-bg: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_mobile_item_hover',
			[
				'label' => esc_html__( 'Hover', 'kv-menu' ),
			]
		);

		$this->add_control(
			'mobile_item_hover_color',
			[
				'label'     => esc_html__( 'Text Color (Hover)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--kv-mobile-item-hover-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mobile_item_hover_bg',
			[
				'label'     => esc_html__( 'Background Color (Hover)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--kv-mobile-item-hover-bg: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_mobile_item_active',
			[
				'label' => esc_html__( 'Active', 'kv-menu' ),
			]
		);

		$this->add_control(
			'mobile_item_active_color',
			[
				'label'     => esc_html__( 'Text Color (Active)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--kv-mobile-item-active-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mobile_item_active_bg',
			[
				'label'     => esc_html__( 'Background Color (Active)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--kv-mobile-item-active-bg: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'mobile_item_padding',
			[
				'label'      => esc_html__( 'Item Link Padding', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-nav .kv-menu-list a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		// Group 3: Submenu Indicators
		$this->add_control(
			'mobile_indicator_heading',
			[
				'label'     => esc_html__( 'Submenu Indicators', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'mobile_indicator_size',
			[
				'label'      => esc_html__( 'Indicator Size', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-sub-indicator-mobile' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'mobile_indicator_spacing',
			[
				'label'      => esc_html__( 'Spacing (Margin Left)', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-sub-indicator-mobile' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_mobile_indicator_style' );

		$this->start_controls_tab(
			'tab_mobile_indicator_normal',
			[
				'label' => esc_html__( 'Normal', 'kv-menu' ),
			]
		);

		$this->add_control(
			'mobile_indicator_color',
			[
				'label'     => esc_html__( 'Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--kv-mobile-indicator-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_mobile_indicator_hover',
			[
				'label' => esc_html__( 'Hover', 'kv-menu' ),
			]
		);

		$this->add_control(
			'mobile_indicator_hover_color',
			[
				'label'     => esc_html__( 'Color (Hover)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--kv-mobile-indicator-hover-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_mobile_footer',
			[
				'label'     => esc_html__( 'Mobile Panel Footer', 'kv-menu' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'breakpoint!' => 'none',
					'show_mobile_footer' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'mobile_footer_padding',
			[
				'label'      => esc_html__( 'Footer Padding', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-mobile-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'mobile_footer_link_color',
			[
				'label'     => esc_html__( 'Links Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-mobile-footer a:not(.kv-menu-footer-cta)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .kv-menu-mobile-footer span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mobile_footer_link_hover_color',
			[
				'label'     => esc_html__( 'Links Hover Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-mobile-footer a:not(.kv-menu-footer-cta):hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'mobile_footer_links_typography',
				'selector' => '{{WRAPPER}} .kv-menu-mobile-footer a:not(.kv-menu-footer-cta), {{WRAPPER}} .kv-menu-mobile-footer span',
			]
		);

		$this->add_control(
			'mobile_footer_cta_heading',
			[
				'label'     => esc_html__( 'CTA Button', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'mobile_footer_cta_typography',
				'selector' => '{{WRAPPER}} .kv-menu-footer-cta',
			]
		);

		$this->start_controls_tabs( 'tabs_mobile_footer_cta_style' );

		$this->start_controls_tab(
			'tab_mobile_footer_cta_normal',
			[
				'label' => esc_html__( 'Normal', 'kv-menu' ),
			]
		);

		$this->add_control(
			'mobile_footer_cta_color',
			[
				'label'     => esc_html__( 'Text Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-footer-cta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mobile_footer_cta_bg',
			[
				'label'     => esc_html__( 'Background Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-footer-cta' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_mobile_footer_cta_hover',
			[
				'label' => esc_html__( 'Hover', 'kv-menu' ),
			]
		);

		$this->add_control(
			'mobile_footer_cta_hover_color',
			[
				'label'     => esc_html__( 'Text Color (Hover)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-footer-cta:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mobile_footer_cta_hover_bg',
			[
				'label'     => esc_html__( 'Background Color (Hover)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-menu-footer-cta:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'mobile_footer_cta_padding',
			[
				'label'      => esc_html__( 'Button Padding', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-footer-cta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'mobile_footer_cta_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-menu-footer-cta' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// ADVANCED HEADER SETTINGS
		// ==========================================

		$this->start_controls_section(
			'section_desktop_cta',
			[
				'label' => esc_html__( 'Desktop CTA Button', 'kv-menu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_desktop_cta',
			[
				'label'        => esc_html__( 'Show Desktop CTA', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'desktop_cta_text',
			[
				'label'     => esc_html__( 'Button Text', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Request A Proposal', 'kv-menu' ),
				'condition' => [ 'show_desktop_cta' => 'yes' ],
			]
		);

		$this->add_control(
			'desktop_cta_url',
			[
				'label'     => esc_html__( 'Button Link', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => [ 'url' => '#' ],
				'condition' => [ 'show_desktop_cta' => 'yes' ],
			]
		);
		
		$this->add_control(
			'desktop_cta_icon',
			[
				'label'       => esc_html__( 'Button Icon', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				],
				'condition' => [ 'show_desktop_cta' => 'yes' ],
			]
		);

		$this->add_control(
			'desktop_cta_color',
			[
				'label'     => esc_html__( 'Button Text Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-desktop-cta' => 'color: {{VALUE}};',
				],
				'condition' => [ 'show_desktop_cta' => 'yes' ],
			]
		);

		$this->add_control(
			'desktop_cta_bg',
			[
				'label'     => esc_html__( 'Button Background', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-desktop-cta' => 'background-color: {{VALUE}};',
				],
				'condition' => [ 'show_desktop_cta' => 'yes' ],
			]
		);
		
		$this->add_control(
			'desktop_cta_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'kv-menu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .kv-desktop-cta' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [ 'show_desktop_cta' => 'yes' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_header_behavior',
			[
				'label' => esc_html__( 'Header Behavior (Transparent & Sticky)', 'kv-menu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'is_transparent_header',
			[
				'label'        => esc_html__( 'Enable Transparent Header', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
				'description'  => esc_html__( 'Sets the header to position absolute so it overlays the content below it.', 'kv-menu' ),
			]
		);

		$this->add_control(
			'is_sticky_header',
			[
				'label'        => esc_html__( 'Enable Sticky Header', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'sticky_offset',
			[
				'label'     => esc_html__( 'Sticky Trigger Offset (px)', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 100,
				'condition' => [ 'is_sticky_header' => 'yes' ],
			]
		);

		$this->add_control(
			'sticky_logo',
			[
				'label'     => esc_html__( 'Sticky Logo Image', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'description' => esc_html__( 'Optional: Upload a different logo to show when the header becomes sticky.', 'kv-menu' ),
				'condition' => [ 'is_sticky_header' => 'yes' ],
			]
		);

		$this->add_control(
			'sticky_bg_color',
			[
				'label'     => esc_html__( 'Sticky Background Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-header-sticky .kv-menu-main-bar' => 'background-color: {{VALUE}} !important;',
				],
				'condition' => [ 'is_sticky_header' => 'yes' ],
			]
		);

		$this->add_control(
			'sticky_text_color',
			[
				'label'     => esc_html__( 'Sticky Menu Text Color', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .kv-header-sticky .kv-menu-list > .menu-item > a' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .kv-header-sticky .kv-menu-logo-text' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .kv-header-sticky .kv-menu-toggle-btn' => 'color: {{VALUE}} !important;',
				],
				'condition' => [ 'is_sticky_header' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['menu'] ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="elementor-widget-empty-icon" style="text-align:center; padding:20px; border:1px dashed #ccc;">';
				echo '<i class="eicon-nav-menu" style="font-size:30px; margin-bottom:10px; display:block;"></i>';
				echo esc_html__( 'Please select a menu in the widget settings.', 'kv-menu' );
				echo '</div>';
			}
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'kv-menu-wrapper' );
		$this->add_render_attribute( 'wrapper', 'class', 'kv-menu--layout-' . $settings['layout'] );
		$this->add_render_attribute( 'wrapper', 'class', 'kv-menu--breakpoint-' . $settings['breakpoint'] );
		if ( 'none' !== $settings['breakpoint'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'kv-menu--anim-' . $settings['panel_animation'] );
		}

		// Mega menu wrapper classes removed since width and alignment are handled via WP menus item settings

		if ( 'yes' !== $settings['show_underline'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'kv-menu--hide-underline' );
		}

		if ( 'yes' === $settings['is_transparent_header'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'kv-menu-transparent' );
		}

		if ( 'yes' === $settings['is_sticky_header'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'kv-menu-sticky-enabled' );
			$this->add_render_attribute( 'wrapper', 'data-sticky-offset', $settings['sticky_offset'] );
		}

		// Mobile panel attributes
		$this->add_render_attribute( 'nav', 'id', 'kv-menu-' . $this->get_id() );
		$this->add_render_attribute( 'nav', 'class', 'kv-menu-nav' );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			
			<div class="kv-menu-main-bar">
				
				<?php if ( 'yes' === $settings['show_logo'] ) : ?>
					<div class="kv-menu-logo-container">
						<a href="<?php echo esc_url( empty( $settings['logo_url']['url'] ) ? home_url( '/' ) : $settings['logo_url']['url'] ); ?>" class="kv-menu-logo-link">
							<?php if ( 'site_logo' === $settings['logo_type'] ) : ?>
								<?php 
								$custom_logo_id = get_theme_mod( 'custom_logo' );
								$logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
								if ( has_custom_logo() && ! empty( $logo ) ) {
									echo '<img src="' . esc_url( $logo[0] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="kv-menu-logo-img">';
								} else {
									echo '<span class="kv-menu-logo-text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
								}
								?>
							<?php else : ?>
								<?php if ( ! empty( $settings['logo_image']['url'] ) ) : ?>
									<img src="<?php echo esc_url( $settings['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="kv-menu-logo-img kv-logo-normal">
									<?php if ( 'yes' === $settings['is_sticky_header'] && ! empty( $settings['sticky_logo']['url'] ) ) : ?>
										<img src="<?php echo esc_url( $settings['sticky_logo']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="kv-menu-logo-img kv-logo-sticky" style="display:none;">
									<?php endif; ?>
								<?php endif; ?>
							<?php endif; ?>
						</a>
					</div>
				<?php endif; ?>

				<?php if ( 'none' !== $settings['breakpoint'] ) : ?>
					<div class="kv-menu-toggle-container">
						<button class="kv-menu-toggle-btn" aria-expanded="false" aria-controls="kv-menu-<?php echo esc_attr( $this->get_id() ); ?>">
							
							<?php if ( 'animated' === $settings['toggle_icon_style'] ) : ?>
								<span class="kv-menu-hamburger-animated">
									<span></span>
									<span></span>
									<span></span>
								</span>
							<?php else : ?>
								<?php if ( ! empty( $settings['toggle_text'] ) ) : ?>
									<span class="kv-menu-toggle-text"><?php echo esc_html( $settings['toggle_text'] ); ?></span>
								<?php endif; ?>

								<span class="kv-menu-toggle-icon kv-menu-icon-open">
									<?php \Elementor\Icons_Manager::render_icon( $settings['hamburger_icon'], [ 'aria-hidden' => 'true' ] ); ?>
								</span>
								<span class="kv-menu-toggle-icon kv-menu-icon-close">
									<?php \Elementor\Icons_Manager::render_icon( $settings['close_icon'], [ 'aria-hidden' => 'true' ] ); ?>
								</span>
							<?php endif; ?>
						</button>
					</div>
				<?php endif; ?>

				<nav <?php $this->print_render_attribute_string( 'nav' ); ?>>
					
					<?php if ( 'none' !== $settings['breakpoint'] ) : ?>
						<div class="kv-menu-mobile-header">
							<!-- Left Side: Back Chevron OR Site Logo -->
							<div class="kv-menu-mobile-header-left">
								<button class="kv-menu-back-btn" aria-label="<?php echo esc_attr__( 'Back', 'kv-menu' ); ?>" style="display: none;">
									<span class="kv-menu-back-icon">
										<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
									</span>
								</button>
								<?php if ( 'yes' === $settings['show_logo'] ) : ?>
									<div class="kv-menu-mobile-logo-container">
										<?php if ( 'site_logo' === $settings['logo_type'] ) : ?>
											<?php 
											$custom_logo_id = get_theme_mod( 'custom_logo' );
											$logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
											if ( has_custom_logo() && ! empty( $logo ) ) {
												echo '<img src="' . esc_url( $logo[0] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="kv-menu-logo-img">';
											} else {
												echo '<span class="kv-menu-logo-text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
											}
											?>
										<?php else : ?>
											<?php if ( ! empty( $settings['logo_image']['url'] ) ) : ?>
												<img src="<?php echo esc_url( $settings['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="kv-menu-logo-img">
											<?php endif; ?>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>

							<button class="kv-menu-close-btn" aria-label="<?php echo esc_attr__( 'Close', 'kv-menu' ); ?>">
								<span class="kv-menu-close-icon">
									<?php if ( 'animated' === $settings['toggle_icon_style'] ) : ?>
										<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
									<?php else : ?>
										<?php \Elementor\Icons_Manager::render_icon( $settings['close_icon'], [ 'aria-hidden' => 'true' ] ); ?>
									<?php endif; ?>
								</span>
							</button>
						</div>
					<?php endif; ?>

					<div class="kv-menu-drilldown-container">
						<?php
						ob_start();
						\Elementor\Icons_Manager::render_icon( $settings['submenu_indicator_icon'], [ 'aria-hidden' => 'true' ] );
						$desktop_icon = ob_get_clean();
						if ( empty( trim( $desktop_icon ) ) ) {
							$desktop_icon = '<i class="fas fa-angle-down" aria-hidden="true"></i>';
						}

						ob_start();
						\Elementor\Icons_Manager::render_icon( $settings['submenu_indicator_icon_mobile'], [ 'aria-hidden' => 'true' ] );
						$mobile_icon = ob_get_clean();
						if ( empty( trim( $mobile_icon ) ) ) {
							$mobile_icon = '<i class="fas fa-chevron-right" aria-hidden="true"></i>';
						}

						$filter_callback = function( $item_output, $item, $depth, $args) use ( $desktop_icon, $mobile_icon ) {
							$has_children = in_array( 'menu-item-has-children', $item->classes );
							$mega_enabled = get_post_meta( $item->ID, '_kv_menu_mega_enabled', true );
							$has_mega     = ( 0 === $depth && 'yes' === $mega_enabled );

							if ( $has_children || $has_mega ) {
								$indicator_html = '<span class="kv-menu-sub-indicator kv-menu-sub-indicator-desktop">' . $desktop_icon . '</span>';
								$indicator_html .= '<span class="kv-menu-sub-indicator kv-menu-sub-indicator-mobile">' . $mobile_icon . '</span>';
								
								$pos = strrpos( $item_output, '</a>' );
								if ( false !== $pos ) {
									$item_output = substr_replace( $item_output, $indicator_html, $pos, 0 );
								} else {
									$item_output .= $indicator_html;
								}
							}

							if ( $has_mega ) {
								$template_id = get_post_meta( $item->ID, '_kv_menu_mega_template_id', true );
								if ( ! empty( $template_id ) ) {
									// Enqueue template CSS for Elementor section
									if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
										$css_file = new \Elementor\Core\Files\CSS\Post( $template_id );
										$css_file->enqueue();
									}
									
									$mega_content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id );
									
									// If content is empty and user can manage options, show a premium help block
									if ( empty( $mega_content ) && ( current_user_can( 'edit_theme_options' ) || \Elementor\Plugin::$instance->editor->is_edit_mode() ) ) {
										$mega_content = '<div class="kv-menu-mega-empty-placeholder" style="padding: 30px; text-align: center; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 8px; box-sizing: border-box; width: 100%;">';
										$mega_content .= '<p style="margin: 0 0 12px 0; font-weight: 600; color: #64748b; font-family: sans-serif; font-size: 14px; line-height: 1.4;">' . esc_html__( 'Mega Menu template is empty.', 'kv-menu' ) . '</p>';
										$mega_content .= '<a href="' . esc_url( admin_url( 'post.php?post=' . $template_id . '&action=elementor' ) ) . '" class="elementor-button elementor-size-sm" target="_blank" style="background-color: #6366f1; color: #fff; text-decoration: none; display: inline-block; padding: 8px 16px; border-radius: 6px; font-weight: 500; font-family: sans-serif; font-size: 12px; line-height: 1.5; box-shadow: 0 2px 4px rgba(99,102,241,0.2); transition: background-color 0.2s ease;">' . esc_html__( 'Edit Template in Elementor', 'kv-menu' ) . '</a>';
										$mega_content .= '</div>';
									}
									
									if ( ! empty( $mega_content ) ) {
										$mega_width_type = get_post_meta( $item->ID, '_kv_menu_mega_width_type', true );
										$mega_custom_width = get_post_meta( $item->ID, '_kv_menu_mega_custom_width', true );
										$mega_alignment = get_post_meta( $item->ID, '_kv_menu_mega_alignment', true );

										if ( empty( $mega_width_type ) ) {
											$mega_width_type = 'full';
										}
										if ( empty( $mega_custom_width ) ) {
											$mega_custom_width = '1000px';
										}
										if ( empty( $mega_alignment ) ) {
											$mega_alignment = 'center';
										}

										$panel_classes = [ 'kv-menu-mega-panel' ];
										$panel_style = '';

										if ( 'custom' === $mega_width_type ) {
											$panel_classes[] = 'kv-menu-mega-panel--custom-width';
											$panel_classes[] = 'kv-menu-mega-panel--align-' . $mega_alignment;

											$clean_width = trim( $mega_custom_width );
											if ( ! empty( $clean_width ) ) {
												if ( is_numeric( $clean_width ) ) {
													$clean_width .= 'px';
												}
												$panel_style = ' style="max-width: ' . esc_attr( $clean_width ) . '; width: 100%;"';
											}
										} else {
											$panel_classes[] = 'kv-menu-mega-panel--full-width';
										}

										$item_output .= '<div class="' . esc_attr( implode( ' ', $panel_classes ) ) . '"' . $panel_style . '>' . $mega_content . '</div>';
									}
								}
							}

							return $item_output;
						};

						add_filter( 'walker_nav_menu_start_el', $filter_callback, 10, 4 );

						wp_nav_menu( [
							'menu'        => $settings['menu'],
							'container'   => false,
							'menu_class'  => 'kv-menu-list',
							'depth'       => 3,
							'fallback_cb' => '__return_empty_string',
						] );

						remove_filter( 'walker_nav_menu_start_el', $filter_callback, 10 );
						?>
					</div>

					<?php if ( 'none' !== $settings['breakpoint'] && 'yes' === $settings['show_mobile_footer'] ) : ?>
						<div class="kv-menu-mobile-footer">
							<div class="kv-menu-footer-links">
								<?php if ( ! empty( $settings['footer_link_1_text'] ) && ! empty( $settings['footer_link_1_url']['url'] ) ) : 
									$this->add_link_attributes( 'footer_link_1', $settings['footer_link_1_url'] ); ?>
									<a <?php $this->print_render_attribute_string( 'footer_link_1' ); ?>><?php echo esc_html( $settings['footer_link_1_text'] ); ?></a>
								<?php endif; ?>
								
								<?php if ( ! empty( $settings['footer_link_2_text'] ) && ! empty( $settings['footer_link_2_url']['url'] ) ) : 
									$this->add_link_attributes( 'footer_link_2', $settings['footer_link_2_url'] ); ?>
									<a <?php $this->print_render_attribute_string( 'footer_link_2' ); ?>><?php echo esc_html( $settings['footer_link_2_text'] ); ?></a>
								<?php endif; ?>

								<?php if ( ! empty( $settings['footer_info_text'] ) ) : ?>
									<?php if ( ! empty( $settings['footer_info_url'] ) ) : ?>
										<a href="<?php echo esc_url( $settings['footer_info_url'] ); ?>"><?php echo esc_html( $settings['footer_info_text'] ); ?></a>
									<?php else : ?>
										<span><?php echo esc_html( $settings['footer_info_text'] ); ?></span>
									<?php endif; ?>
								<?php endif; ?>
							</div>
							
							<?php if ( ! empty( $settings['footer_cta_text'] ) && ! empty( $settings['footer_cta_url']['url'] ) ) : 
								$this->add_link_attributes( 'footer_cta', $settings['footer_cta_url'] );
								$this->add_render_attribute( 'footer_cta', 'class', 'kv-menu-footer-cta' ); ?>
								<a <?php $this->print_render_attribute_string( 'footer_cta' ); ?>><?php echo esc_html( $settings['footer_cta_text'] ); ?></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</nav>

				<?php if ( 'yes' === $settings['show_desktop_cta'] ) : 
					$this->add_link_attributes( 'desktop_cta', $settings['desktop_cta_url'] );
				?>
					<div class="kv-menu-desktop-cta-wrapper">
						<a <?php $this->print_render_attribute_string( 'desktop_cta' ); ?> class="kv-desktop-cta">
							<span class="kv-cta-text"><?php echo esc_html( $settings['desktop_cta_text'] ); ?></span>
							<?php if ( ! empty( $settings['desktop_cta_icon']['value'] ) ) : ?>
								<span class="kv-cta-icon">
									<?php \Elementor\Icons_Manager::render_icon( $settings['desktop_cta_icon'], [ 'aria-hidden' => 'true' ] ); ?>
								</span>
							<?php endif; ?>
						</a>
					</div>
				<?php endif; ?>

			</div>
		</div>
		<?php
	}
}
