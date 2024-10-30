<?php
/**
 * Module Settings Hook General
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Settings_Hook_General implements Interface_Hook_Settings{


   /**
    * The id of the section.
    *
    * @return string
    */
   public static function section_id(){
      return '';//leave empty to be the default section
   }



   /**
    * The name of the section.
    *
    * @return string
    */
   public static function section_name(){
      return __('Settings', 'integration-sirfiliate-woocommerce');
   }



   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_action('init', [__CLASS__, 'maybe_init']);

      add_filter(PREFIX . '\core\plugin_action_links', [__CLASS__, 'add_action_link']);

      add_action('woocommerce_admin_field_' . PREFIX .'_ian_source', [__CLASS__, 'output_field_value_source'], 99);//deprecated since 1.0.1
      add_action('woocommerce_admin_field_' . PREFIX .'_field_value_source', [__CLASS__, 'output_field_value_source'], 99);
      add_action('woocommerce_admin_field_' . PREFIX .'_use_wc_price', [__CLASS__, 'output_use_wc_price'], 99);

   }



   /**
    * Initiates the section under a condition.
    *
    * @return void
    */
   public static function maybe_init(){

      $initiate = apply_filters(PREFIX . '\module\settings\general\initiate', true);

      if($initiate){
         add_filter(PREFIX . '\settings\sections\\' . SETTINGS_TAB_ID, [__CLASS__, 'add_section']);
         add_filter(PREFIX . '\settings\fields\\' . SETTINGS_TAB_ID . '\\'.self::section_id(), [__CLASS__, 'add_section_fields']);
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
    * @param array $fields
    * @return array
    */
   public static function add_section_fields($items){

      $items[] = [
         'name' => __('Misc', 'integration-sirfiliate-woocommerce'),
         'id'   => PREFIX . '_misc_title',
         'type' => 'title',
         'desc' => '',
      ];

      if(defined(__NAMESPACE__ . '\PRESERVE_STOCK_OFFSET') && PRESERVE_STOCK_OFFSET){
         $items[] = [
            'name'     => __('Preserve Stock Offset', 'integration-sirfiliate-woocommerce'),
            'id'       => PREFIX .'_preserve_stock_offset',
            'desc_tip' => __('Define a value that will be subtracted from the product stock. This will help to avoid selling out of stock products.', 'integration-sirfiliate-woocommerce'),
            'type'     => 'number',
            'default'  => '0',
            'validation' => [
               'message'     => __('"Preserve Stock Offset" must be greater than or equal with 0.', 'integration-sirfiliate-woocommerce'),
               'value'       => [
                  [
                     'compare'  => 0,
                     'operator' => '>='
                  ]
               ],
            ]
         ];
      }

      $items[] = [
         'name' => __('Debug Mode', 'integration-sirfiliate-woocommerce'),
         'id'   => PREFIX .'_debug',
         'type' => 'checkbox',
         'desc' => __('Enable', 'integration-sirfiliate-woocommerce'),
         'desc_tip' => __('Set whether or not to enable debug mode.', 'integration-sirfiliate-woocommerce'),
         'default' => 'no',
         'autoload' => false,
      ];

      $items[] = [
         'name' => __('Remove Configuration', 'integration-sirfiliate-woocommerce'),
         'id'   => PREFIX .'_remove_config',
         'type' => 'checkbox',
         'desc' => __('Yes', 'integration-sirfiliate-woocommerce'),
         'desc_tip' => __('Set whether or not to remove the plugin configuration on uninstall.', 'integration-sirfiliate-woocommerce'),
         'default' => 'no',
         'autoload' => false,
      ];

      $items[] = [
         'type' => 'sectionend',
         'id'   => PREFIX . '_misc_sectionend',
      ];

      return $items;
   }



   /**
    * Useful in conjunction with the hook `woocommerce_admin_field_{$field}` to completely render a custom content in the section.
    *
    * @param array $values
    * @return string
    */
   public static function output_section($values){}



   /**
    * Adds action link to the settings page.
    *
    * @param array $links
    * @return array
    */
   public static function add_action_link($links){

      $links['actions'] = array_merge([
         SETTINGS_URL => __('Settings', 'integration-sirfiliate-woocommerce')
      ], $links['actions']);

      return $links;
   }



   /**
    * Renders the template for the value source field.
    *
    * @param array $value
    * @return string
    */
   public static function output_field_value_source($value){

      /**
       * Backward compatibility
       * @since 1.0.1
       */

      $f_ids = [
         Util::prefix('ean'),
         Util::prefix('identifier_code_source'),
      ];

      $old_1 = Option::get('ian_custom_field_name');
      $old_2 = Option::get('ian_attribute_name');

      foreach($f_ids as $id){

         if($value['id'] == $id){

            if( ! empty($old_1) ){
               Option::set("{$value['id']}__custom_field_name", $old_1);
               Option::delete('ian_custom_field_name');
            }
            if( ! empty($old_2) ){
               Option::set("{$value['id']}__attribute_name", $old_2);
               Option::delete('ian_attribute_name');
            }
         }
      }
      //end of backward compatibily

      $source            = Option::get($value['id'], $value['default']);
      $custom_field_name = Option::get("{$value['id']}__custom_field_name");
      $attribute_name    = Option::get("{$value['id']}__attribute_name");

      $display_field1 = $source === 'custom_field' ? 'display:block;' : 'display:none;';
      $display_field2 = $source === 'attribute' ? 'display:block;' : 'display:none;';

      echo Util::get_template(\dirname(__FILE__).'/templates/field-source.php', [
         'source'            => $source,
         'parent_field_id'   => Util::unprefix( $value['id'] ),
         'custom_field_name' => $custom_field_name,
         'attribute_name'    => $attribute_name,
         'display_field1'    => $display_field1,
         'display_field2'    => $display_field2,
         'value'             => $value,
      ]);

   }



   /**
    * Renders the HTML of `Use the default WooCommerce` price filed.
    *
    * @param array $value
    * @return string
    */
   public static function output_use_wc_price($value){

      $use_wc_price   = Option::get($value['id'], $value['default']);
      $price_addition = Option::get('price_addition');
      $display_field  = 'yes' === $use_wc_price ? 'display:block;' : 'display:none;';

      echo Util::get_template(\dirname(__FILE__).'/templates/use-wc-price.php', [
         'use_wc_price'   => $use_wc_price,
         'price_addition' => $price_addition,
         'display_field'  => $display_field,
         'value'          => $value,
      ]);

   }
}