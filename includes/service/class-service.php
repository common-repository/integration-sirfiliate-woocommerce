<?php
/**
 * Service
 *
 * @author Team WSA
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Service{


   /**
    * Sets the affiliate ID to cookie or user meta.
    *
    * @param string $value
    * @param int $user_id
    * @return void
    */
   public static function set_affiliate_id($value, $user_id = 0){

      $valid = self::is_valid_affiliate($value);

      if($valid){

         if(empty($user_id)){

            if( ! isset($_COOKIE[Util::prefix('affiliate_id')]) ){

               $time = Option::get('reference_lifetime', 30) * \DAY_IN_SECONDS;

               setcookie(Util::prefix('affiliate_id'), $value, time()+$time, \COOKIEPATH, \COOKIE_DOMAIN);

            }

         }else{
            update_user_meta($user_id, Util::prefix('affiliate_id'), $value);
         }

      }elseif(DEBUG){

         Util::wc_debug_log([
            'message' => 'This affiliate ID '.$value.' is invalid'
         ]);
      }

   }



   /**
    * Retrieves the affiliate ID.
    *
    * @param \WC_Order $order
    * @return string
    */
   public static function get_affiliate_id($order){

      $value = '';

      if('yes' === Option::get('recognize_aff_coupon')){

         foreach($order->get_coupon_codes() as $code){

            $code = strtoupper($code);

            if(self::is_valid_affiliate($code)){
               $value = $code;
               break;
            }
         }
      }

      if(empty($value)){
         $value = get_post_meta($order->get_id(), Util::prefix('affiliate_id'), true);
      }

      if(empty($value)){
         $user_id = $order->get_customer_id();
         $value = get_user_meta($user_id, Util::prefix('affiliate_id'), true);
      }

      if(empty($value)){
         $value = Util::array($_COOKIE)->get( Util::prefix('affiliate_id') );
      }

      return $value;
   }



   /**
    * Checks whether or not the affiliate is a valid one.
    *
    * @param string $affiliate_id
    * @return boolean
    */
   public function is_valid_affiliate($affiliate_id){

      $result = false;
      $api    = new API;
      $list   = $api->get_affiliates();

      foreach($list as $item){
         if($affiliate_id === $item->id){
            $result = true;
            break;
         }
      }

      return $result;
   }



   /**
    * Retrieves order total.
    *
    * @param \WC_Order $order
    * @return string
    */
   public static function get_order_total(\WC_Order $order){

      $total = (float) $order->get_total();
      return number_format($total, 2, '.', '');

   }



   /**
    * Registers a transaction for the given affiliate and order.
    *
    * @param string $affiliate_id
    * @param \WC_Order $order
    * @return array
    */
   public static function register_order_transaction($affiliate_id, \WC_Order $order){

      $output = [
         'success' => false,
         'message' => __('Something went wrong, please try again', 'integration-sirfiliate-woocommerce')
      ];
      $created = $order->get_meta(Util::prefix('transaction_id'));

      if($created){

         $output['message'] = __('This order already have a transaction created in Sir Filiate!', 'integration-sirfiliate-woocommerce');

      }elseif(empty($affiliate_id)){

         $output['message'] = __('Invalid Sir Filiate affiliate!', 'integration-sirfiliate-woocommerce');

      }else{

         Product::add_exclude_commission_calculation_hook($order);

         $api      = new API();
         $user_id  = $order->get_customer_id();
         $response = $api->add_transaction([
            'affiliate'   => $affiliate_id,
            'value'       => Service::get_order_total($order),
            'description' => sprintf('Created by order #%s on %s', $order->get_id(), parse_url(home_url(), PHP_URL_HOST)),
            'metadata'    => [
               'order_id' => $order->get_id()
            ],
         ]);

         Product::remove_exclude_commission_calculation_hook($order);

         if(isset($response->id)){

            $order->update_meta_data(Util::prefix('affiliate_id'), $affiliate_id);
            $order->update_meta_data(Util::prefix('transaction_id'), $response->id);
            $order->save_meta_data();

            self::set_affiliate_id($affiliate_id, $user_id);

            $output = [
               'success' => true,
               'message' => __('The Sir Filiate transaction has been successfully created!', 'integration-sirfiliate-woocommerce')
            ];

         }else{

            $output['message'] = __('The Sir Filiate transaction could have not been created, please check logs.', 'integration-sirfiliate-woocommerce');

         }

      }

      if( ! $output['success'] ){
         $order->add_order_note($output['message']);
         $order->save();
      }

      return $output;

   }
}