<?php
/**
 * Module Logger Hook
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Logger_Hook{


   /**
    * Initiates.
    *
    * @return void
    */
   public static function init(){

      add_action('init', [__CLASS__, 'process_criteria_list']);

      add_filter(PREFIX . '\core\plugin_action_links', [__CLASS__, 'add_action_link']);

      add_action('admin_init', [__CLASS__, 'check_connected_options']);

      add_action('updated_option', [__CLASS__, 'process_connected_options'], 30);
      add_action('added_option', [__CLASS__, 'process_connected_options'], 30);
   }



   /**
    * Processes the list of criteria.
    *
    * @return void
    */
   public static function process_criteria_list(){

      /**
       * [
       *    'log_code' => [
       *       'type'    => 'warning|error|debug|info',
       *       'message' => 'message goes here',
       *       'hook'    => 'init', //on which hook to be checked
       *       'active'  => false|true, //true means to create and false to remove
       *    ]
       * ]
       */
      $list = apply_filters(PREFIX . '\logger\criteria_list', []);

      foreach($list as $code => $item){

         add_filter(PREFIX .'\logger\messages', function($messages) use ($code, $item){
            $messages[$code] = $item['message'];
            return $messages;
         });

         add_action($item['hook'], function() use ($code, $item){

            $logger = new Module_Logger;

            if($item['active']){
               $logger->{'set_'.$item['type']}($code, [], __FILE__, __LINE__);
            }else{
               $logger->remove_log($code);
            }

         });

      }
   }



   /**
    * Adds action link to the settings page.
    *
    * @param array $links
    * @return array
    */
   public static function add_action_link($links){

      $links['actions'] = array_merge([
         SETTINGS_URL . '&section=logs' => __('Logs', 'integration-sirfiliate-woocommerce')
      ], $links['actions']);

      return $links;
   }



   /**
    * Check if connected options are saved in the logs.
    *
    * @return void
    */
   public static function check_connected_options(){

      $logger = new Module_Logger;

      foreach($logger->get_connected_options() as $key => $option){
         $logger->set_log_from_option($key);
      }
   }



   /**
    * Sets the log when a connected option is added/updated.
    *
    * @param string $option_name
    * @return void
    */
   public static function process_connected_options( $option_name ) {

      $logger = new Module_Logger;
      $logger->set_log_from_option($option_name, true);
   }

}