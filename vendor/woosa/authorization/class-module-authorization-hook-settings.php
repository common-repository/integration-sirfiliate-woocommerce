<?php
/**
 * Module Authorization Hook Settings
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Authorization_Hook_Settings implements Interface_Hook_Settings{


   /**
    * The id of the section.
    *
    * @return string
    */
   public static function section_id(){
      return 'authorization';
   }



   /**
    * The name of the section.
    *
    * @return string
    */
   public static function section_name(){
      return __('Authorization', 'integration-sirfiliate-woocommerce');
   }



   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_action('admin_init', [__CLASS__, 'maybe_init']);
   }



   /**
    * Initiates the section under a condition.
    *
    * @return void
    */
   public static function maybe_init(){

      $initiate = apply_filters(PREFIX . '\authorization\initiate', true);

      if($initiate){

         add_action(PREFIX . '\settings\sections\\' . SETTINGS_TAB_ID, [__CLASS__, 'add_section'], 20);
         add_action(PREFIX . '\settings\fields\\' . SETTINGS_TAB_ID . '\\'.self::section_id(), [__CLASS__, 'add_section_fields']);
         add_action('woocommerce_admin_field_' . PREFIX .'_authorization_ui', [__CLASS__, 'output_section']);
      }

   }



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

      $GLOBALS['hide_save_button'] = true;//hide submit button

      $items = [
         [
            'name' => __('Authorization', 'integration-sirfiliate-woocommerce'),
            'id'   => PREFIX . '_authorization_title',
            'type' => 'title',
         ],
         [
            'id'   => PREFIX . '_authorization_ui',
            'type' => PREFIX . '_authorization_ui',
         ],
         [
            'id'   => PREFIX . '_authorization_sectionend',
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
      Module_Authorization::render($values);
   }

}