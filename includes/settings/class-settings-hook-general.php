<?php
/**
 * Settings Hook General
 *
 * @author Team WSA
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Settings_Hook_General implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_filter(PREFIX . '\settings\fields\\' . SETTINGS_TAB_ID . '\\', [__CLASS__, 'add_section_fields'], 11);

   }



   /**
    * Adds the fields of the section.
    *
    * @param array $fields
    * @return array
    */
   public static function add_section_fields($items){

      $new_items = [
         [
            'name' => __('General', 'integration-sirfiliate-woocommerce'),
            'type' => 'title',
            'desc' => '',
            'id'   => PREFIX . '_general_section_title',
         ],
         [
            'name'     => __('Minimum order amount', 'integration-sirfiliate-woocommerce'),
            'id'       => PREFIX .'_min_order_amount',
            'desc_tip' => __('Set the minimium amount of order for which a transaction will be created in Sir Filiate.', 'integration-sirfiliate-woocommerce'),
            'default'  => '10',
            'type'     => 'number',
         ],
         [
            'name'     => __('Reference lifetime', 'integration-sirfiliate-woocommerce'),
            'id'       => PREFIX .'_reference_lifetime',
            'desc_tip' => __('How long (in days) the reference ID will be stored in cookie?', 'integration-sirfiliate-woocommerce'),
            'default'  => '30',
            'type'     => 'number',
         ],
         [
            'name'     => __('Check affiliate via coupon', 'integration-sirfiliate-woocommerce'),
            'id'       => PREFIX .'_recognize_aff_coupon',
            'desc_tip' => __('Whether or not to recognize the affiliate via coupon code?', 'integration-sirfiliate-woocommerce'),
            'default'  => 'no',
            'type'     => 'checkbox',
         ],
         [
            'type' => 'sectionend',
            'id'   => PREFIX . '_general_section_sectionend',
         ],
      ];

      $items = array_merge($new_items, $items);

      return $items;
   }

}