<?php
/**
 * @author Team WSA
 */


namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


?>

<tr class="<?php echo PREFIX;?>-style">
   <td class="p-0">
      <div class="bg-white w-1000 p-10 mb-15">

         <div data-<?php echo Util::prefix('search-filters', true);?>>

            <div class="pb-10">
               <p><b><?php _e('Affiliate', 'integration-sirfiliate-woocommerce');?></b></p>

               <select name="<?php echo PREFIX;?>_fields[affiliate]" data-<?php echo PREFIX;?>-select2="yes">
                  <option value=""><?php _e('Please select', 'integration-sirfiliate-woocommerce');?></option>
                  <?php foreach($affiliates as $item):?>
                     <option value="<?php echo esc_attr($item->id);?>" <?php selected($item->id, Util::array($_GET)->get(PREFIX . '_affiliate'));?>><?php echo esc_html($item->name);?> (<?php echo esc_html($item->email);?>)</option>
                  <?php endforeach;?>
               </select>
            </div>

            <div class="pb-10">
               <p><b><?php _e('Status', 'integration-sirfiliate-woocommerce');?></b></p>

               <select name="<?php echo PREFIX;?>_fields[status]">
                  <option value=""><?php _e('Please select', 'integration-sirfiliate-woocommerce');?></option>
                  <option value="wc-active" <?php selected('wc-active', Util::array($_GET)->get(PREFIX . '_status'));?>><?php _e('Active', 'integration-sirfiliate-woocommerce');?></option>
                  <option value="wc-processing" <?php selected('wc-processing', Util::array($_GET)->get(PREFIX . '_status'));?>><?php _e('Processing', 'integration-sirfiliate-woocommerce');?></option>
                  <option value="wc-completed" <?php selected('wc-completed', Util::array($_GET)->get(PREFIX . '_status'));?>><?php _e('Completed', 'integration-sirfiliate-woocommerce');?></option>
                  <option value="wc-on-hold" <?php selected('wc-on-hold', Util::array($_GET)->get(PREFIX . '_status'));?>><?php _e('On-hold', 'integration-sirfiliate-woocommerce');?></option>
                  <option value="wc-refunded" <?php selected('wc-refunded', Util::array($_GET)->get(PREFIX . '_status'));?>><?php _e('Refunded', 'integration-sirfiliate-woocommerce');?></option>
                  <option value="wc-pending" <?php selected('wc-pending', Util::array($_GET)->get(PREFIX . '_status'));?>><?php _e('Pending', 'integration-sirfiliate-woocommerce');?></option>
                  <option value="wc-pending-cancel" <?php selected('wc-pending-cancel', Util::array($_GET)->get(PREFIX . '_status'));?>><?php _e('Pending cancel', 'integration-sirfiliate-woocommerce');?></option>
                  <option value="wc-cancelled" <?php selected('wc-cancelled', Util::array($_GET)->get(PREFIX . '_status'));?>><?php _e('Cancelled', 'integration-sirfiliate-woocommerce');?></option>
                  <option value="wc-failed" <?php selected('wc-failed', Util::array($_GET)->get(PREFIX . '_status'));?>><?php _e('Failed', 'integration-sirfiliate-woocommerce');?></option>
                  <option value="wc-expired" <?php selected('wc-expired', Util::array($_GET)->get(PREFIX . '_status'));?>><?php _e('Expired', 'integration-sirfiliate-woocommerce');?></option>
               </select>
            </div>

            <div class="pb-10">
               <p><b><?php _e('Type', 'integration-sirfiliate-woocommerce');?></b></p>

               <select name="<?php echo PREFIX;?>_fields[type]">
                  <option value=""><?php _e('Please select', 'integration-sirfiliate-woocommerce');?></option>
                  <option value="shop_order" <?php selected('shop_order', Util::array($_GET)->get(PREFIX . '_type'));?>><?php _e('Order', 'integration-sirfiliate-woocommerce');?></option>
                  <option value="shop_subscription" <?php selected('shop_subscription', Util::array($_GET)->get(PREFIX . '_type'));?>><?php _e('Subscription', 'integration-sirfiliate-woocommerce');?></option>
               </select>
            </div>

            <div class="pb-15">
               <button class="button button-primary" type="button" data-<?php echo Util::prefix('view-search-results', true);?>><?php _e('View results', 'integration-sirfiliate-woocommerce');?></button>
            </div>

         </div>

         <div data-<?php echo Util::prefix('search-results', true);?>></div>

      </div>
   </td>
</tr>