<?php 
/**
 * Plugin Name:  Similar Products
 * Plugin URI: http://cedcommerce.com
 * Description: Add Similar products on Product Page, Cart Page according to your needs and get rid of randomly displaying products.
 * Version: 1.0.5
 * Author: CedCommerce <plugins@cedcommerce.com>
 * Author URI: http://cedcommerce.com
 * Requires at least: 3.5
 * Tested up to: 5.4.0
 * Text Domain: ced-similar-product
 * Domain Path: /languages
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('CED_SP_DIR', plugin_dir_path( __FILE__ ));
define('CED_SP_DIR_URL', plugin_dir_url( __FILE__ ));

$activated = true;
if (function_exists('is_multisite') && is_multisite())
{
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) )
	{
		$activated = false;
	}
}
else
{
	if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
	{
		$activated = false;
	}
}

/**
 * Check if WooCommerce is active
 **/
if ($activated)
{
	
	include_once CED_SP_DIR.'class/ced_similar-products-class.php';

	add_action('plugins_loaded', 'ced_sp_load_text_domain');

	/**
	 * This function is used to load language'.
	 * @name ced_sp_load_text_domain()
	 * @author CedCommerce<plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	*/

	function ced_sp_load_text_domain()
	{
		$domain = "ced-similar-product";
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, CED_SP_DIR .'languages/'.$domain.'-' . $locale . '.mo' );
		$var=load_plugin_textdomain( 'ced-similar-product', false, plugin_basename( dirname(__FILE__) ) . '/languages' );
	}
	
	
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'ced_sp_add_settings_link');
	
	
	/**
	 * add setting link to the plugin
	 * @name ced_sp_add_settings_link
	 * @author CedCommerce <plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 * @param string $links
	 * @return string
	*/
	
	function ced_sp_add_settings_link($links)
	{
		$settings_link = '<a href="'.get_admin_url().'admin.php?page=wc-settings&tab=products&section=ced_similar_products">Settings</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
	
	
	
}
else
{
	/**
	 * Show error notice if WooCommerce is not activated.
	 * @name ced_sp_plugin_error_notice()
	 * @author CedCommerce<plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */

	function ced_sp_plugin_error_notice()
	{
		?>
		<div class="error notice is-dismissible">
		<p><?php _e( 'WooCommerce is not activated. Please install WooCommerce to use the Shipping Delivery Date Management with gift message extension !!!', 'ced-similar-product' ); ?></p>
		</div>
		<?php
	}
		
	add_action( 'admin_init', 'ced_sp_plugin_deactivate' );
		
	/**
	 * Deactivate extension if WooCommerce is not activated.
	 * @name ced_sp_plugin_deactivate()
	 * @author CedCommerce<plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */
	
	function ced_sp_plugin_deactivate() 
	{
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'ced_sp_plugin_error_notice' );
	}
}


?>
