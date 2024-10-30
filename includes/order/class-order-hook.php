<?php
/**
 * Order Hook
 *
 * @author Team WSA
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Order_Hook implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

      add_action('woocommerce_subscription_payment_complete', [__CLASS__, 'set_subscription_affiliate']);

      add_action('woocommerce_order_status_completed', [__CLASS__, 'add_affiliate_transaction']);
      add_action('woocommerce_order_status_cancelled', [__CLASS__, 'remove_affiliate_transaction']);
      add_action('woocommerce_order_status_failed', [__CLASS__, 'remove_affiliate_transaction']);
      add_action('woocommerce_order_status_refunded', [__CLASS__, 'remove_affiliate_transaction']);
   }



   /**
    * Adds the metabox.
    *
    * @return void
    */
   public static function add_metabox() {

      add_meta_box(
         PREFIX . '_connect_affiliate',
         __('Sir Filiate', 'integration-sirfiliate-woocommerce'),
         [__CLASS__, 'output_metabox'],
         ['shop_order', 'shop_subscription'],
         'side',
         'core'
      );
   }



   /**
    * Displays the metabox content.
    *
    * @since 1.0.1 - restrict only to completed/active status
    * @param \WP_Post $post
    * @return string
    */
   public static function output_metabox($post){

      $api       = new API();
      $post_type = get_post_type( $post );
      $aff_id    = get_post_meta($post->ID, Util::prefix('affiliate_id'), true);
      $created   = get_post_meta($post->ID, Util::prefix('transaction_id'), true);
      $disabled  = in_array($post->post_status, ['wc-completed', 'wc-active']) ? false : true;
      $field_att = $disabled ? 'disabled="disabled"' : '';
      $box_att   = $disabled ? '' : 'data-' . PREFIX . '-order-affiliate';

      ?>
      <div <?php echo esc_attr($box_att);?>>

         <?php if( ! empty($created) ): ?>
            <p><?php printf(__('Transaction created for affiliate: %s', 'integration-sirfiliate-woocommerce'), '<b>'.esc_html($aff_id).'</b>');?></p>

            <p>
               <button type="button" class="button" data-action="disconnect"><?php _e('Disconnect', 'integration-sirfiliate-woocommerce');?></button>
            </p>

         <?php elseif( ! empty($aff_id) && 'shop_subscription' === $post_type): ?>

            <p><?php printf(__('Future orders will generate transactions for affiliate: %s', 'integration-sirfiliate-woocommerce'), '<b>'.$aff_id.'</b>');?></p>

            <p>
               <button type="button" class="button" data-action="disconnect"><?php _e('Disconnect', 'integration-sirfiliate-woocommerce');?></button>
            </p>

         <?php else: ?>

            <p><?php _e('Choose which affiliate to connect with', 'integration-sirfiliate-woocommerce');?></p>

            <select name="<?php echo PREFIX;?>_fields[affiliate_id]" <?php echo esc_attr($field_att);?> data-<?php echo PREFIX;?>-select2="yes">
               <option value=""><?php _e('Please select', 'integration-sirfiliate-woocommerce');?></option>
               <?php foreach($api->get_affiliates() as $item):?>
                  <option value="<?php echo esc_attr($item->id);?>"><?php echo esc_html($item->name);?> (<?php echo esc_html($item->email);?>)</option>
               <?php endforeach;?>
            </select>

            <p>
               <button type="button" class="button" data-action="connect" <?php echo esc_attr($field_att);?>><?php _e('Connect', 'integration-sirfiliate-woocommerce');?></button>
            </p>
         <?php endif; ?>

         <input type="hidden" name="<?php echo PREFIX;?>_fields[order_id]" value="<?php echo esc_attr($post->ID);?>">

      </div>
      <?php
   }



   /**
    * Saves the affiliate id on the created subscrition.
    *
    * @param \WC_Subscription $subscription
    * @return void
    */
   public static function set_subscription_affiliate($subscription){

      $aff_id = Service::get_affiliate_id($subscription);

      update_post_meta($subscription->get_id(), Util::prefix('affiliate_id'), $aff_id);

   }



   /**
    * Registers a transaction if any affiliate was found at complete order.
    *
    * @param int $order_id
    * @return void
    */
   public static function add_affiliate_transaction($order_id){

      $order        = wc_get_order($order_id);
      $aff_id       = Service::get_affiliate_id($order);
      $order_amount = Option::get('min_order_amount', 10);

      if( ! empty($aff_id) && Service::get_order_total($order) >= $order_amount ){
         Service::register_order_transaction($aff_id, $order);
      }
   }



   /**
    * Removes if there are any created transaction.
    *
    * @param int $order_id
    * @return void
    */
   public static function remove_affiliate_transaction($order_id){

      $trx_id = get_post_meta($order_id, Util::prefix('transaction_id'), true);

      if( ! empty($trx_id) ){
         $api      = new API();
         $response = $api->delete_transaction($trx_id);

         if(isset($response->id)){
            delete_post_meta($order_id, Util::prefix('transaction_id'));
         }
      }
   }

}