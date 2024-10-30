<?php
/**
 * Action Bulker Hook
 *
 * @author Team WSA
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Action_Bulker_Hook implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_filter(PREFIX . '\action_bulker\actions', [__CLASS__, 'bulk_actions']);

   }



   /**
    * List of available bulk actions.
    *
    * @return array
    */
   public static function bulk_actions($items){

      $items[PREFIX . '_exclude_commission_calculation'] = [
         'label'         => __('Sir Filiate: Exclude commission calculation', 'woosa-shipitsmarter'),
         'post_type'     => ['product'],
         'callback'      => [Product::class, 'exclude_commission_calculation'],
         'schedulable'   => false,
         'bulk_perform'  => false,
         'validate_item' => true,
      ];
      $items[PREFIX . '_include_commission_calculation'] = [
         'label'         => __('Sir Filiate: Include commission calculation', 'woosa-shipitsmarter'),
         'post_type'     => ['product'],
         'callback'      => [Product::class, 'include_commission_calculation'],
         'schedulable'   => false,
         'bulk_perform'  => false,
         'validate_item' => true,
      ];

      return $items;
   }


}