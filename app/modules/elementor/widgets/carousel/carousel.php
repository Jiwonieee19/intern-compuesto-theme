<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

class Elem_Carousel extends Widget_Base {
  public function get_title(): string {
    return 'Working Carousel';
  }

  public function get_script_depends(): array {
    return [
      'theme-swiper-js',
    ];
  }

  public function get_style_depends(): array {
    return [
      'theme-swiper-css',
      $this->_get_asset_handle(),
    ];
  }

  protected function register_controls() {
    $this->start_controls_section(
      'slides_section',
      [
        'label' => 'Slides',
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    $repeater = new Repeater();

    $repeater->add_control(
      'image',
      [
        'label'   => __( 'Image' ),
        'type'    => Controls_Manager::MEDIA,
        'default' => [
          'url' => '',
        ],
      ]
    );

    $repeater->add_control(
      'title',
      [
        'label'       => __( 'Title' ),
        'type'        => Controls_Manager::TEXT,
        'default'     => '',
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'link',
      [
        'label'   => __( 'Link' ),
        'type'    => Controls_Manager::URL,
        'default' => [
          'url' => '',
        ],
      ]
    );

    $this->add_control(
      'slides',
      [
        'label'       => __( 'Slides' ),
        'type'        => Controls_Manager::REPEATER,
        'fields'      => $repeater->get_controls(),
        'title_field' => '{{{ title }}}',
        'default'     => [],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'settings_section',
      [
        'label' => 'Settings',
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'slides_per_view',
      [
        'label'   => __( 'Slides Per View (desktop)' ),
        'type'    => Controls_Manager::SELECT,
        'options' => [
          '1' => '1',
          '2' => '2',
          '3' => '3',
        ],
        'default'        => '1',
        'tablet_default' => '2',
        'mobile_default' => '1',
      ]
    );

    $this->add_control(
      'gap',
      [
        'label'   => __( 'Gap (px)' ),
        'type'    => Controls_Manager::NUMBER,
        'default' => 10,
        'min'     => 0,
        'max'     => 100,
      ]
    );

    $this->add_control(
      'speed',
      [
        'label'   => __( 'Transition Speed (ms)' ),
        'type'    => Controls_Manager::NUMBER,
        'default' => 500,
        'min'     => 100,
        'max'     => 3000,
      ]
    );

    $this->add_control(
      'autoplay',
      [
        'label'        => __( 'Autoplay' ),
        'type'         => Controls_Manager::SWITCHER,
        'label_on'     => __( 'Yes' ),
        'label_off'    => __( 'No' ),
        'return_value' => 'yes',
        'default'      => 'yes',
      ]
    );

    $this->add_control(
      'autoplay_delay',
      [
        'label'     => __( 'Autoplay Delay (ms)' ),
        'type'      => Controls_Manager::NUMBER,
        'default'   => 5000,
        'min'       => 1000,
        'max'       => 15000,
        'condition' => [
          'autoplay' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'loop',
      [
        'label'        => __( 'Infinite Loop' ),
        'type'         => Controls_Manager::SWITCHER,
        'label_on'     => __( 'Yes' ),
        'label_off'    => __( 'No' ),
        'return_value' => 'yes',
        'default'      => 'yes',
      ]
    );

    $this->add_control(
      'show_arrows',
      [
        'label'        => __( 'Arrows' ),
        'type'         => Controls_Manager::SWITCHER,
        'label_on'     => __( 'Show' ),
        'label_off'    => __( 'Hide' ),
        'return_value' => 'yes',
        'default'      => 'yes',
      ]
    );

    $this->add_control(
      'show_dots',
      [
        'label'        => __( 'Pagination Dots' ),
        'type'         => Controls_Manager::SWITCHER,
        'label_on'     => __( 'Show' ),
        'label_off'    => __( 'Hide' ),
        'return_value' => 'yes',
        'default'      => 'yes',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'style_section',
      [
        'label' => 'Colors',
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'arrow_color',
      [
        'label'     => __( 'Arrow Icon Color' ),
        'type'      => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .app-carousel-arrow' => 'color: {{VALUE}};',
        ],
        'default'   => '#FFFFFF',
      ]
    );

    $this->add_control(
      'dot_color',
      [
        'label'     => __( 'Dot Color' ),
        'type'      => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
        ],
        'default'   => 'rgba(255,255,255,.55)',
      ]
    );

    $this->add_control(
      'dot_active_color',
      [
        'label'     => __( 'Active Dot Color' ),
        'type'      => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
        ],
        'default'   => '#A073F7',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $settings = $this->get_settings_for_display();
    $class    = 'app-carousel-widget';
    $uid      = uniqid( "$class-" );
    $slides   = $settings['slides'];

    if ( empty( $slides ) || ! is_array( $slides ) ) {
      return;
    }

    $count = count( $slides );

    $config = [
      'slidesPerView' => max( 1, (float) ( $settings['slides_per_view_mobile'] ?? '1' ) ),
      'breakpoints'   => [
        768  => [
          'slidesPerView' => max( 1, (float) ( $settings['slides_per_view_tablet'] ?? '1' ) ),
        ],
        1025 => [
          'slidesPerView' => max( 1, (float) ( $settings['slides_per_view'] ?? '1' ) ),
        ],
      ],
      'spaceBetween'  => (int) ( $settings['gap'] ?? 10 ),
      'speed'         => (int) ( $settings['speed'] ?? 500 ),
      'keyboard'      => true,
      'navigation'    => [
        'nextEl' => "#{$uid} .app-carousel-arrow--next",
        'prevEl' => "#{$uid} .app-carousel-arrow--prev",
      ],
      'pagination'    => [
        'el'        => "#{$uid} .swiper-pagination",
        'clickable' => true,
      ],
    ];

    if ( 'yes' === ( $settings['loop'] ?? '' ) && $count > 1 ) {
      $config['loop'] = true;
    }

    if ( 'yes' === ( $settings['autoplay'] ?? '' ) && $count > 1 ) {
      $config['autoplay'] = [
        'delay'                => (int) ( $settings['autoplay_delay'] ?? 5000 ),
        'disableOnInteraction' => false,
        'pauseOnMouseEnter'    => true,
      ];
    }

    if ( $count < 2 ) {
      unset( $config['navigation'], $config['pagination'] );
    }

    $encoded_config = wp_json_encode( $config );
    ?>
    <div id="<?= esc_attr( $uid ) ?>" class="<?= esc_attr( $class ) ?>">
      <div class="swiper app-carousel" dir="ltr">
        <div class="swiper-wrapper">
          <?php foreach ( $slides as $slide ) :
            $img   = $slide['image']['url'] ?? '';
            $title = $slide['title'] ?? '';
            $link  = $slide['link']['url'] ?? '';

            if ( empty( $img ) ) {
              continue;
            }
            ?>
            <div class="swiper-slide">
              <?php if ( ! empty( $link ) ) : ?>
                <a href="<?= esc_url( $link ) ?>" class="app-carousel-slide">
                  <img src="<?= esc_url( $img ) ?>" alt="<?= esc_attr( $title ) ?>" loading="lazy">
                  <?php if ( ! empty( $title ) ) : ?>
                    <span class="app-carousel-caption"><?= esc_html( $title ) ?></span>
                  <?php endif; ?>
                </a>
              <?php else : ?>
                <div class="app-carousel-slide">
                  <img src="<?= esc_url( $img ) ?>" alt="<?= esc_attr( $title ) ?>" loading="lazy">
                  <?php if ( ! empty( $title ) ) : ?>
                    <span class="app-carousel-caption"><?= esc_html( $title ) ?></span>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if ( 'yes' === ( $settings['show_arrows'] ?? '' ) && $count > 1 ) : ?>
          <div class="app-carousel-arrow app-carousel-arrow--prev" role="button" tabindex="0" aria-label="Previous slide">
            <svg aria-hidden="true" viewBox="0 0 1000 1000" width="24" height="24" xmlns="http://www.w3.org/2000/svg"><path d="M646 125C629 125 613 133 604 142L308 442C296 454 292 471 292 487 292 504 296 521 308 533L604 854C617 867 629 875 646 875 663 875 679 871 692 858 704 846 713 829 713 812 713 796 708 779 692 767L438 487 692 225C700 217 708 204 708 187 708 171 704 154 692 142 675 129 663 125 646 125Z"/></svg>
          </div>
          <div class="app-carousel-arrow app-carousel-arrow--next" role="button" tabindex="0" aria-label="Next slide">
            <svg aria-hidden="true" viewBox="0 0 1000 1000" width="24" height="24" xmlns="http://www.w3.org/2000/svg"><path d="M696 533C708 521 713 504 713 487 713 471 708 454 696 446L400 146C388 133 375 125 354 125 338 125 325 129 313 142 300 154 292 171 292 187 292 204 296 221 308 233L563 492 304 771C292 783 288 800 288 817 288 833 296 850 308 863 321 871 338 875 354 875 371 875 388 867 400 854L696 533Z"/></svg>
          </div>
        <?php endif; ?>

        <?php if ( 'yes' === ( $settings['show_dots'] ?? '' ) && $count > 1 ) : ?>
          <div class="swiper-pagination"></div>
        <?php endif; ?>
      </div>
    </div>

    <script defer>
      (function () {
        var uid = <?= wp_json_encode( $uid ) ?>;
        var config = <?= $encoded_config ?>;
        var retries = 0;

        function init() {
          var root = document.getElementById(uid);
          if (!root || root.dataset.appCarouselReady === 'ready') return;

          if (typeof window.Swiper !== 'function') {
            if (retries++ < 50) { window.setTimeout(init, 100); }
            return;
          }

          if (root.swiper && typeof root.swiper.destroy === 'function') {
            root.swiper.destroy(true, true);
          }

          root.swiper = new Swiper(root.querySelector('.app-carousel'), config);
          root.dataset.appCarouselReady = 'ready';
        }

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', init);
        } else {
          init();
        }
      })();
    </script>
    <?php
  }

  // DO NOT CHANGE/UPDATE BELOW FUNCTIONS IF NOT NECESSARY

  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
    $this->_register_assets();
  }

  public function get_name(): string {
    return __CLASS__;
  }

  public function get_categories(): array {
    return [ 'custom' ];
  }

  private function _register_assets() {
    $name = str_replace( '_', '-', strtolower( substr( $this->get_name(), 5 ) ) );

    wp_register_style( $this->_get_asset_handle(), get_theme_file_uri( "dist/css/$name.min.css" ), [], ASSETS_VERSION );
  }

  private function _get_asset_handle(): string {
    return "theme-{$this->get_name()}";
  }
}
