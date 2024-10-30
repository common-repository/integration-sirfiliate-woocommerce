<?php
/**
 * Settings Hook Overview
 *
 * @author Team WSA
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Settings_Hook_Overview implements Interface_Hook_Settings{


   /**
    * The id of the section.
    *
    * @return string
    */
   public static function section_id(){
      return 'overview';
   }



   /**
    * The name of the section.
    *
    * @return string
    */
   public static function section_name(){
      return __('Overview', 'integration-sirfiliate-woocommerce');
   }



   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_filter(PREFIX . '\settings\sections\\' . SETTINGS_TAB_ID, [__CLASS__, 'add_section'], 12);
      add_filter(PREFIX . '\settings\fields\\' . SETTINGS_TAB_ID . '\\'.self::section_id(), [__CLASS__, 'add_section_fields']);
      add_action('woocommerce_admin_field_' . PREFIX .'_overview_content', [__CLASS__, 'output_section']);

   }



   /**
    * Initiates the section under a condition.
    *
    * @return void
    */
   public static function maybe_init(){}



   /**
    * Adds the section in the list.
    *
    * @param array $items
    * @return array
    */
   public static function add_section($items){

      $items[self::section_id()] = self::section_name();

      return $items;
   }



   /**
    * Adds the fields of the section.
    *
    * @param array $items
    * @return array
    */
   public static function add_section_fields($items){

      $GLOBALS['hide_save_button'] = true;

      $items = [
         [
            'name' => __('Overview', 'integration-sirfiliate-woocommerce'),
            'id'   => PREFIX . '_overview_title',
            'type' => 'title',
         ],
         [
            'name' => __( 'Key', 'integration-sirfiliate-woocommerce' ),
            'id'   => PREFIX . '_overview_content',
            'type' => PREFIX . '_overview_content',
         ],
         [
            'id'   => PREFIX . '_overview_sectionend',
            'type' => 'sectionend',
         ],
      ];

      return $items;
   }



   /**
    * Useful in conjunction with the hook `woocommerce_admin_field_{$field}` to completely render a custom content in the section.
    *
    * @param array $values
    * @return string
    */
   public static function output_section($values){

      $api        = new API();
      $affiliates = $api->get_affiliates();

      echo Util::get_template('overview.php', [
         'values'     => $values,
         'affiliates' => $affiliates
      ], dirname(__FILE__), '/templates');

   }
}