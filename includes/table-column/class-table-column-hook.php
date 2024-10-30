<?php
/**
 * Table Column Hook
 *
 * @author Team WSA
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;

class Table_Column_Hook implements Interface_Hook {

   /**
    * Init the hooks
    *
    * @return void
    */
   public static function init() {
      add_filter(PREFIX . '\table_column\columns', [__CLASS__, 'add_column']);
   }

   /**
    * Callback for \table_column\columns filter
    *
    * @param $items
    * @return mixed
    */
   public static function add_column($items) {
      $items[PREFIX . '_product_exclude_commission_calculation_status'] = [
         'label'        => __('Sir Filiate', 'integration-sirfiliate-woocommerce'),
         'post_type'    => ['product'],
         'after_column' => 'product_cat',
         'callback'     => [Product::class, 'exclude_commission_calculation_status'],
      ];

      return $items;

   }
}