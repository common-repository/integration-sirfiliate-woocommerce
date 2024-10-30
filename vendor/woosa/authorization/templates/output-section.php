<?php
/**
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


?>

<tr class="<?php echo PREFIX;?>-style">
   <td class="p-0">
      <div class="bg-white w-800 p-10">
         <p><?php printf(__('Status: %s', 'integration-sirfiliate-woocommerce'), $status);?></p>

         <?php do_action(PREFIX . '\authorization\output_section\fields', $authorization);?>

         <div class="pt-15">
            <button type="button" class="button button-primary" <?php echo $button['data-attr'];?>><?php echo $button['label'];?></button>
         </div>
      </div>
   </td>
</tr>