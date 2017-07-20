<?php

/*
 Plugin Name: WC Restore "Purchased Items" Column in Orders Page
 Plugin URI: https://profiles.wordpress.org/rynald0s
 Description: This plugin restores the "Purchased Items" column in the orders page. 
 Author: Rynaldo Stoltz
 Author URI: https://github.com/rynaldos
 Version: 1.0
 License: GPLv3 or later License
 URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

/**
 * Check if WooCommerce is active
 **/

add_filter('manage_edit-shop_order_columns', 'wc_custom_purchased_column');

function wc_custom_purchased_column($columns)
{
    $new_array = array();
    foreach ($columns as $key => $title) {
        if ($key == 'billing_address') {

            $new_array['order_items'] = __('Purchased', 'woocommerce');
        }

        $new_array[$key] = $title;
    }
    return $new_array;
}

add_action('manage_shop_order_posts_custom_column', 'wc_shop_custom_column', 10, 2);

function wc_shop_custom_column($column)
{
    global $post, $woocommerce, $the_order;
    switch ($column) {

        case 'order_items':
            $terms = $the_order->get_items();

            echo '<a href="#" class="show_order_items">' . apply_filters( 'woocommerce_admin_order_item_count', sprintf( _n( '%d item', '%d items', $the_order->get_item_count(), 'woocommerce' ), $the_order->get_item_count() ), $the_order ) . '</a>';

                if ( sizeof( $the_order->get_items() ) > 0 ) {

                    echo '<table class="order_items" cellspacing="0">';

                    foreach ( $the_order->get_items() as $item ) {
                        $product        = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
                        $item_meta = (WC()->version < '3.1.0') ? new WC_Order_Item_Meta( $item ) : new WC_Order_Item_Product; 
                        $item_meta_html = (WC()->version < '3.1.0') ? $item_meta->display( true, true ) : $item_meta->get_product(); 
                        //$item_meta      = new WC_Order_Item_Meta( $item, $product );
                        //$item_meta_html = $item_meta->display( true, true );
                        ?>
                        <tr class="<?php echo apply_filters( 'woocommerce_admin_order_item_class', '', $item, $the_order ); ?>">
                            <td class="qty"><?php echo esc_html( $item->get_quantity() ); ?></td>
                            <td class="name">
                                <?php  if ( $product ) : ?>
                                    <?php echo ( wc_product_sku_enabled() && $product->get_sku() ) ? $product->get_sku() . ' - ' : ''; ?><a href="<?php echo get_edit_post_link( $product->get_id() ); ?>"><?php echo apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ); ?></a>
                                <?php else : ?>
                                    <?php echo apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ); ?>
                                <?php endif; ?>
                                <?php if ( ! empty( $item_meta_html ) ) : ?>
                                    <?php echo wc_help_tip( $item_meta_html ); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php
                    }

                    echo '</table>';

                } else echo '&ndash;';
            break;

        }
}
