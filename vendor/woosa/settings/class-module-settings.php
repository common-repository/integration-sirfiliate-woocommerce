<?php
/**
 * Module Settings
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


if(!class_exists('\WC_Settings_Page')){
   include_once WP_CONTENT_DIR . '/plugins/woocommerce/includes/admin/settings/class-wc-settings-page.php';
}


class Module_Settings extends \WC_Settings_Page {


   /**
    * Setting page id.
    *
    * @var string
    */
   protected $id = SETTINGS_TAB_ID;


   /**
    * Setting page label.
    *
    * @var string
    */
   protected $label = SETTINGS_TAB_NAME;



   /**
    * Constructor of this class.
    *
    */
   public function __construct() {

      add_action('init', [$this, 'process_hooks']);

      add_action('updated_option', [__CLASS__, 'save_in_extra'], 99);
      add_action('added_option', [__CLASS__, 'save_in_extra'], 99);

      add_action(PREFIX . '\core\state\uninstalled', [$this, 'clean_settings']);
   }



   /**
    * Defines the hooks.
    *
    * @return void
    */
   public function process_hooks(){

      $initiate = apply_filters(PREFIX . '\module\settings\initiate', true);

      if($initiate){

         add_filter('woocommerce_settings_tabs_array', [$this, 'add_settings_page'], 50); //make sure it's at the end of default WooCommerce tabs
         add_action('woocommerce_settings_' . $this->id, [$this, 'output_sections']);
         add_action('woocommerce_settings_' . $this->id, [$this, 'output']);
         add_action('woocommerce_settings_save_' . $this->id, array( $this, 'save' ));

         add_filter('woocommerce_settings_groups', [$this, 'register_setting_tab']);
         add_filter('woocommerce_settings-' . $this->id, [$this, 'register_setting_tab_fields']);
      }
   }



   /**
    * Registers setting tab which will be available via REST API.
    *
    * @param array $locations
    * @return array
    */
    public function register_setting_tab($locations){

      $locations[] = [
         'id'    => $this->id,
         'label' => $this->label,
      ];

      return $locations;
   }



   /**
    * Registers setting fields which will be available via REST API
    *
    * @param array $locations
    * @return array
    */
   public function register_setting_tab_fields($settings){

      return array_merge(
         $settings,
         $this->get_settings(),
         $this->get_settings('authorization'),
         $this->get_settings('license')
      );

   }



   /**
    * Saves setting value in an extra given setting option if matches.
    *
    * @param string $option
    * @return void
    */
   public static function save_in_extra($option){

      $list = apply_filters(PREFIX . '\settings\save_in_extra\list', []);//['option_key_to_compare' => 'option_key_where_to_save']

      if(isset($list[$option])){
         Option::set($list[$option], Option::get($option));
      }
   }



   /**
    * Removes all options which have our prefix in case the remove config setting option is enabled.
    *
    * @return void
    */
   public function clean_settings(){

      global $wpdb;

      if('yes' === Option::get('remove_config')){

         $prefix = PREFIX;
         $wpdb->query("DELETE FROM `wp_options`
            WHERE `option_name` LIKE '{$prefix}_%'
               OR `option_name` LIKE '_{$prefix}_%'
               OR `option_name` LIKE '_transient_timeout_{$prefix}_%'
               OR `option_name` LIKE '_transient_{$prefix}_%'
         ");
      }
   }



   /**
   * Gets settings per section.
   *
   * @param string $section
   * @return array
   */
   public function get_settings( $section = null ) {

      $fields = apply_filters( PREFIX . '\settings\fields\\' . $this->id . '\\' . $section, [] );

      foreach($fields as $key => $field){
         $fields[$key]['autoload'] = false;
         $fields[$key]['option_key'] = $field['id'];
      }

      return $fields;
   }



   /**
    * Get sections.
    *
    * @return array
    */
   public function get_sections() {
      return apply_filters( PREFIX . '\settings\sections\\' . $this->id, [] );
   }



   /**
    * Renders tab content.
    *
    * @return string
    */
   public function output() {

      global $current_section;

      $settings = $this->get_settings( $current_section );
      $settings = $this->disable_fields( $settings );
      $settings = $this->exclude_fields( $settings );

      woocommerce_admin_fields( $settings );

   }



   /**
    * Save settings.
    */
   public function save() {

      global $current_section;

      $settings = $this->get_settings( $current_section );
      $settings = $this->validate_fields($settings);

      do_action(PREFIX . "\settings\before_save\{$current_section}", $settings, $_POST);

      woocommerce_update_options( $settings );

      //saves extra custom fields
      if(isset($_POST[Util::prefix('fields')])){
         foreach($_POST[Util::prefix('fields')] as $key => $value){
            Option::set($key, $value);
         }
      }

      do_action(PREFIX . "\settings\after_save\{$current_section}", $settings, $_POST);
   }



   /**
    * Disable fields based on dynamic conditions.
    *
    * @param array $fields
    * @return array
    */
   public function disable_fields( array $fields ){

      foreach( $fields as $key => $field ) {

         $disabled = apply_filters(PREFIX . '\settings\disable_field', false, $field);

         if($disabled){
            if(isset($fields[$key]['custom_attributes'])){
               $fields[$key]['custom_attributes'] = array_merge($fields[$key]['custom_attributes'], [
                  'disabled' => 'disabled'
               ]);
            }else{
               $fields[$key]['custom_attributes'] = [
                  'disabled' => 'disabled'
               ];
            }
         }
      }

      return $fields;
   }



   /**
    * Removes specific fields.
    *
    * @param array $fields
    * @return array $fields
    */
   public function exclude_fields( array $fields ) {

      foreach( $fields as $key => $field ) {
         $condition = apply_filters(PREFIX . '\settings\exclude_field\condition', isset($field['hide_in_admin']), $field);
         if ($condition) {
            unset( $fields[$key] );
         }
      }

      return $fields;

   }



   /**
    * Runs defined field validation.
    *
    * @param array $fields
    * @return array $fields
    */
   public function validate_fields( array $fields ) {

      $valid = true;

      foreach( $fields as $key => $item ) {

         if(isset($item['validation'])){

            if(isset($_POST[$item['id']])){

               $value   = $_POST[$item['id']];
               $data    = $item['validation'];
               $valid   = apply_filters(PREFIX . '\settings\validate_item', $valid, $value, $data);
               $message = Util::array($data)->get('message', "{$item['name']} - invalid value provided!");
               $message = sprintf(__('%s Provided value: %s', 'integration-sirfiliate-woocommerce'), $message, $value);

               if( ! $valid ){
                  unset($fields[$key]);
                  \WC_Admin_Settings::add_error( $message );
               }
            }
         }
      }

      return $fields;

   }

}