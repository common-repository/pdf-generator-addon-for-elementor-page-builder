<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class RTWWPGE_Widget_PDF extends Widget_Base {
	private $rtw_pgaepb_stng;
	public function get_name() {
		return 'rtw-pdf-generator-addon';
	}

	public function get_title() {
		return __( 'PDF - Generator', 'elementor' );
	}

	public function get_icon() {
		return 'eicon-post-content';
	}

    public function get_categories() {
		return [ 'basic' ];
	}


	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'PDF Settings', 'elementor' )
			]
		);

		$this->add_control(
			'pdf_width',
			[
				'label' => __( 'PDF Button Width', 'elementor' ),
				'type' => Controls_Manager::NUMBER,
				'placeholder' => __( 'Enter PDF Button Width', 'elementor' ),
				'default' => 64,
			]
		);

		$this->add_control(
			'pdf_height',
			[
				'label' => __( 'PDF Button Height', 'elementor' ),
				'type' => Controls_Manager::NUMBER,
				'placeholder' => __( 'Enter PDF Button Height', 'elementor' ),
				'default' => 64,
			]
		);

        $this->add_control(
		  'pdf_image',
		  [
		     'label' => __( 'PDF Button Icon', 'elementor' ),
		     'type' => Controls_Manager::MEDIA,
		     'default' => [
		        'url' => RTW_PGAEPB_URL.'/public/images/pdf_down_icon.png',
		     ],
		  ]
		);

        $this->add_control(
			'pdf_class',
			[
				'label' => __( 'Exclude HTML Class', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'For multiple classes use commma', 'elementor' )
			]
		);

        $this->add_control(
			'pdf_id',
			[
				'label' => __( 'Exclude HTML ID', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'For multiple IDs use commma', 'elementor' )
			]
		);

        $this->end_controls_section();
	}

	protected function render() {
        $settings = $this->get_settings();
        $rtw_basic_stng = get_option('rtw_pgaepb_basic_setting_opt');
		if( !$rtw_basic_stng )
		{
			$rtw_basic_stng = array();
		}
		$rtw_css_stng = get_option('rtw_pgaepb_css_setting_opt');
		if( !$rtw_css_stng )
		{
			$rtw_css_stng = array();
		}
		$rtw_header_stng = get_option('rtw_pgaepb_header_setting_opt');
		if( !$rtw_header_stng )
		{
			$rtw_header_stng = array();
		}
		$rtw_footer_stng = get_option('rtw_pgaepb_footer_setting_opt');
		if( !$rtw_footer_stng )
		{
			$rtw_footer_stng = array();
		}
		$rtw_watermark_stng = get_option('rtw_pgaepb_watermark_setting_opt');
		if( !$rtw_watermark_stng )
		{
			$rtw_watermark_stng = array();
		}
		$this->rtw_pgaepb_stng = array_merge($rtw_basic_stng,$rtw_css_stng,$rtw_header_stng,$rtw_footer_stng,$rtw_watermark_stng);

		global $post;
		
		if(empty( $this->rtw_pgaepb_stng['post_type'] ) )
		{
			return;
		}
		if ( !array_key_exists( get_post_type(), $this->rtw_pgaepb_stng['post_type'] ) )
		{
			return;
		}
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) 
		{
			if( is_cart() )
			{
				if ( !isset($rtw_basic_stng['rtw_pgaepb_show_pdf_btn']['on_cart']) )
				{
					return;
				}
				if (isset($rtw_basic_stng['rtw_pgaepb_show_pdf_btn']['on_cart']) && $rtw_basic_stng['rtw_pgaepb_show_pdf_btn']['on_cart'] == 0 )
				{
					return;
				}
			}
			if( is_shop() )
			{
				if ( !isset($rtw_basic_stng['rtw_pgaepb_show_pdf_btn']['on_shop']) )
				{
					return;
				}
				if (isset($rtw_basic_stng['rtw_pgaepb_show_pdf_btn']['on_shop']) && $rtw_basic_stng['rtw_pgaepb_show_pdf_btn']['on_shop'] == 0 )
				{
					return;
				}
			}
			if( is_checkout() )
			{
				if ( !isset($rtw_basic_stng['rtw_pgaepb_show_pdf_btn']['on_checkout']) )
				{
					return;
				}
				if (isset($rtw_basic_stng['rtw_pgaepb_show_pdf_btn']['on_checkout']) && $rtw_basic_stng['rtw_pgaepb_show_pdf_btn']['on_checkout'] == 0 )
				{
					return;
				}
			}
		}
	   	$rtw_pgaepb_img = wp_get_attachment_image_src( $settings['pdf_image']['id'], 'full' );
	   	if( !$rtw_pgaepb_img )
	   	{
	   		$rtw_pgaepb_image[0] = $settings['pdf_image']['url'];
			$rtw_pgaepb_img = $rtw_pgaepb_image;
		}
	   	$rtw_pgaepb_width = '50px';
	   	$rtw_pgaepb_height = '50px';
	   	if( isset( $settings['pdf_width'] ) && !empty( $settings['pdf_width'] ) )
	   	{
	   		$rtw_pgaepb_width = sanitize_text_field($settings['pdf_width']);
	   	}
	   	if( isset( $settings['pdf_height'] ) && !empty( $settings['pdf_height'] ) )
	   	{
	   		$rtw_pgaepb_height = sanitize_text_field($settings['pdf_height']);
	   	}

	   	// CHECK PDF FILE EXIST FOR CACHING
	   	if( isset($this->rtw_pgaepb_stng['file_name'] ) && $this->rtw_pgaepb_stng ['file_name'] == 'post_name') 
		{
			$rtw_file_path = RTW_PDF_DIR . '/' . $post->post_name . '.pdf';
		} 
		else  {
			$rtw_file_path = RTW_PDF_DIR . '/' .$post->ID. '.pdf';
		}
		
		$rtw_is_cache = true;
	   	if( !file_exists( $rtw_file_path ) )
	   	{
			$rtw_is_cache = false;
		}
		$rtw_html = '<div class="rtw_pgaepb_main">
			<a style="cursor:pointer;" target="_blank" rel="noindex,nofollow" data-post_url="'.esc_url( add_query_arg( 'generate_pdf', 'true', get_permalink( $post->ID ) ) ).'" data-post_id="'.esc_attr($post->ID).'" data-pdf_cache="'.esc_attr($rtw_is_cache).'" data-pdf_class="'.esc_attr($settings['pdf_class']).'" data-pdf_id="'.esc_attr($settings['pdf_id']).'" title="Download PDF" class="rtwwpge_pdf_button">
				<img alt="'.__('Download PDF','pdf-generator-addon-for-elementor-page-builder').'" src="'.$rtw_pgaepb_img[0].'" width="'.esc_attr($rtw_pgaepb_width).'" height="'.esc_attr($rtw_pgaepb_height).'">
			</a>
			<img src="'.RTW_PGAEPB_URL.'/public/images/spinner.gif" class="rtwwpge_pdf_gif">
		</div>';
		
		echo $rtw_html;
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new RTWWPGE_Widget_PDF() );