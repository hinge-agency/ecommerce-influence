<?php

class WcAttributes {
    public static function getVariables( $url ) {
        $attributesScript = file_get_contents( dirname( __FILE__ ) . '/template/optimonk-wc-attributes.js' );

        $cartUpdateSetRows = join( "\n    ", self::getCartSetRows() );
        $attributesScript = str_replace( '{{set_cart_data}}', $cartUpdateSetRows, $attributesScript);

        $postID = url_to_postid($url);
        $productData    = self::getWooCommerceProductData($postID);
        $productSetRows = join( "\n    ", self::getAdapterSet( $productData ) );
        $attributesScript = str_replace( '{{set_product_data}}', $productSetRows, $attributesScript);

        echo $attributesScript . "\n";
    }

    protected static function getCartSetRows() {
        $wooCommerceCartData = self::getWooCommerceCartData();

        $string = array();

        foreach ( $wooCommerceCartData['cart'] as $cartItem ) {
            $string[] = 'adapter.Cart.add("' . $cartItem['sku'] .
                '", {quantity:' . (float) $cartItem['quantity'] .
                ', price:' . (float) $cartItem['price'] .
                '});';
        }

        $string = array_merge( $string, self::getAdapterSet( $wooCommerceCartData['avs'] ) );

        return $string;
    }

    protected static function getWooCommerceCartData() {
        global $woocommerce;

        $return = array(
            'cart' => array(),
            'avs'  => array(
                'cart_total'                   => 0,
                'cart_total_without_discounts' => 0,
                'number_of_item_kinds'         => 0,
                'total_number_of_cart_items'   => 0,
                'applied_coupons'              => ''
            )
        );

        if ( self::isWooCommerce() === false ) {
            return $return;
        }

        $cartTotal              = 0;
        $numberOfItemKinds      = 0;
        $totalNumberOfCartItems = 0;

        foreach ( $woocommerce->cart->get_cart() as $item => $values ) {
            /** @var WC_Product_Simple $cartProduct */
            $cartProduct         = $values['data'];
            $cartProductName     = $cartProduct->get_title();
            $cartProductQuantity = $values['quantity'];
            $productId = self::getProductId($cartProduct);
            $cartProductPrice    = get_post_meta( $productId, '_price', true );

            $return['cart'][] = array(
                'sku'      => $cartProduct->get_sku(),
                'name'     => $cartProductName,
                'price'    => $cartProductPrice,
                'quantity' => $cartProductQuantity,
            );
            $cartTotal += $cartProductQuantity * $cartProductPrice;
            $numberOfItemKinds ++;
            $totalNumberOfCartItems += $cartProductQuantity;
        }

        $return['avs']['cart_total_without_discounts'] = $cartTotal;
        $return['avs']['cart_total']                   = (float) strip_tags( $woocommerce->cart->get_cart_total() );
        $return['avs']['number_of_item_kinds']         = $numberOfItemKinds;
        $return['avs']['total_number_of_cart_items']   = $totalNumberOfCartItems;
        $return['avs']['applied_coupons']              = join( '|', $woocommerce->cart->get_applied_coupons() );

        return $return;
    }

    protected static function getWooCommerceProductData($postID) {

        $return = array(
            'current_product.name'       => '',
            'current_product.sku'        => '',
            'current_product.price'      => '',
            'current_product.stock'      => '',
            'current_product.categories' => '',
            'current_product.tags'       => '',
        );

        if ( self::isWooCommerceProductPage(get_post($postID)) === false ) {
            return $return;
        }

        $woocommerceVersion = self::wpbo_get_woo_version_number();
        $product = wc_get_product($postID);

        $return['current_product.name']       = $product->get_title();
        $return['current_product.sku']        = $product->get_sku();
        $return['current_product.price']      = $product->get_price();
        $return['current_product.stock']      = $product->get_stock_quantity();

        if ($woocommerceVersion >= "3.0.2") {
            $productId = self::getProductId($product);
            $return['current_product.categories'] = strip_tags( wc_get_product_category_list( $productId, '|' ) );
            $return['current_product.tags']       = strip_tags( wc_get_product_tag_list( $productId, '|' ) );
        } else {
            $return['current_product.categories'] = strip_tags( $product->get_categories( '|' ) );
            $return['current_product.tags']       = strip_tags( $product->get_tags( '|' ) );
        }

        return $return;
    }

    /**
     * @param $product
     * @return mixed
     */
    protected static function getProductId($product)
    {
        $woocommerceVersion = self::wpbo_get_woo_version_number();
        return $woocommerceVersion >= '2.6.0' ? $product->get_id() : $product->id;
    }

    protected static function isWooCommerceProductPage($post) {
        return self::isWooCommerce() && get_post_type($post) === 'product';
    }

    protected static function isWooCommerce() {
        global $woocommerce;

        if (
            isset( $woocommerce ) === false
            || class_exists( '\WC_Product' ) === false
        ) {
            return false;
        }

        return true;
    }

    protected static function getAdapterSet( array $data ) {
        $string = array();

        foreach ( $data as $key => $value ) {
            $string[] = 'adapter.attr("wp_' . $key . '", "' . $value . '");';
        }

        return $string;
    }

    protected static function wpbo_get_woo_version_number() {
        // If get_plugins() isn't available, require it
        if ( ! function_exists( 'get_plugins' ) )
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        // Create the plugins folder and file variables
        $plugin_folder = get_plugins( '/' . 'woocommerce' );
        $plugin_file = 'woocommerce.php';

        // If the plugin version number is set, return it
        if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
            return $plugin_folder[$plugin_file]['Version'];

        } else {
            // Otherwise return null
            return NULL;
        }
    }
}
