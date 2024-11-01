<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! class_exists( 'CED_SIMILAR_PRODUCTS' ) )
{
	//Custom action hook for add to cart and others
	
	add_action( 'ced_similar_product_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
	add_action( 'ced_similar_product_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	
	class CED_SIMILAR_PRODUCTS
	{
		/**
		 * This is a class constructor where all actions and filters are defined'.
		 * @name __construct()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		public function __construct()
		{
			add_filter( 'woocommerce_product_data_tabs', array($this , 'ced_similar_product_tabs'), 10, 2);
			add_action( 'woocommerce_product_data_panels', array($this , 'ced_similar_product_html'));
			add_action( 'save_post', array($this , 'ced_similar_product_save'));
			// add_action( 'woocommerce_after_shop_loop_item', array($this , 'ced_similar_product_listing'), 15);
			add_action( 'wp_enqueue_scripts', array($this , 'ced_similar_product_assets'));
			add_action( 'woocommerce_after_single_product_summary', array($this , 'ced_similar_product_detail_page'), 15);
			add_action( 'woocommerce_cart_collaterals', array($this , 'ced_similar_product_cart_page'), 5);
			add_filter( 'woocommerce_get_sections_products', array($this , 'ced_similar_product_setting_menu'), 25);
			add_filter( 'woocommerce_get_settings_products', array($this , 'ced_similar_product_setting_page'), 10, 2);
			add_action( 'woocommerce_settings_save_products', array( $this, 'ced_similar_product_setting_save'));
		}
		
		/**
		 * Save Similar Product setting.
		 * @name ced_similar_product_setting_page()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		function ced_similar_product_setting_save()
		{
			global $current_section;
			$setting = array();
			$settings = $this->ced_similar_product_setting_page( $setting, $current_section );
			WC_Admin_Settings::save_fields( $settings );
		}
		
		/**
		 * Add Similar Products setting.
		 * @name ced_similar_product_setting_page()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		function ced_similar_product_setting_page($settings = array() ,$current_section)
		{
			if($current_section == 'ced_similar_products')
			{
				$settings = apply_filters( 'ced_similar_products_settings', array(
					array(
						'title' 	=> __( 'Settings', 'woocommerce' ),
						'type' 		=> 'title',
					),
	
					array(
						'title'    => __( 'Title', 'woocommerce' ),
						'placeholder' => __( 'Similar Products','woocommerce' ),
						'desc'     => __( 'Heading under which similar products shown', 'woocommerce' ),
						'css'      => 'min-width:300px;',
						'type'     => 'text',
						'id'            => 'ced_similar_product_heading',
						'desc_tip' =>  true,
					),
					array(
							'title'    => __( 'No. of similar products display in list', 'woocommerce' ),
							'desc'     => __( 'No. of similar products display in list', 'woocommerce' ),
							'css'      => 'min-width:300px;',
							'type'     => 'number',
							'id'       => 'ced_similar_product_count',
							'desc_tip' =>  true,
					),
					array(
							'title'    => __( 'Custom CSS', 'woocommerce' ),
							'desc'     => __( 'Custom CSS for output', 'woocommerce' ),
							'css'      => 'min-width:300px;',
							'type'     => 'textarea',
							'id'       => 'ced_similar_product_css',
							'desc_tip' =>  true,
					),
					// array(
					// 	'title'           => __( 'Similar Products Visibility', 'woocommerce' ),
					// 	'desc'            => __( 'Shop Page', 'woocommerce' ),
					// 	'default'         => 'yes',
					// 	'type'            => 'checkbox',
					// 	'checkboxgroup'   => 'start',
					// 	'id'            => 'ced_similar_product_shop_visibility',
					// 	'autoload'        => false
					// ),
	
					array(
						'title'           => __( 'Similar Products Visibility', 'woocommerce' ),
						'desc'            => __( 'Product Detail Page', 'woocommerce' ),
						'default'         => 'yes',
						'type'            => 'checkbox',
						'checkboxgroup'   => 'start',
						'id'            => 'ced_similar_product_product_page_visibility',
						'autoload'        => false
					),
	
					array(
						'desc'            => __( 'Cart Page', 'woocommerce' ),
						'default'         => 'yes',
						'type'            => 'checkbox',
						'checkboxgroup'   => '',
						'id'            => 'ced_similar_product_cart_visibility',
						'autoload'        => false
					),
	
					array(
						'type' 	=> 'sectionend',
						'id' 	=> 'ced_similar_product_setting'
					),
	
				));
			}
			return $settings;
		}
		
		/**
		 * Add Similar Products tabs in setting page.
		 * @name ced_similar_product_setting_menu()
		 * @param $sections
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		function ced_similar_product_setting_menu($sections)
		{
			$sections['ced_similar_products'] = __( 'Similar Products', 'woocommerce' );
			return $sections;
		}
		
		/**
		 * Add Similar Products tabs in Admin product page.
		 * @name ced_similar_product_tabs()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function ced_similar_product_tabs($product_data_tabs)
		{
			$product_data_tabs['ced_similar_products'] = array(
							'label'  => __( 'Similar Products', 'ced-similar-product' ),
							'target' => 'ced_similar_product_data',
							'class'  => array(),
						);
			return $product_data_tabs;
		}
		
		/**
		 * Add Similar Products html in Admin.
		 * @name ced_similar_product_html()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function ced_similar_product_html()
		{
			global $post;
			$ced_similar_product_discount = get_post_meta( $post->ID, '_ced_similar_product_discount', true);
			?>
			<div id="ced_similar_product_data" class="panel woocommerce_options_panel hidden">
				<div class="options_group">
					<p class="form-field">
					<label for="similar_product_ids"><?php _e( 'Similar Products', 'ced-similar-product' ); ?></label>
					<select class="wc-product-search" multiple="multiple" style="width: 80%;" id="similar_product_ids" name="similar_product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo intval( $post->ID ); ?>">
						<?php
							$product_ids = get_post_meta( $post->ID, '_similar_product_ids', true );	

							foreach ( $product_ids as $product_id ) {
								$product = wc_get_product( $product_id );
								if ( is_object( $product ) ) {
									echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
								}
							}
						?>
					</select> <?php echo wc_help_tip( __( 'Similar products which you recommend instead of the currently viewed product, for example, products that are more profitable or better quality or more expensive.', 'ced-similar-product' ) ); ?>
					</p>
				</div>
				<?php do_action( 'woocommerce_product_options_similar_product' ); ?>
			</div>
			<?php 
		}
		
		/**
		 * Save Similar products in admin
		 * @name ced_similar_product_save()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function ced_similar_product_save()
		{
			global $post;
			$similar_products = isset( $_POST['similar_product_ids'] ) ? $_POST['similar_product_ids'] : array();

			update_post_meta( $post->ID, '_similar_product_ids', $similar_products );
		}	
		
		/**
		 * Create similar product popup
		 * @name ced_similar_product_listing()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function ced_similar_product_listing()
		{
			$visible =  get_option('ced_similar_product_shop_visibility');
			$title =  get_option('ced_similar_product_heading');
			if($title == '')
			{
				$title = __('Similar Products', 'ced-similar-product');
			}	
			
			if($visible == 'yes')
			{
				global $product, $woocommerce_loop;
				$similar_products = $this->get_similar_products();
				$similar_products_count =  get_option('ced_similar_product_count', '');
				if(!empty($similar_products_count)){
					$posts_per_page = $similar_products_count;
				}else{
					$posts_per_page = -1;
				}
				
				if ( sizeof( $similar_products ) > 0 ) 
				{
					$orderby ='';
					$meta_query = WC()->query->get_meta_query();
					
					if(WC()->version<'3.0.0')
                {
                   $args = array(
							'post_type'           => 'product',
							'ignore_sticky_posts' => 1,
							'no_found_rows'       => 1,
							'posts_per_page'      => $posts_per_page,
							'orderby'             => $orderby,
							'post__in'            => $similar_products,
							'post__not_in'        => array( $product->id ),
							'meta_query'          => $meta_query
					);
                }
                else
                {    
                  $args = array(
							'post_type'           => 'product',
							'ignore_sticky_posts' => 1,
							'no_found_rows'       => 1,
							'posts_per_page'      => $posts_per_page,
							'orderby'             => $orderby,
							'post__in'            => $similar_products,
							'post__not_in'        => array( $product->get_id() ),
							'meta_query'          => $meta_query
					);
                }


					
		
					$products = new WP_Query( $args );
					//$woocommerce_loop['columns'] = $columns;
					$ced_similar_product_loop = $woocommerce_loop['loop'];
					
					if ( $products->have_posts() ) : ?>
						<h3 itemprop="name" class="ced_open_similar_products product_title entry-title"><?php echo $title ?></h3>
						<div class="ced_similar_products products" style="display:none;">
							<div class="ced_similar_product_wrapper">
								<h3 style="float:left;"><?php echo $title ?></h3>
								<a class="ced_similar_products_close" href="javascript:void(0);"><img src="<?php echo CED_SP_DIR_URL?>/assets/images/close.png" style="width: 30px;"></a>
								<div class="clear"></div>
								<hr/>
								<?php 
								woocommerce_product_loop_start();
								while ( $products->have_posts() ) : 
									$products->the_post();  
									global $product, $woocommerce_loop;
									if ( ! $product || ! $product->is_visible() ) 
									{
										return;
									}
									$classes = array();
									$classes[] = 'last';
									?>
									<li <?php post_class( $classes ); ?> style="margin-right:2%;">
									<?php
										do_action( 'woocommerce_before_shop_loop_item' );
										do_action( 'woocommerce_before_shop_loop_item_title' );
										do_action( 'woocommerce_shop_loop_item_title' );
										do_action( 'woocommerce_after_shop_loop_item_title' );
										do_action( 'ced_similar_product_after_shop_loop_item' );
									?>
									</li>
									<?php 
								endwhile;
								woocommerce_product_loop_end(); 
								?>
							</div>
						</div>
					<?php 
					endif;
					$woocommerce_loop['loop'] = $ced_similar_product_loop;
					wp_reset_postdata();
				}
			}
		}	

		/**
		 * Returns the similar product ids.
		 * @return array
		 */
		public function get_similar_products() 
		{
			global $post;
			$product_ids = array_filter( array_map( 'absint', (array) get_post_meta( $post->ID, '_similar_product_ids', true ) ) );
			return apply_filters( 'woocommerce_product_similar_ids', $product_ids );
		}
		
		/**
		 * Returns the similar product ids.
		 * @return array
		 */
	
		public function get_cart_similar_products($product_id) 
		{
			global $post;
			$product_ids = array_filter( array_map( 'absint', (array) get_post_meta( $product_id, '_similar_product_ids', true ) ) );
			return apply_filters( 'woocommerce_cart_product_similar_ids', $product_ids );
		}
		
		/**
		 * Add css and Scripts
		 * @name ced_similar_product_listing()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function ced_similar_product_assets()
		{	
			wp_enqueue_script( 'ced_sp_js', CED_SP_DIR_URL.'/assets/js/ced-similar-product.js', array('jquery'));
			wp_enqueue_style( 'ced_sp_css', CED_SP_DIR_URL.'/assets/css/ced-similar-product.css', false );
			$customCss = get_option('ced_similar_product_css');
			if (isset ( $customCss ) ) {
				wp_add_inline_style( 'ced_sp_css', $customCss );
			}
		}	
		
		/**
		 * List similar products on product detail page
		 * @name ced_similar_product_detail_page()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function ced_similar_product_detail_page()
		{
			$visible =  get_option('ced_similar_product_product_page_visibility');
			$title =  get_option('ced_similar_product_heading');
			if($title == '')
			{
				$title = __('Similar Products', 'ced-similar-product');
			}
				
			if($visible == 'yes')
			{
				global $product, $woocommerce_loop;
				$similar_products = $this->get_similar_products();
				$similar_products_count =  get_option('ced_similar_product_count', '');
				if(!empty($similar_products_count)){
					$posts_per_page = $similar_products_count; 
				}else{
					$posts_per_page = -1;
				}
				if ( sizeof( $similar_products ) === 0 ) 
				{
					return;
				}
				$orderby = '';
				$meta_query = WC()->query->get_meta_query();
				
				if(WC()->version<'3.0.0')
                {
                    $args = array(
						'post_type'           => 'product',
						'ignore_sticky_posts' => 1,
						'no_found_rows'       => 1,
						'posts_per_page'      => $posts_per_page,
						'orderby'             => $orderby,
						'post__in'            => $similar_products,
						'post__not_in'        => array( $product->id ),
						'meta_query'          => $meta_query
				);
                }
                else
                {    
                   $args = array(
						'post_type'           => 'product',
						'ignore_sticky_posts' => 1,
						'no_found_rows'       => 1,
						'posts_per_page'      => $posts_per_page,
						'orderby'             => $orderby,
						'post__in'            => $similar_products,
						'post__not_in'        => array( $product->get_id() ),
						'meta_query'          => $meta_query
				);
                }


				
				$products = new WP_Query( $args );
				//$woocommerce_loop['columns'] = 3;
				$ced_similar_product_loop = $woocommerce_loop['loop'];
				if ( $products->have_posts() ) : ?>
				<div class="products ced_similar_prod_div">
					<h3 style="float:left;"><?php echo $title;?></h3>
					<?php 
					woocommerce_product_loop_start();
					while ( $products->have_posts() ) : 
						$products->the_post();
						global $product, $woocommerce_loop;
						
						$classes = array();
						$classes[] = 'last';
						$classes[] = 'ced_similar_products_items';
						?>
						<li <?php post_class( $classes ); ?> style="margin-right:2%;">
							<?php
							do_action( 'woocommerce_before_shop_loop_item' );
							do_action( 'woocommerce_before_shop_loop_item_title' );
							do_action( 'woocommerce_shop_loop_item_title' );
							do_action( 'woocommerce_after_shop_loop_item_title' );
							do_action( 'ced_similar_product_after_shop_loop_item' );
							?>
						</li>
					<?php 
					endwhile;
					woocommerce_product_loop_end(); ?>
				</div>
				<?php 
				endif;
				$woocommerce_loop['loop'] = $ced_similar_product_loop;
				wp_reset_postdata();
			}
		}

		/**
		 * List similar products on cart page
		 * @name ced_similar_product_detail_page()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function ced_similar_product_cart_page()
		{
			$visible =  get_option('ced_similar_product_cart_visibility');
			$title =  get_option('ced_similar_product_heading');
			if($title == '')
			{
				$title = __('Similar Products', 'ced-similar-product');
			}
			
			if($visible == 'yes')
			{
				$ced_similar_cart_product = array();
				$similar_title = true;
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item )
				{
					global $product, $woocommerce_loop;
					$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
					$ced_similar_cart_product[] = $product_id;
				}
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) 
				{
					global $product, $woocommerce_loop;
					
					$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
					$ced_similar_cart_product[] = $product_id;
					$similar_products = $this->get_cart_similar_products($product_id);
					$similar_products_count =  get_option('ced_similar_product_count', '');
					if(!empty($similar_products_count)){
						$posts_per_page = $similar_products_count;
					}else{
						$posts_per_page = -1;
					}
					if ( sizeof( $similar_products ) > 0 ) 
					{
						$orderby = '';
						$meta_query = WC()->query->get_meta_query();
						$args = array(
								'post_type'           => 'product',
								'ignore_sticky_posts' => 1,
								'no_found_rows'       => 1,
								'posts_per_page'      => $posts_per_page,
								'orderby'             => $orderby,
								'post__in'            => $similar_products,
								'post__not_in'        => array( $product_id ),
								'meta_query'          => $meta_query
							);
						
						$products = new WP_Query( $args );
						//$woocommerce_loop['columns'] = 4;
						$ced_similar_product_loop = $woocommerce_loop['loop'];
						if ( $products->have_posts() ) : 
							while ( $products->have_posts() ) : $products->the_post();
								
								global $product, $woocommerce_loop;

								if(WC()->version<'3.0.0')
					                {
					                   $ced_similar_product_id = $product->id;
					                }
					                else
					                {    
					                    $ced_similar_product_id = $product->get_id();
					                }

								
								if(!in_array($ced_similar_product_id, $ced_similar_cart_product))
								{
									if($similar_title)
									{
										?>
										<div class="products">
										<h3 style="float:left;"><?php echo $title;?></h3>
										<?php 
										$similar_title = false;
										woocommerce_product_loop_start();
									}
									
									$classes = array();
									$classes[] = 'last';
									?>
									<li <?php post_class( $classes ); ?> style="margin-right:2%;">
									<?php
										do_action( 'woocommerce_before_shop_loop_item' );
										do_action( 'woocommerce_before_shop_loop_item_title' );
										do_action( 'woocommerce_shop_loop_item_title' );
										do_action( 'woocommerce_after_shop_loop_item_title' );
										do_action( 'ced_similar_product_after_shop_loop_item' );
									?>
									</li>
									<?php 
								}
							endwhile; 
						endif;
						$woocommerce_loop['loop'] = $ced_similar_product_loop;
						wp_reset_postdata();
					}	
				}
				if(!$similar_title)
				{
					woocommerce_product_loop_end();
					?>
					</div>
					<?php 
				}
			}
		}	
	}
	new CED_SIMILAR_PRODUCTS();
}	
?>