<?php
/**
 * Authorization Hook Settings
 *
 * @author Team WSA
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Authorization_Hook_Settings implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_filter(PREFIX . '\authorization\initiate', '__return_false');

      add_action(PREFIX . '\settings\fields\\' . SETTINGS_TAB_ID . '\\', [__CLASS__, 'add_section_fields'], 90);

      add_filter(PREFIX . '\authorization\save_extra_fields', '__return_false');

   }



   /**
    * Adds the fields of the section.
    *
    * @param array $items
    * @return array
    */
   public static function add_section_fields($items){

      $new_items = [
         [
            'name' => __('Authorization', 'integration-sirfiliate-woocommerce'),
            'id'   => PREFIX . '_authorization_title',
            'type' => 'title',
            'desc' => Module_Authorization::render_status(),
         ],
         [
            'name'     => __('API Key', 'integration-sirfiliate-woocommerce'),
            'id'       => PREFIX .'_api_key',
            'type'     => 'password',
            'desc_tip' => __('You can find this on your account.', 'integration-sirfiliate-woocommerce'),
            'disable'  => ['if_authorized'],
         ],
         [
            'name'     => __('Sandbox', 'integration-sirfiliate-woocommerce'),
            'id'       => PREFIX .'_api_sandbox',
            'type'     => 'checkbox',
            'desc' => __('Enable/disable', 'integration-sirfiliate-woocommerce'),
            'disable'  => ['if_authorized'],
         ],
         [
            'id'   => PREFIX . '_authorization_sectionend',
            'type' => 'sectionend',
         ]
      ];

      return array_merge($new_items, $items);
   }

}