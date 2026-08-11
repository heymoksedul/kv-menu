<?php
/**
 * KV Mega Menu Tabs Widget.
 *
 * @package KV_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class KV_Mega_Menu_Tabs_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'kv-mega-menu-tabs';
	}

	public function get_title() {
		return esc_html__( 'KV Mega Menu Tabs', 'kv-menu' );
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Tabs Content', 'kv-menu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'tab_title',
			[
				'label'       => esc_html__( 'Tab Title (Left Side)', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Home Services', 'kv-menu' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'main_title',
			[
				'label'       => esc_html__( 'Content Main Title', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Home Services', 'kv-menu' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'subtitle',
			[
				'label'       => esc_html__( 'Subtitle', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Choose Your Industry', 'kv-menu' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'links_list',
			[
				'label'       => esc_html__( 'Links List', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 10,
				'default'     => "fas fa-home | Roofing | #\nfas fa-thermometer-half | HVAC | #\nfas fa-solar-panel | Solar | #\nfas fa-bug | Pest Control | #\nfas fa-wrench | Plumbing | #\nfas fa-bolt | Electrical | #",
				'description' => esc_html__( 'Enter one link per line. Format: Icon Class | Title | URL. (e.g. fas fa-home | Roofing | https://example.com)', 'kv-menu' ),
			]
		);

		$repeater->add_control(
			'featured_image',
			[
				'label'   => esc_html__( 'Featured Image', 'kv-menu' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'show_promo',
			[
				'label'        => esc_html__( 'Show Promo Box', 'kv-menu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$repeater->add_control(
			'promo_icon',
			[
				'label'     => esc_html__( 'Promo Icon Class', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => 'fas fa-rocket',
				'condition' => [ 'show_promo' => 'yes' ],
			]
		);

		$repeater->add_control(
			'promo_badge',
			[
				'label'     => esc_html__( 'Promo Badge Text', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => 'NEW',
				'condition' => [ 'show_promo' => 'yes' ],
			]
		);

		$repeater->add_control(
			'promo_title',
			[
				'label'       => esc_html__( 'Promo Title', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'ScaleOption, your growth engine',
				'label_block' => true,
				'condition'   => [ 'show_promo' => 'yes' ],
			]
		);

		$repeater->add_control(
			'promo_desc',
			[
				'label'     => esc_html__( 'Promo Description', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => 'The ultimate AI-powered growth engine that automates lead capture.',
				'condition' => [ 'show_promo' => 'yes' ],
			]
		);

		$repeater->add_control(
			'promo_link_text',
			[
				'label'     => esc_html__( 'Promo Link Text', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => 'Learn more',
				'condition' => [ 'show_promo' => 'yes' ],
			]
		);

		$repeater->add_control(
			'promo_link_url',
			[
				'label'     => esc_html__( 'Promo Link URL', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => [ 'url' => '#' ],
				'condition' => [ 'show_promo' => 'yes' ],
			]
		);

		$this->add_control(
			'tabs',
			[
				'label'       => esc_html__( 'Tabs', 'kv-menu' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'tab_title' => esc_html__( 'Home Services', 'kv-menu' ) ],
					[ 'tab_title' => esc_html__( 'Legal', 'kv-menu' ) ],
				],
				'title_field' => '{{{ tab_title }}}',
			]
		);

		$this->add_control(
			'bottom_contact_title',
			[
				'label'     => esc_html__( 'Sidebar Bottom Title', 'kv-menu' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => 'Get In Touch',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'bottom_contact_info',
			[
				'label'   => esc_html__( 'Sidebar Bottom Info', 'kv-menu' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '01873-116666',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$tabs = $settings['tabs'];
		$id_int = substr( $this->get_id_int(), 0, 3 );
		?>
		<div class="kv-mega-tabs-wrapper" id="kv-mega-tabs-<?php echo esc_attr( $id_int ); ?>">
			
			<div class="kv-mega-tabs-sidebar">
				<ul class="kv-mega-tabs-nav">
					<?php foreach ( $tabs as $index => $tab ) : 
						$active_class = ( 0 === $index ) ? ' active' : '';
					?>
						<li class="kv-mega-tab-item<?php echo esc_attr( $active_class ); ?>" data-tab-target="kv-tab-content-<?php echo esc_attr( $id_int . '-' . $index ); ?>">
							<span class="kv-mega-tab-title"><?php echo esc_html( $tab['tab_title'] ); ?></span>
							<i class="fas fa-chevron-right kv-mega-tab-icon"></i>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php if ( ! empty( $settings['bottom_contact_title'] ) || ! empty( $settings['bottom_contact_info'] ) ) : ?>
					<div class="kv-mega-tabs-contact">
						<?php if ( ! empty( $settings['bottom_contact_title'] ) ) : ?>
							<strong><?php echo esc_html( $settings['bottom_contact_title'] ); ?></strong><br>
						<?php endif; ?>
						<?php if ( ! empty( $settings['bottom_contact_info'] ) ) : ?>
							<span><?php echo esc_html( $settings['bottom_contact_info'] ); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="kv-mega-tabs-content-area">
				<?php foreach ( $tabs as $index => $tab ) : 
					$active_class = ( 0 === $index ) ? ' active' : '';
				?>
					<div class="kv-mega-tab-pane<?php echo esc_attr( $active_class ); ?>" id="kv-tab-content-<?php echo esc_attr( $id_int . '-' . $index ); ?>">
						
						<div class="kv-mega-tab-pane-header">
							<h3><?php echo esc_html( $tab['main_title'] ); ?></h3>
							<p><?php echo esc_html( $tab['subtitle'] ); ?></p>
						</div>

						<div class="kv-mega-tab-pane-body">
							<div class="kv-mega-tab-links">
								<?php 
								if ( ! empty( $tab['links_list'] ) ) {
									$lines = explode( "\n", $tab['links_list'] );
									echo '<ul>';
									foreach ( $lines as $line ) {
										$parts = array_map( 'trim', explode( '|', $line ) );
										if ( count( $parts ) >= 2 ) {
											$icon  = $parts[0];
											$title = $parts[1];
											$url   = isset( $parts[2] ) ? $parts[2] : '#';
											echo '<li><a href="' . esc_url( $url ) . '">';
											if ( ! empty( $icon ) ) {
												echo '<i class="' . esc_attr( $icon ) . '"></i> ';
											}
											echo esc_html( $title ) . '</a></li>';
										}
									}
									echo '</ul>';
								}
								?>
							</div>

							<div class="kv-mega-tab-media">
								<?php if ( ! empty( $tab['featured_image']['url'] ) ) : ?>
									<img src="<?php echo esc_url( $tab['featured_image']['url'] ); ?>" alt="<?php echo esc_attr( $tab['main_title'] ); ?>">
								<?php endif; ?>
								
								<?php if ( 'yes' === $tab['show_promo'] ) : ?>
									<div class="kv-mega-promo-box">
										<div class="kv-mega-promo-header">
											<i class="<?php echo esc_attr( $tab['promo_icon'] ); ?>"></i>
											<?php if ( ! empty( $tab['promo_badge'] ) ) : ?>
												<span class="kv-mega-promo-badge"><?php echo esc_html( $tab['promo_badge'] ); ?></span>
											<?php endif; ?>
										</div>
										<h4><?php echo esc_html( $tab['promo_title'] ); ?></h4>
										<p><?php echo esc_html( $tab['promo_desc'] ); ?></p>
										<?php if ( ! empty( $tab['promo_link_text'] ) ) : ?>
											<a href="<?php echo esc_url( $tab['promo_link_url']['url'] ); ?>"><?php echo esc_html( $tab['promo_link_text'] ); ?></a>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>

					</div>
				<?php endforeach; ?>
			</div>

		</div>
		<?php
	}
}
