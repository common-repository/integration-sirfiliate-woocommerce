<?php
/**
 * Service Hook
 *
 * @author Team WSA
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Service_Hook implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_action(PREFIX . '\authorization\connect', [__CLASS__, 'connect_env'], 10, 2);

      add_action('init', [__CLASS__, 'save_affiliate']);
   }



   /**
    * Grant the access to the service.
    *
    * @param array $output
    * @param Module_Authorization $ma
    * @return array
    */
   public static function connect_env($output, $ma){

      $api = new API();

      if( ! $api->is_valid_key() ){
         $output = [
            'success' => false,
            'message' => __('Granting authorization has failed, please check if the API key is correct.', 'integration-sirfiliate-woocommerce'),
         ];
      }

      return $output;
   }



   /**
    * Saves the affiliate ID from URL as cookie.
    *
    * @return void
    */
   public static function save_affiliate(){

      if( isset($_GET['ref']) ){
         Service::set_affiliate_id( $_GET['ref'] );
      }

   }

}