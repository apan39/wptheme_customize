<?php

/**
 * Child theme functions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development
 * and http://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions would be used.
 *
 * Text Domain: oceanwp
 * @link http://codex.wordpress.org/Plugin_API
 *
 */
/**
 * Load the parent style.css file
 *
 * @link http://codex.wordpress.org/Child_Themes
 */
function oceanwp_child_enqueue_parent_style()
{
	// Dynamically get version number of the parent stylesheet (lets browsers re-cache your stylesheet when you update your theme)
	$theme = wp_get_theme('OceanWP');
	$version = $theme->get('Version');
	// Load the stylesheet
	//wp_enqueue_style( 'parent-theme', get_template_directory_uri() . '/style.css');
	//wp_dequeue_style('parent-theme');
	wp_enqueue_style('custom-fa', 'https://use.fontawesome.com/releases/v5.0.6/css/all.css');
	wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('oceanwp-style'), $version);
}

// JJ load theme last
add_action('wp_enqueue_scripts', 'oceanwp_child_enqueue_parent_style', PHP_INT_MAX);

// //add_action('woocommerce_before_add_to_cart_button', 'bbloomer_custom_action', 5);
// function woocommerce_template_product_description()
// {
//     wc_get_template('single-product/tabs/description.php');
// }
// add_action('woocommerce_before_single_product_summary', 'woocommerce_template_product_description', 20);


// add_filter('woocommerce_product_tabs', '__return_false', 10, 2);

// JJ Change text on sale badge
add_filter('woocommerce_sale_flash', 'woocommerce_custom_sale_text', 10, 3);
function woocommerce_custom_sale_text($text, $post, $_product)
{
	return '<span class="onsale">Kampanj!</span>';
}

// JJ Change text on buy button
add_filter('add_to_cart_text', 'woo_custom_cart_button_text');
//For Single Product Page.
add_filter('woocommerce_product_single_add_to_cart_text', 'woo_single_custom_cart_button_text');
function woo_single_custom_cart_button_text()
{
	return __('Lägg i kundvagn', 'woocommerce');
}

//For Archives Product Page.
add_filter('woocommerce_product_add_to_cart_text', 'woo_custom_cart_button_text');
function woo_custom_cart_button_text()
{
	return __('Köp', 'woocommerce');
}

/**
 * Trim zeros in price decimals for products
 **/
add_filter('woocommerce_price_trim_zeros', '__return_true');


/**
 * WooCommerce cart icon
 *
 * @since 1.4.4
 */
if (!function_exists('oceanwp_woo_cart_icon_shortcode')) {

	function oceanwp_woo_cart_icon_shortcode($atts)
	{

		// Return if WooCommerce is not enabled or if admin to avoid error
		if (
			!class_exists('WooCommerce')
			|| is_admin()
		) {
			return;
		}

		// Return if is in the Elementor edit mode, to avoid error
		if (
			class_exists('Elementor\Plugin')
			&& \Elementor\Plugin::$instance->editor->is_edit_mode()
		) {
			return esc_html__('This shortcode only works in front end', 'ocean-extra');
		}

		extract(shortcode_atts(array(
			'class' 			=> '',
			'style' 			=> 'drop_down',
			'custom_link' 		=> '',
			'total' 			=> false,
			'cart_style' 		=> 'compact',
			'hide_if_empty' 	=> false,
			'color' 			=> '',
			'hover_color' 		=> '',
			'count_color' 		=> '',
			'count_hover_color' => '',
		), $atts));

		// Return items if "hide if empty cart" is checked (for mobile)
		if (
			true == $hide_if_empty
			&& !WC()->cart->cart_contents_count > 0
		) {
			return;
		}

		// Toggle class
		$toggle_class = 'toggle-cart-widget';

		// Define classes to add to li element
		$classes = array('woo-menu-icon', 'bag-style', 'woo-cart-shortcode');

		// Add style class
		$classes[] = 'wcmenucart-toggle-' . $style;

		// Cart style
		if ('compact' != $cart_style) {
			$classes[] = $cart_style;
		}

		// Prevent clicking on cart and checkout
		if ('custom_link' != $style && (is_cart() || is_checkout())) {
			$classes[] = 'nav-no-click';
		}

		// Add toggle class
		else {
			$classes[] = $toggle_class;
		}

		// If custom class
		if (!empty($class)) {
			$classes[] = $class;
		}

		// Turn classes into string
		$classes = implode(' ', $classes);

		// URL
		if ('custom_link' == $style && $custom_link) {
			$url = esc_url($custom_link);
		} else {
			$cart_id = wc_get_page_id('cart');
			if (function_exists('icl_object_id')) {
				$cart_id = icl_object_id($cart_id, 'page');
			}
			$url = get_permalink($cart_id);
		}

		// Style
		if (
			!empty($color)
			|| !empty($hover_color)
			|| !empty($count_color)
			|| !empty($count_hover_color)
		) {

			// Vars
			$css = '';
			$output = '';

			if (!empty($color)) {
				$css .= '.woo-cart-shortcode .fas fa-shopping-cart .wcmenucart-count {color:' . $color . '; border-color:' . $color . ';}';
				$css .= '.woo-cart-shortcode .fas fa-shopping-cart .wcmenucart-count:after {border-color:' . $color . ';}';
			}

			if (!empty($hover_color)) {
				$css .= '.woo-cart-shortcode.bag-style:hover .fas fa-shopping-cart .wcmenucart-count, .show-cart .wcmenucart-cart-icon .wcmenucart-count {background-color: ' . $hover_color . '; border-color:' . $hover_color . ';}';
				$css .= '.woo-cart-shortcode.bag-style:hover .fas fa-shopping-cart .wcmenucart-count:after, .show-cart .wcmenucart-cart-icon .wcmenucart-count:after {border-color:' . $hover_color . ';}';
			}

			if (!empty($count_color)) {
				$css .= '.woo-cart-shortcode .fas fa-shopping-cart .wcmenucart-count {color:' . $count_color . ';}';
			}

			if (!empty($count_hover_color)) {
				$css .= '.woo-cart-shortcode.bag-style:hover .fas fa-shopping-cart .wcmenucart-count, .show-cart .wcmenucart-cart-icon .wcmenucart-count {color:' . $count_hover_color . ';}';
			}

			// Add style
			if (!empty($css)) {
				echo "<style type=\"text/css\">\n" . wp_strip_all_tags(oceanwp_minify_css($css)) . "\n</style>";
			}
		}

		ob_start(); ?>

		<div class="<?php echo esc_attr($classes); ?>">

			<?php  // JJ Check if mobile then add a different cart icon  
			?>

			<?php if (wp_is_mobile()) { ?>

				<span class="wcmenucart-shortcode"></span>
				<?php
				if (true == $total) { ?>
					<span class="wcmenucart-total"><?php WC()->cart->get_cart_total(); ?></span>
				<?php } ?>
				<span class="fas fa-shopping-cart">
					<span class="wcmenucart-count"><?php WC()->cart->get_cart_contents_count(); ?></span>
				</span>
				</a>
			<?php } else { ?>
				<a href="<?php echo esc_url($url); ?>" class="wcmenucart-shortcode">
					<?php
					if (true == $total) { ?>
						<span class="wcmenucart-total"><?php WC()->cart->get_cart_total(); ?></span>
					<?php } ?>
					<span class="fas fa-shopping-cart">
						<span class="wcmenucart-count"><?php WC()->cart->get_cart_contents_count(); ?></span>
					</span>
				</a>
			<?php } ?>


			<?php
			if (
				'drop_down' == $style
				&& !is_cart()
				&& !is_checkout()
			) { ?>
				<div class="current-shop-items-dropdown owp-mini-cart clr">
					<div class="current-shop-items-inner clr">
						<?php the_widget('WC_Widget_Cart', 'title='); ?>
					</div>
				</div>
			<?php } ?>
		</div>

<?php
		return ob_get_clean();
	}
}
add_shortcode('oceanwp_woo_cart', 'oceanwp_woo_cart_icon_shortcode');

// add_action('wp_enqueue_scripts', 'tthq_add_custom_fa_css');

// function tthq_add_custom_fa_css()
// {
//     wp_enqueue_style('custom-fa', 'https://use.fontawesome.com/releases/v5.0.6/css/all.css');
// }

// JJ Remove shop or product in woocommerce url Commented out this code 2019-05-18
/*
* Remove /product/ or /shop/ ... support %product_cat%
* Author: levantoan.com
*/
// function devvn_remove_slug( $post_link, $post ) {
// 	if ( !in_array( get_post_type($post), array( 'product' ) ) || 'publish' != $post->post_status ) {
// 			return $post_link;
// 	}
// 	if('product' == $post->post_type){
// 			$post_link = str_replace( '/product/', '/', $post_link ); //replace "product" to your slug
// 	}else{
// 			$post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );
// 	}
// 	return $post_link;
// }
// add_filter( 'post_type_link', 'devvn_remove_slug', 10, 2 );

// function devvn_woo_product_rewrite_rules($flash = false) {
// 	global $wp_post_types, $wpdb;
// 	$siteLink = esc_url(home_url('/'));
// 	foreach ($wp_post_types as $type=>$custom_post) {
// 			if($type == 'product'){
// 					if ($custom_post->_builtin == false) {
// 							$querystr = "SELECT {$wpdb->posts}.post_name, {$wpdb->posts}.ID
// 													FROM {$wpdb->posts} 
// 													WHERE {$wpdb->posts}.post_status = 'publish'
// 													AND {$wpdb->posts}.post_type = '{$type}'";
// 							$posts = $wpdb->get_results($querystr, OBJECT);
// 							foreach ($posts as $post) {
// 									$current_slug = get_permalink($post->ID);
// 									$base_product = str_replace($siteLink,'',$current_slug);
// 									add_rewrite_rule($base_product.'?$', "index.php?{$custom_post->query_var}={$post->post_name}", 'top');
// 							}
// 					}
// 			}
// 	}
// 	if ($flash == true)
// 			flush_rewrite_rules(false);
// }
// add_action('init', 'devvn_woo_product_rewrite_rules');
// /*Fix 404*/
// function devvn_woo_new_product_post_save($post_id){
// 	global $wp_post_types;
// 	$post_type = get_post_type($post_id);
// 	foreach ($wp_post_types as $type=>$custom_post) {
// 			if ($custom_post->_builtin == false && $type == $post_type) {
// 					devvn_woo_product_rewrite_rules(true);
// 			}
// 	}
// }
// add_action('wp_insert_post', 'devvn_woo_new_product_post_save');

// /*
// * Remove product-category in URL
// * Author: levantoan.com
// */
// add_filter( 'term_link', 'devvn_product_cat_permalink', 10, 3 );
// function devvn_product_cat_permalink( $url, $term, $taxonomy ){
// 	switch ($taxonomy):
// 			case 'product_cat':
// 					$taxonomy_slug = 'product-category'; //Change product-category to your product category slug
// 					if(strpos($url, $taxonomy_slug) === FALSE) break;
// 					$url = str_replace('/' . $taxonomy_slug, '', $url);
// 					break;
// 	endswitch;
// 	return $url;
// }
// // Add our custom product cat rewrite rules
// function devvn_product_category_rewrite_rules($flash = false) {
// 	$terms = get_terms( array(
// 			'taxonomy' => 'product_cat',
// 			'post_type' => 'product',
// 			'hide_empty' => false,
// 	));
// 	if($terms && !is_wp_error($terms)){
// 			$siteurl = esc_url(home_url('/'));
// 			foreach ($terms as $term){
// 					$term_slug = $term->slug;
// 					$baseterm = str_replace($siteurl,'',get_term_link($term->term_id,'product_cat'));
// 					add_rewrite_rule($baseterm.'?$','index.php?product_cat='.$term_slug,'top');
// 					add_rewrite_rule($baseterm.'page/([0-9]{1,})/?$', 'index.php?product_cat='.$term_slug.'&paged=$matches[1]','top');
// 					add_rewrite_rule($baseterm.'(?:feed/)?(feed|rdf|rss|rss2|atom)/?$', 'index.php?product_cat='.$term_slug.'&feed=$matches[1]','top');
// 			}
// 	}
// 	if ($flash == true)
// 			flush_rewrite_rules(false);
// }
// add_action('init', 'devvn_product_category_rewrite_rules');
// /*Fix 404 when creat new term*/
// add_action( 'create_term', 'devvn_new_product_cat_edit_success', 10, 2 );
// function devvn_new_product_cat_edit_success( $term_id, $taxonomy ) {
// 	devvn_product_category_rewrite_rules(true);
// }

// Allways show price at cart on single product page
// Because Google did not like a price range(needed to hide it)
function ssp_always_show_variation_prices($show, $parent, $variation)
{
	return true;
}
add_filter('woocommerce_show_variation_price', 'ssp_always_show_variation_prices', 99, 3);



/* Manually add structured data until Elementor fixes this */
add_action('wp_head', 'add_json_ld_head', 3);
function add_json_ld_head()
{
	if (is_product()) {
		global $product;

		// We should first check if there are any _GET parameters available
		// When there are not we are on a variable product page but not on a specific variable one
		// In that case we need to put in the AggregateOffer structured data
		$nr_get = count($_GET);

		if ($nr_get > 0) {
			// This is a variable product
			$mother_id = wc_get_product()->get_id();
			$children_ids = $product->get_children();
			$prod_type = $product->get_type();

			if ($prod_type == "variable") {
				foreach ($children_ids as &$child_val) {
					$product_variations = new WC_Product_Variation($child_val);
					$variations = array_filter($product_variations->get_variation_attributes());
					$from_url = str_replace("\\", "", $_GET, $i);
					$intersect = array_intersect($from_url, $variations);
					if ($variations == $intersect) {
						$variation_id = $child_val;
					}
				}
			}


			if (isset($variation_id)) {
				$variable_product = wc_get_product($variation_id);


				//$product = $variable_product;
				$image_id = $variable_product->get_image_id();
				$image_url = wp_get_attachment_image_url($image_id, 'full');
				$valid_date = date('Y-m-d', strtotime('Dec 31'));
				$taxonomy    = 'pa_brand';
				//$brand_names = wp_get_post_terms($product->get_id(), $taxonomy, array('fields' => 'names'));
				//$brand_name = reset($brand_names);
				$organization = get_bloginfo('name');
				echo "<script type=\"application/ld+json\">{
				\"@context\":\"http://schema.org\",
				\"@graph\":[{
					\"@type\":\"Product\",
					\"name\":\"" . $variable_product->get_name() . "\",
					\"image\":\"" . $image_url . "\",
					\"description\":\"" . wp_strip_all_tags($product->get_short_description(), true) . "\",
					\"sku\":\"" . $variable_product->get_sku() . "\",					
					\"brand\":{
						\"@type\":\"Brand\",
						\"name\":\"Holea\"
					},
					\"offers\":[{
						\"@type\":\"Offer\",
						\"url\":\"" . get_permalink($variable_product->get_id()) . "\",
						\"priceCurrency\":\"SEK\",
						\"price\":\"" . $variable_product->get_price() . "\",
						\"priceValidUntil\":\"" . $valid_date . "\",
						\"itemCondition\": \"https://schema.org/NewCondition\",
						\"availability\":\"http://schema.org/InStock\",
						\"seller\":{
							\"@type\":\"Organization\",
							\"name\":\"" . $organization . "\",
							\"url\":\"" . site_url() . "\"
						}
					}]
				}]
			}</script>";
			}
		}







		//global $product;
		// $image_id = $product->get_image_id();
		// $image_url = wp_get_attachment_image_url($image_id, 'full');
		// $valid_date = date('Y-m-d', strtotime('Dec 31'));
		// $taxonomy    = 'pa_brand';
		// $brand_names = wp_get_post_terms($product->get_id(), $taxonomy, array('fields' => 'names'));
		// $brand_name = reset($brand_names);
		// $organization = get_bloginfo('name');
		// echo "<script type=\"application/ld+json\">{
		// 	\"@context\":\"http://schema.org\",
		// 	\"@graph\":[{
		// 		\"@type\":\"Product\",\"@id\":\"" . $product->get_id(). "\",
		// 		\"name\":\"" . $product->get_name(). "\",
		// 		\"url\":\"" . get_permalink($product->get_id()). "\",
		// 		\"description\":\"" . wp_strip_all_tags($product->get_short_description(), true). "\",
		// 		\"image\":\"" . $image_url. "\",
		// 		\"sku\":" . $product->get_id() . ",". "
		// 		\"offers\":[{
		// 			\"@type\":\"Offer\",
		// 			\"price\":\"" . $product->get_price(). "\",
		// 			\"priceSpecification\":{\"price\":\"" . $product->get_price(). "\",
		// 			\"priceCurrency\":\"EUR\",
		// 			\"valueAddedTaxIncluded\":\"true\"},
		// 			\"priceValidUntil\":\"" . $valid_date. "\",
		// 			\"availability\":\"http://schema.org/InStock\",
		// 			\"priceCurrency\":\"EUR\",
		// 			\"seller\":{
		// 				\"@type\":\"Organization\",
		// 				\"name\":\"". $organization . "\",
		// 				\"url\":\"" . site_url(). "\"
		// 			}
		// 		}],
		// 		\"mainEntityOfPage\":\"" . get_permalink($product->get_id()). "\",
		// 		\"brand\":{
		// 			\"@type\":\"Organization\",
		// 			\"name\":\"" . $brand_name. "\"
		// 		},
		// 		\"manufacturer\":{
		// 			\"@type\":\"Organization\",
		// 			\"name\":\"" . $brand_name. "\"
		// 		}
		// 	}]
		// }</script>";
	}
}
