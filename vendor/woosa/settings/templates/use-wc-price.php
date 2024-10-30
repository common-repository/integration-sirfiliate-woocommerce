<?php
/**
 * @author Woosa Team
 */


namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


?>

<tr>
   <th><label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wc_help_tip($value['desc_tip']); ?></label></th>
   <td class="forminp">
      <label>
         <input type="hidden" name="<?php echo esc_attr( $value['id'] );?>" value="no">
         <input type="checkbox" id="<?php echo esc_attr( $value['id'] ); ?>" name="<?php echo esc_attr( $value['id'] );?>" <?php checked($use_wc_price, 'yes');?> data-<?php echo PREFIX;?>-has-extra-field="<?php echo esc_attr( $value['id'] );?>" value="yes"> <?php _e('Yes', 'integration-sirfiliate-woocommerce');?>
      </label>
      <p data-<?php echo PREFIX;?>-extra-field-<?php echo esc_attr( $value['id'] );?>="yes" style="<?php echo esc_attr( $display_field );?>">
         <label style="font-style:italic; font-size:12px;"><?php _e('Adjust the price (e.g. "10" for fixed amount or "10%" for percentage amount)', 'integration-sirfiliate-woocommerce');?></label><br/>
         <input type="text" name="<?php echo Util::prefix('fields[price_addition]');?>" value="<?php echo esc_attr( $price_addition );?>" placeholder="e.g. 10% or 10.00">
      </p>
   </td>
</tr>