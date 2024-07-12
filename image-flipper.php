<?php
/*
Plugin Name: Image Flipper de Productos
Plugin URI: http://expiey.com
Version: 0.1
Description: Agrega una imagen secundaria en los archivos de productos que se revela al pasar el mouse. Muestra la imagen principal y como segunda, la primera de la galería de imágenes.
Author: Luciano Lyall
Author URI: http://expiey.com
Text Domain: flipper-de-productos
Domain Path: /languages/
*/

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    if ( ! class_exists( 'WC_pif' ) ) {

        class WC_pif {

            public function __construct() {
                add_action( 'init', array( $this, 'pif_init' ) );
                add_action( 'wp_enqueue_scripts', array( $this, 'pif_scripts' ) );
                add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'woocommerce_template_loop_second_product_thumbnail' ), 11 );
                add_filter( 'post_class', array( $this, 'product_has_gallery' ) );
            }

            public function pif_init() {
                load_plugin_textdomain( 'woocommerce-product-image-flipper', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
            }

            public function pif_scripts() {
                if ( apply_filters( 'woocommerce_product_image_flipper_styles', true ) ) {
                    wp_enqueue_style( 'pif-styles', plugins_url( '/assets/css/style.css', __FILE__ ) );
                }
            }

            public function product_has_gallery( $classes ) {
                global $product;

                $post_type = get_post_type( get_the_ID() );

                if ( ! is_admin() && $post_type == 'product' ) {
                    $attachment_ids = $this->get_gallery_image_ids( $product );
                    if ( $attachment_ids ) {
                        $classes[] = 'pif-has-gallery';
                    }
                }

                return $classes;
            }

            public function woocommerce_template_loop_second_product_thumbnail() {
                global $product;

                $attachment_ids = $this->get_gallery_image_ids( $product );

                if ( $attachment_ids ) {
                    $attachment_ids = array_values( $attachment_ids );
                    $secondary_image_id = $attachment_ids[0];

                    $secondary_image_alt = get_post_meta( $secondary_image_id, '_wp_attachment_image_alt', true );
                    $secondary_image_title = get_the_title($secondary_image_id);

                    echo wp_get_attachment_image(
                        $secondary_image_id,
                        'shop_catalog',
                        '',
                        array(
                            'class' => 'secondary-image attachment-shop-catalog wp-post-image wp-post-image--secondary',
                            'alt' => $secondary_image_alt,
                            'title' => $secondary_image_title
                        )
                    );
                }
            }

            public function get_gallery_image_ids( $product ) {
                if ( ! is_a( $product, 'WC_Product' ) ) {
                    return;
                }
                return $product->get_gallery_image_ids();
            }

        }

        $WC_pif = new WC_pif();
    }
}
?>
