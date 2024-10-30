<?php
/**
 * Product
 *
 * @author Team WSA
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;

class Product {

   /**
    * @var string The meta key for products to be excluded from calculation of commission, used with prefix
    */
   const EXCLUDE_COMMISSION_CALCULATION = 'exclude_commission_calculation';


   /**
    * Set the product to be excluded from commission calculation
    *
    * @param $product_id
    * @return void
    */
   public static function exclude_commission_calculation($product_id) {
      update_post_meta($product_id, Util::prefix(self::EXCLUDE_COMMISSION_CALCULATION), 1);
   }



   /**
    * Set the products to be included in commission calculation
    *
    * @param $product_ids
    * @return void
    */
   public static function include_commission_calculation($product_id) {
      delete_post_meta($product_id, Util::prefix(self::EXCLUDE_COMMISSION_CALCULATION));
   }

   /**
    * Show the product exclude commission calculation status icons
    *
    * @param $product_id
    * @return void
    */
   public static function exclude_commission_calculation_status($product_id) {
      if (1 == get_post_meta($product_id, Util::prefix(self::EXCLUDE_COMMISSION_CALCULATION), true)) {
         $tip = __('This product is excluded from the commission calculation', 'integration-sirfiliate-woocommerce');
         echo '<span style="color:#a30000" class="tips dashicons dashicons-dismiss" data-tip="' . esc_attr($tip) . '"></span>';
      } else {
         $tip = __('This product is included in the commission calculation', 'integration-sirfiliate-woocommerce');
         echo '<span style="color:#46b450" class="tips dashicons dashicons-yes-alt" data-tip="' . esc_attr($tip) . '"></span>';
      }
   }


   /**
    * Add hook to exclude the products of commission calculation.
    * Use as sandwich in pair with remove_exclude_commission_calculation_hook
    *
    * @param $order
    * @return void
    */
   public static function add_exclude_commission_calculation_hook($order) {
      add_filter('woocommerce_order_get_items', [__CLASS__, 'exclude_commission_calculation_hook']);
      $order->calculate_totals(); // trigger order total re-calculation
   }

   /**
    * Callback for hook woocommerce_order_get_items to exclude products from calculation
    *
    * @param $items
    * @param \WC_Order $order
    * @return void
    */
   public static function exclude_commission_calculation_hook($items) {

      foreach ($items as $key => $item) {
         /**
          * @var \WC_Order_Item_Product $item
          * @var \WC_Product $product
          */
         if (!($item instanceof \WC_Order_Item_Product)) {
            continue;
         }
         $product = $item->get_product();
         if (1 == $product->get_meta(Util::prefix(self::EXCLUDE_COMMISSION_CALCULATION))) {
            unset($items[$key]);
         }
      }

      return $items;

   }



   /**
    * Remove the hook that exclude products commission calculation.
    * Use after order get_total to not affect order calculation in different places and restore the cache of it value
    *
    * @param $order
    * @return void
    */
   public static function remove_exclude_commission_calculation_hook($order) {
      remove_filter('woocommerce_order_get_items', [__CLASS__, 'exclude_commission_calculation_hook']);
      $order->calculate_totals();// trigger order total re-calculation to get it back to normal
   }
}
