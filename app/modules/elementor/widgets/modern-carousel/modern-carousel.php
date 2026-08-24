<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

class Elem_Modern_Carousel extends Widget_Base {
  public function get_title(): string {
    return 'Modern Carousel';
  }

  public function get_script_depends(): array {
    return [
      'theme-modern-carousel-js',
    ];
  }

  public function get_style_depends(): array {
    return [
      $this->_get_asset_handle(),
    ];
  }

  protected function register_controls() {
    $this->_content_slides();
    $this->_content_layout();
    $this->_content_behavior();

    $this->_style_slide_stage();
    $this->_style_active_slide();
    $this->_style_inactive_slides();
    $this->_style_content();
    $this->_style_navigation();
    $this->_style_pagination();
  }

  private function _content_slides() {
    $this->start_controls_section(
      'slides_section',
      [
        'label' => __( 'Slides' ),
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    $repeater = new Repeater();

    $repeater->add_control(
      'image',
      [
        'label'   => __( 'Background Image' ),
        'type'    => Controls_Manager::MEDIA,
        'default' => [
          'url' => Utils::get_placeholder_image_src(),
        ],
      ]
    );

    $repeater->add_control(
      'icon',
      [
        'label'            => __( 'Icon' ),
        'type'             => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon_fa4',
      ]
    );

    $repeater->add_control(
      'heading',
      [
        'label'       => __( 'Heading' ),
        'type'        => Controls_Manager::TEXT,
        'default'     => __( 'Card Title' ),
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'text',
      [
        'label'   => __( 'Text' ),
        'type'    => Controls_Manager::TEXTAREA,
        'rows'    => 3,
        'default' => __( 'A short description for this slide.' ),
      ]
    );

    $repeater->add_control(
      'link',
      [
        'label'       => __( 'Link' ),
        'type'        => Controls_Manager::URL,
        'placeholder' => __( 'https://your-link.com' ),
        'description' => __( 'Only clickable when this card is the active (center) card.' ),
      ]
    );

    $this->add_control(
      'slides',
      [
        'label'       => __( 'Slides' ),
        'type'        => Controls_Manager::REPEATER,
        'fields'      => $repeater->get_controls(),
        'title_field' => '{{{ heading }}}',
        'default'     => [
          [
            'image'   => [ 'url' => Utils::get_placeholder_image_src() ],
            'heading' => __( 'Slide One' ),
            'text'    => __( 'A short description for this slide.' ),
          ],
          [
            'image'   => [ 'url' => Utils::get_placeholder_image_src() ],
            'heading' => __( 'Slide Two' ),
            'text'    => __( 'A short description for this slide.' ),
          ],
          [
            'image'   => [ 'url' => Utils::get_placeholder_image_src() ],
            'heading' => __( 'Slide Three' ),
            'text'    => __( 'A short description for this slide.' ),
          ],
        ],
      ]
    );

    $this->end_controls_section();
  }

  private function _content_layout() {
    $this->start_controls_section(
      'layout_section',
      [
        'label' => __( 'Layout' ),
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    $visible_options = [
      '1' => '1',
      '3' => '3',
      '5' => '5',
    ];

    $this->add_control(
      'visible_slides',
      [
        'label'   => __( 'Visible Slides (Desktop)' ),
        'type'    => Controls_Manager::SELECT,
        'options' => $visible_options,
        'default' => '3',
      ]
    );

    $this->add_control(
      'visible_slides_tablet',
      [
        'label'   => __( 'Visible Slides (Tablet)' ),
        'type'    => Controls_Manager::SELECT,
        'options' => $visible_options,
        'default' => '3',
      ]
    );

    $this->add_control(
      'visible_slides_mobile',
      [
        'label'   => __( 'Visible Slides (Mobile)' ),
        'type'    => Controls_Manager::SELECT,
        'options' => $visible_options,
        'default' => '1',
      ]
    );

    $this->add_control(
      'overlap',
      [
        'label'      => __( 'Overlap (%)' ),
        'type'       => Controls_Manager::SLIDER,
        'size_units' => [ '%' ],
        'range'      => [
          '%' => [ 'min' => 10, 'max' => 60 ],
        ],
        'default'    => [
          'unit' => '%',
          'size' => 34,
        ],
      ]
    );

    $this->add_control(
      'transition_speed',
      [
        'label'   => __( 'Transition Speed (ms)' ),
        'type'    => Controls_Manager::NUMBER,
        'default' => 500,
        'min'     => 150,
        'max'     => 2000,
      ]
    );

    $this->end_controls_section();
  }

  private function _content_behavior() {
    $this->start_controls_section(
      'behavior_section',
      [
        'label' => __( 'Behavior' ),
        'tab'   => Controls_Manager::TAB_CONTENT,
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
        'label'        => __( 'Show Arrows' ),
        'type'         => Controls_Manager::SWITCHER,
        'label_on'     => __( 'Yes' ),
        'label_off'    => __( 'No' ),
        'return_value' => 'yes',
        'default'      => 'yes',
      ]
    );

    $this->add_control(
      'show_dots',
      [
        'label'        => __( 'Show Dots' ),
        'type'         => Controls_Manager::SWITCHER,
        'label_on'     => __( 'Yes' ),
        'label_off'    => __( 'No' ),
        'return_value' => 'yes',
        'default'      => 'yes',
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
        'condition'    => [
          'loop' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'autoplay_delay',
      [
        'label'     => __( 'Autoplay Speed (ms)' ),
        'type'      => Controls_Manager::NUMBER,
        'default'   => 4000,
        'min'       => 1000,
        'max'       => 15000,
        'condition' => [
          'autoplay' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'pause_on_hover',
      [
        'label'        => __( 'Pause on Hover' ),
        'type'         => Controls_Manager::SWITCHER,
        'label_on'     => __( 'Yes' ),
        'label_off'    => __( 'No' ),
        'return_value' => 'yes',
        'default'      => 'yes',
        'condition'    => [
          'autoplay' => 'yes',
        ],
      ]
    );

    $this->end_controls_section();
  }

  private function _style_slide_stage() {
    $this->start_controls_section(
      'slide_style_section',
      [
        'label' => __( 'Slide Stage' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_responsive_control(
      'slide_width',
      [
        'label'      => __( 'Slide Width' ),
        'type'       => Controls_Manager::SLIDER,
        'size_units' => [ 'px' ],
        'range'      => [
          'px' => [ 'min' => 240, 'max' => 720 ],
        ],
        'default'    => [
          'unit' => 'px',
          'size' => 430,
        ],
        'selectors'  => [
          '{{WRAPPER}} .modern-carousel' => '--mc-slide-w: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'slide_height',
      [
        'label'      => __( 'Slide Height' ),
        'type'       => Controls_Manager::SLIDER,
        'size_units' => [ 'px' ],
        'range'      => [
          'px' => [ 'min' => 280, 'max' => 720 ],
        ],
        'default'    => [
          'unit' => 'px',
          'size' => 420,
        ],
        'selectors'  => [
          '{{WRAPPER}} .modern-carousel' => '--mc-slide-h: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'slide_radius',
      [
        'label'      => __( 'Corner Radius' ),
        'type'       => Controls_Manager::SLIDER,
        'size_units' => [ 'px' ],
        'range'      => [
          'px' => [ 'min' => 0, 'max' => 48 ],
        ],
        'default'    => [
          'unit' => 'px',
          'size' => 16,
        ],
        'selectors'  => [
          '{{WRAPPER}} .modern-carousel__card' => 'border-radius: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name'     => 'slide_border',
        'selector' => '{{WRAPPER}} .modern-carousel__card',
      ]
    );

    $this->add_control(
      'stage_heading_note',
      [
        'type'  => Controls_Manager::HEADING,
        'label' => __( 'Image Fallback' ),
      ]
    );

    $this->add_control(
      'slide_background',
      [
        'label'     => __( 'Background Color' ),
        'type'      => Controls_Manager::COLOR,
        'default'   => '#17171D',
        'selectors' => [
          '{{WRAPPER}} .modern-carousel__card' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_section();
  }

  private function _style_active_slide() {
    $this->start_controls_section(
      'active_section',
      [
        'label' => __( 'Active Slide' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'active_scale',
      [
        'label'   => __( 'Scale' ),
        'type'    => Controls_Manager::SLIDER,
        'range'   => [
          'px' => [ 'min' => 0.8, 'max' => 1.2, 'step' => 0.01 ],
        ],
        'default' => [
          'unit' => 'px',
          'size' => 1,
        ],
      ]
    );

    $this->add_control(
      'active_z_index',
      [
        'label'   => __( 'Z-Index' ),
        'type'    => Controls_Manager::NUMBER,
        'default' => 40,
        'min'     => 1,
        'max'     => 999,
      ]
    );

    $this->end_controls_section();
  }

  private function _style_inactive_slides() {
    $this->start_controls_section(
      'inactive_section',
      [
        'label' => __( 'Inactive Slides' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'inactive_scale',
      [
        'label'   => __( 'Scale' ),
        'type'    => Controls_Manager::SLIDER,
        'range'   => [
          'px' => [ 'min' => 0.5, 'max' => 1, 'step' => 0.01 ],
        ],
        'default' => [
          'unit' => 'px',
          'size' => 0.78,
        ],
      ]
    );

    $this->add_control(
      'inactive_opacity',
      [
        'label'   => __( 'Opacity' ),
        'type'    => Controls_Manager::SLIDER,
        'range'   => [
          'px' => [ 'min' => 0.2, 'max' => 1, 'step' => 0.05 ],
        ],
        'default' => [
          'unit' => 'px',
          'size' => 0.75,
        ],
      ]
    );

    $this->add_control(
      'inactive_blur',
      [
        'label'   => __( 'Blur (px)' ),
        'type'    => Controls_Manager::SLIDER,
        'range'   => [
          'px' => [ 'min' => 0, 'max' => 8, 'step' => 0.5 ],
        ],
        'default' => [
          'unit' => 'px',
          'size' => 0,
        ],
      ]
    );

    $this->add_control(
      'inactive_z_index',
      [
        'label'   => __( 'Z-Index' ),
        'type'    => Controls_Manager::NUMBER,
        'default' => 28,
        'min'     => 1,
        'max'     => 999,
      ]
    );

    $this->end_controls_section();
  }

  private function _style_content() {
    $this->start_controls_section(
      'content_style_section',
      [
        'label' => __( 'Slide Content' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'content_strip_heading',
      [
        'type'  => Controls_Manager::HEADING,
        'label' => __( 'Bottom Strip' ),
      ]
    );

    $this->add_control(
      'body_bg',
      [
        'label'     => __( 'Strip Background' ),
        'type'      => Controls_Manager::COLOR,
        'default'   => 'rgba(17,17,23,0.72)',
        'selectors' => [
          '{{WRAPPER}} .modern-carousel__body' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'body_padding',
      [
        'label'      => __( 'Strip Padding' ),
        'type'       => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%', 'em' ],
        'default'    => [
          'top'      => 14,
          'right'    => 18,
          'bottom'   => 14,
          'left'     => 18,
          'unit'     => 'px',
          'isLinked' => false,
        ],
        'selectors'  => [
          '{{WRAPPER}} .modern-carousel__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'icon_heading_note',
      [
        'type'  => Controls_Manager::HEADING,
        'label' => __( 'Icon' ),
      ]
    );

    $this->add_control(
      'icon_size',
      [
        'label'     => __( 'Icon Size' ),
        'type'      => Controls_Manager::SLIDER,
        'range'     => [
          'px' => [ 'min' => 14, 'max' => 64 ],
        ],
        'default'   => [
          'unit' => 'px',
          'size' => 24,
        ],
        'selectors' => [
          '{{WRAPPER}} .modern-carousel__icon' => 'font-size: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'icon_color',
      [
        'label'     => __( 'Icon Color' ),
        'type'      => Controls_Manager::COLOR,
        'default'   => '#FFFFFF',
        'selectors' => [
          '{{WRAPPER}} .modern-carousel__icon' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'icon_color_hover',
      [
        'label'     => __( 'Icon Color (Hover)' ),
        'type'      => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .modern-carousel__card:hover .modern-carousel__icon' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'heading_style_note',
      [
        'type'  => Controls_Manager::HEADING,
        'label' => __( 'Heading' ),
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name'     => 'heading_typography',
        'selector' => '{{WRAPPER}} .modern-carousel__heading',
      ]
    );

    $this->add_control(
      'heading_color',
      [
        'label'     => __( 'Heading Color' ),
        'type'      => Controls_Manager::COLOR,
        'default'   => '#FFFFFF',
        'selectors' => [
          '{{WRAPPER}} .modern-carousel__heading' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'text_style_note',
      [
        'type'  => Controls_Manager::HEADING,
        'label' => __( 'Text' ),
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name'     => 'text_typography',
        'selector' => '{{WRAPPER}} .modern-carousel__text',
      ]
    );

    $this->add_control(
      'text_color',
      [
        'label'     => __( 'Text Color' ),
        'type'      => Controls_Manager::COLOR,
        'default'   => 'rgba(255,255,255,0.75)',
        'selectors' => [
          '{{WRAPPER}} .modern-carousel__text' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_section();
  }

  private function _style_navigation() {
    $this->start_controls_section(
      'navigation_section',
      [
        'label' => __( 'Navigation' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_responsive_control(
      'arrow_size',
      [
        'label'      => __( 'Arrow Size' ),
        'type'       => Controls_Manager::SLIDER,
        'size_units' => [ 'px' ],
        'range'      => [
          'px' => [ 'min' => 32, 'max' => 80 ],
        ],
        'default'    => [
          'unit' => 'px',
          'size' => 46,
        ],
        'selectors'  => [
          '{{WRAPPER}} .modern-carousel__arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'arrow_bg',
      [
        'label'     => __( 'Background' ),
        'type'      => Controls_Manager::COLOR,
        'default'   => '#FFFFFF',
        'selectors' => [
          '{{WRAPPER}} .modern-carousel__arrow' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'arrow_color',
      [
        'label'     => __( 'Icon Color' ),
        'type'      => Controls_Manager::COLOR,
        'default'   => '#2B2B33',
        'selectors' => [
          '{{WRAPPER}} .modern-carousel__arrow' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'arrow_hover_bg',
      [
        'label'     => __( 'Hover Background' ),
        'type'      => Controls_Manager::COLOR,
        'default'   => '#A073F7',
        'selectors' => [
          '{{WRAPPER}} .modern-carousel__arrow:hover, {{WRAPPER}} .modern-carousel__arrow:focus-visible' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'arrow_hover_color',
      [
        'label'     => __( 'Hover Icon Color' ),
        'type'      => Controls_Manager::COLOR,
        'default'   => '#FFFFFF',
        'selectors' => [
          '{{WRAPPER}} .modern-carousel__arrow:hover, {{WRAPPER}} .modern-carousel__arrow:focus-visible' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'arrow_radius',
      [
        'label'      => __( 'Corner Radius' ),
        'type'       => Controls_Manager::SLIDER,
        'size_units' => [ 'px', '%' ],
        'range'      => [
          'px' => [ 'min' => 0, 'max' => 40 ],
          '%'  => [ 'min' => 0, 'max' => 50 ],
        ],
        'default'    => [
          'unit' => 'px',
          'size' => 0,
        ],
        'selectors'  => [
          '{{WRAPPER}} .modern-carousel__arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();
  }

  private function _style_pagination() {
    $this->start_controls_section(
      'pagination_section',
      [
        'label' => __( 'Pagination' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_responsive_control(
      'dot_size',
      [
        'label'      => __( 'Dot Size' ),
        'type'       => Controls_Manager::SLIDER,
        'size_units' => [ 'px' ],
        'range'      => [
          'px' => [ 'min' => 4, 'max' => 20 ],
        ],
        'default'    => [
          'unit' => 'px',
          'size' => 2,
        ],
        'selectors'  => [
          '{{WRAPPER}} .modern-carousel__dot' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'dot_active_width',
      [
        'label'      => __( 'Active Dot Width' ),
        'type'       => Controls_Manager::SLIDER,
        'size_units' => [ 'px' ],
        'range'      => [
          'px' => [ 'min' => 4, 'max' => 40 ],
        ],
        'default'    => [
          'unit' => 'px',
          'size' => 12,
        ],
        'selectors'  => [
          '{{WRAPPER}} .modern-carousel__dot.is-active' => 'width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'dot_color',
      [
        'label'     => __( 'Inactive Dot Color' ),
        'type'      => Controls_Manager::COLOR,
        'default'   => 'rgba(43,43,51,0.22)',
        'selectors' => [
          '{{WRAPPER}} .modern-carousel__dot' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'dot_active_color',
      [
        'label'     => __( 'Active Dot Color' ),
        'type'      => Controls_Manager::COLOR,
        'default'   => '#A073F7',
        'selectors' => [
          '{{WRAPPER}} .modern-carousel__dot.is-active' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $settings = $this->get_settings_for_display();
    $slides   = $settings['slides'];

    if ( empty( $slides ) || ! is_array( $slides ) ) {
      return;
    }

    $count = count( $slides );
    $uid   = 'modern-carousel-' . esc_attr( $this->get_id() );

    $loop      = ( 'yes' === ( $settings['loop'] ?? '' ) );
    $autoplay  = ( 'yes' === ( $settings['autoplay'] ?? '' ) && $loop );
    $speed     = max( 150, (int) ( $settings['transition_speed'] ?? 500 ) );
    $delay     = max( 1000, (int) ( $settings['autoplay_delay'] ?? 4000 ) );
    $scale_a   = (float) ( $settings['active_scale']['size'] ?? 1 );
    $scale_i   = (float) ( $settings['inactive_scale']['size'] ?? 0.78 );
    $opacity_i = (float) ( $settings['inactive_opacity']['size'] ?? 0.75 );
    $blur_i    = (float) ( $settings['inactive_blur']['size'] ?? 0 );
    $z_a       = max( 1, (int) ( $settings['active_z_index'] ?? 40 ) );
    $z_i       = max( 1, (int) ( $settings['inactive_z_index'] ?? 28 ) );

    $config = [
      'vd' => max( 1, (int) ( $settings['visible_slides'] ?? 3 ) ),
      'vt' => max( 1, (int) ( $settings['visible_slides_tablet'] ?? 3 ) ),
      'vm' => max( 1, (int) ( $settings['visible_slides_mobile'] ?? 1 ) ),
      'ov' => (float) ( $settings['overlap']['size'] ?? 34 ),
      'sp' => $speed,
      'lp' => $loop ? 1 : 0,
      'ap' => $autoplay ? 1 : 0,
      'de' => $delay,
      'ph' => ( 'yes' === ( $settings['pause_on_hover'] ?? '' ) ) ? 1 : 0,
      'sa' => $scale_a,
      'si' => $scale_i,
      'oi' => $opacity_i,
      'bi' => $blur_i,
      'za' => $z_a,
      'zi' => $z_i,
    ];

    $root_attributes = [
      'id'                   => $uid,
      'class'                => 'modern-carousel',
      'data-mc'              => wp_json_encode( $config ),
      'role'                 => 'region',
      'aria-roledescription' => 'carousel',
      'aria-label'           => __( 'Carousel' ),
    ];

    $style_vars = [];

    if ( ! empty( $settings['dot_size']['size'] ) ) {
      $style_vars[] = '--mc-dot-size:' . $settings['dot_size']['size'] . $settings['dot_size']['unit'];
    }

    if ( ! empty( $style_vars ) ) {
      $root_attributes['style'] = implode( ';', $style_vars ) . ';';
    }

    $this->add_render_attribute( 'root', $root_attributes );

    $mc_css  = '';
    $sel     = '#' . $uid;
    $unitify = static function ( $control ) {
      return ( isset( $control['size'], $control['unit'] ) && '' !== (string) $control['size'] )
        ? $control['size'] . $control['unit']
        : '';
    };

    $dot_wh  = $unitify( $settings['dot_size'] ?? [] );
    $act_w   = $unitify( $settings['dot_active_width'] ?? [] );
    $dot_c   = $settings['dot_color'] ?? '';
    $dot_ac  = $settings['dot_active_color'] ?? '';
    $icon_hc = $settings['icon_color_hover'] ?? '';
    $ar_wh   = $unitify( $settings['arrow_size'] ?? [] );
    $ar_bg   = $settings['arrow_bg'] ?? '';
    $ar_col  = $settings['arrow_color'] ?? '';
    $ar_hbg  = $settings['arrow_hover_bg'] ?? '';
    $ar_hcol = $settings['arrow_hover_color'] ?? '';
    $ar_rad  = $unitify( $settings['arrow_radius'] ?? [] );

    if ( $dot_wh ) {
      $mc_css .= "{$sel} .modern-carousel__slide{--mc-dot-size:{$dot_wh}}";
    }

    $dot_sz = $dot_wh ? : 'var(--mc-dot-size)';
    $mc_css .= "{$sel} .modern-carousel__dot{width:{$dot_sz};height:{$dot_sz};min-width:0;min-height:0;padding:0;border:0}";
    if ( $act_w ) {
      $mc_css .= "{$sel} .modern-carousel__dot.is-active{width:{$act_w}}";
    }
    if ( $dot_c ) {
      $mc_css .= "{$sel} .modern-carousel__dot{background-color:{$dot_c}}";
    }
    if ( $dot_ac ) {
      $mc_css .= "{$sel} .modern-carousel__dot.is-active{background-color:{$dot_ac}}";
    }
    if ( $icon_hc ) {
      $mc_css .= "{$sel} .modern-carousel__card:hover .modern-carousel__icon,{$sel} .modern-carousel__slide.is-active:hover .modern-carousel__icon{color:{$icon_hc};transition:color .2s ease}";
    }
    if ( $ar_wh ) {
      $mc_css .= "{$sel} .modern-carousel__arrow{width:{$ar_wh};height:{$ar_wh}}";
    }
    if ( $ar_bg || $ar_col ) {
      $mc_css .= "{$sel} .modern-carousel__arrow{" . trim( ( $ar_bg ? "background-color:{$ar_bg};" : '' ) . ( $ar_col ? "color:{$ar_col}" : '' ), ';' ) . '}';
    }
    if ( $ar_hbg || $ar_hcol ) {
      $mc_css .= "{$sel} .modern-carousel__arrow:hover,{$sel} .modern-carousel__arrow:focus-visible{" . trim( ( $ar_hbg ? "background-color:{$ar_hbg};" : '' ) . ( $ar_hcol ? "color:{$ar_hcol}" : '' ), ';' ) . '}';
    }
    if ( $ar_rad ) {
      $mc_css .= "{$sel} .modern-carousel__arrow{border-radius:{$ar_rad}}";
    }

    if ( $mc_css ) {
      // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
      echo '<style>' . wp_strip_all_tags( $mc_css ) . '</style>';
    }
    ?>
    <div <?php $this->print_render_attribute_string( 'root' ); ?>>

      <div class="modern-carousel__viewport" tabindex="0">
        <div class="modern-carousel__track">
          <?php foreach ( $slides as $i => $slide ) :
            $image_url  = $slide['image']['url'] ?? '';
            $heading    = $slide['heading'] ?? '';
            $text       = $slide['text'] ?? '';
            $label      = '' !== $heading ? $heading : sprintf( __( 'Slide %1$s' ), $i + 1 );
            $slide_aria = sprintf( __( '%1$s of %2$s' ), $i + 1, $count );
            ?>
            <div class="modern-carousel__slide"
              role="group"
              aria-roledescription="slide"
              aria-label="<?= esc_attr( $slide_aria ) ?>">
              <?php
              $mc_link   = $slide['link'] ?? [];
              $mc_href   = ! empty( $mc_link['url'] ) ? $mc_link['url'] : '';
              $mc_attrs  = '';
              if ( $mc_href ) {
                $mc_attrs .= ' href="' . esc_url( $mc_href ) . '"';
                if ( ! empty( $mc_link['is_external'] ) ) {
                  $mc_attrs .= ' target="_blank"';
                }
                if ( ! empty( $mc_link['nofollow'] ) ) {
                  $mc_attrs .= ' rel="nofollow"';
                }
              }
              ?>
              <<?= $mc_href ? 'a' : 'div' ?> class="modern-carousel__card-link"<?= $mc_attrs ?>>
              <div class="modern-carousel__card"
                <?php if ( ! empty( $image_url ) ) : ?>
                  style="background-image:url('<?= esc_url( $image_url ) ?>')"
                <?php endif; ?>>
                <div class="modern-carousel__body">
                  <div class="modern-carousel__details">
                    <?php if ( ! empty( $heading ) ) : ?>
                      <h3 class="modern-carousel__heading"><?= esc_html( $heading ) ?></h3>
                    <?php endif; ?>
                    <?php if ( ! empty( $text ) ) : ?>
                      <div class="modern-carousel__text"><?= wp_kses_post( wpautop( $text ) ) ?></div>
                    <?php endif; ?>
                  </div>
                  <?php if ( ! empty( $slide['icon'] ) || ! empty( $slide['icon_fa4'] ) ) : ?>
                    <span class="modern-carousel__icon">
                      <?php \Elementor\Icons_Manager::render_icon( $slide['icon'], [ 'aria-hidden' => 'true' ] ); ?>
                    </span>
                  <?php endif; ?>
                </div>
              </div>
              </<?= $mc_href ? 'a' : 'div' ?>>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <?php if ( 'yes' === ( $settings['show_arrows'] ?? '' ) && $count > 1 ) : ?>
        <button type="button" class="modern-carousel__arrow modern-carousel__arrow--prev" data-mc-prev aria-label="<?= esc_attr__( 'Previous slide' ) ?>">
          <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 18l-6-6 6-6" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button type="button" class="modern-carousel__arrow modern-carousel__arrow--next" data-mc-next aria-label="<?= esc_attr__( 'Next slide' ) ?>">
          <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      <?php endif; ?>

      <?php if ( 'yes' === ( $settings['show_dots'] ?? '' ) && $count > 1 ) : ?>
        <div class="modern-carousel__pagination" role="tablist" aria-label="<?= esc_attr__( 'Choose slide' ) ?>">
          <?php foreach ( $slides as $i => $slide ) :
            $label = $slide['heading'] ?? '';
            ?>
            <button type="button"
              class="modern-carousel__dot"
              role="tab"
              data-mc-go="<?= esc_attr( $i ) ?>"
              aria-label="<?= esc_attr( '' !== $label ? sprintf( __( 'Go to %s' ), $label ) : sprintf( __( 'Go to slide %1$s' ), $i + 1 ) ) ?>"></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <?php
  }

  // DO NOT CHANGE/UPDATE BELOW FUNCTIONS IF NOT NECESSARY

  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
    $this->_register_assets();
    $this->_register_script();
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

  private function _register_script() {
    wp_register_script( 'theme-modern-carousel-js', get_theme_file_uri( 'app/assets/lib/modern-carousel/modern-carousel.js' ), [], ASSETS_VERSION, true );
  }

  private function _get_asset_handle(): string {
    return "theme-{$this->get_name()}";
  }
}
