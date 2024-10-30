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
      <select id="<?php echo esc_attr( $value['id'] ); ?>" name="<?php echo esc_attr( $value['id'] );?>" data-<?php echo PREFIX;?>-has-extra-field="<?php echo esc_attr( $value['id'] );?>">
         <option value="default" <?php selected($source, 'default');?>><?php _e('Default', 'integration-sirfiliate-woocommerce');?></option>
         <option value="custom_field" <?php selected($source, 'custom_field');?>><?php _e('Product custom field', 'integration-sirfiliate-woocommerce');?></option>
         <option value="sku" <?php selected($source, 'sku');?>><?php _e('Product SKU', 'integration-sirfiliate-woocommerce');?></option>
         <option value="attribute" <?php selected($source, 'attribute');?>><?php _e('Product attribute', 'integration-sirfiliate-woocommerce');?></option>
      </select>
      <p data-<?php echo PREFIX;?>-extra-field-<?php echo esc_attr( $value['id'] );?>="custom_field" style="<?php echo esc_attr( $display_field1 );?>">
         <label style="font-style:italic; font-size:12px;"><?php _e('Specify the custom field name', 'integration-sirfiliate-woocommerce');?></label><br/>
         <input type="text" name="<?php echo Util::prefix("fields[{$parent_field_id}__custom_field_name]");?>" value="<?php echo esc_attr( $custom_field_name );?>">
      </p>
      <div data-<?php echo PREFIX;?>-extra-field-<?php echo esc_attr( $value['id'] );?>="attribute" style="<?php echo esc_attr( $display_field2 );?>">
         <p>
            <label style="font-style:italic; font-size:12px;"><?php _e('Specify below the slug or name of the attribute.', 'integration-sirfiliate-woocommerce');?></label>
         </p>
         <p>
            <input type="text" name="<?php echo Util::prefix("fields[{$parent_field_id}__attribute_name]");?>" value="<?php echo esc_attr( $attribute_name );?>">
         </p>
         <p style="font-style:italic; font-size:12px; display: block;">&bull; <?php _e('only the first value of the attribute values will be used.', 'integration-sirfiliate-woocommerce');?></p>
         <p style="font-style:italic; font-size:12px; display: block; margin: 0;">&bull; <?php _e('it does not work for custom attributes and variable products.', 'integration-sirfiliate-woocommerce');?></p>
      </div>
   </td>
</tr>
