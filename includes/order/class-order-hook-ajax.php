<?php
/**
 * Order Hook AJAX
 *
 * @author Team WSA
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Order_Hook_AJAX implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_action('wp_ajax_' . PREFIX . '_connect_order_affiliate', [__CLASS__, 'process_connect_affiliate']);
      add_action('wp_ajax_' . PREFIX . '_disconnect_order_affiliate', [__CLASS__, 'process_disconnect_affiliate']);

   }



   /**
    * Connect order with an affiliate.
    *
    * @return string
    */
   public static function process_connect_affiliate(){

      //check to make sure the request is from same server
      if(!check_ajax_referer( 'wsa-nonce', 'security', false )){
         return;
      }

      parse_str(Util::array($_POST)->get('fields'), $fields);

      $fields       = Util::array($fields)->get(Util::prefix('fields'));
      $aff_id       = $fields['affiliate_id'];
      $order_id     = $fields['order_id'];
      $order        = wc_get_order($order_id);
      $order_amount = Option::get('min_order_amount', 10);

      if($order instanceof \WC_Order){

         if(Service::get_order_total($order) >= $order_amount){

            if('shop_order' === $order->get_type()){

               wp_send_json(Service::register_order_transaction($aff_id, $order));

            }elseif('shop_subscription' === $order->get_type()){

               update_post_meta($order->get_id(), Util::prefix('affiliate_id'), $aff_id);

               wp_send_json([
                  'success' => true,
                  'message' => __('The affiliate has been connect with this subscription.', 'integration-sirfiliate-woocommerce'),
               ]);

            }

         }else{

            wp_send_json([
               'success' => false,
               'message' => __('The order amount is less than the minimum amount defined in settings!', 'integration-sirfiliate-woocommerce'),
            ]);
         }
      }

      wp_send_json([
         'success' => false,
         'message' => __('Invalid order!', 'integration-sirfiliate-woocommerce'),
      ]);

   }



   /**
    * Disconnect order affiliate.
    *
    * @return string
    */
   public static function process_disconnect_affiliate(){

      //check to make sure the request is from same server
      if(!check_ajax_referer( 'wsa-nonce', 'security', false )){
         return;
      }

      parse_str(Util::array($_POST)->get('fields'), $fields);

      $fields   = Util::array($fields)->get(Util::prefix('fields'));
      $order_id = $fields['order_id'];
      $trx_id   = get_post_meta($order_id, Util::prefix('transaction_id'), true);

      if( ! empty($trx_id) ){

         $api      = new API();
         $response = $api->delete_transaction($trx_id);

         if(isset($response->id)){
            delete_post_meta($order_id, Util::prefix('transaction_id'));
         }
      }

      delete_post_meta($order_id, Util::prefix('affiliate_id'));

      wp_send_json([
         'success' => true,
         'message' => __('The order affiliate has been removed.', 'integration-sirfiliate-woocommerce'),
      ]);
   }
}