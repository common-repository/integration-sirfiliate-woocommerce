<?php
/**
 * @author Team WSA
 */


namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


?>
<div class="pt-10 pb-10">
   <b><?php printf(__('Results(%s)', ''), count($results));?></b>
</div>

<table class=" widefat fixed striped">
   <thead>
      <tr>
         <td style="width: 10%;"><?php _e('ID', 'integration-sirfiliate-woocommerce');?></td>
         <td><?php _e('Status', 'integration-sirfiliate-woocommerce');?></td>
         <td><?php _e('Products', 'integration-sirfiliate-woocommerce');?></td>
      </tr>
   </thead>
   <tbody>
      <?php if( empty($results) ):?>

         <tr>
            <td colspan="3"><?php _e('No results found', 'integration-sirfiliate-woocommerce');?></td>
         </tr>

      <?php else:?>

         <?php foreach($results as $result):?>
            <tr>
               <td><?php echo esc_html($result['id']);?></td>
               <td>
                  <mark class="order-status subscription-status status-<?php echo esc_attr($result['status']);?>"><span><?php echo esc_html(ucfirst($result['status']));?></span></mark>
               </td>
               <td><?php echo esc_html(implode(', ', $result['products']));?></td>
            </tr>
         <?php endforeach;?>

      <?php endif;?>
   </tbody>
   <tfoot>
      <tr>
         <td style="width: 10%;"><?php _e('ID', 'integration-sirfiliate-woocommerce');?></td>
         <td><?php _e('Status', 'integration-sirfiliate-woocommerce');?></td>
         <td><?php _e('Products', 'integration-sirfiliate-woocommerce');?></td>
      </tr>
   </tfoot>
</table>