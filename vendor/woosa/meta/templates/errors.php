<?php
/**
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


?>
<div class="clear"><br/></div>
<div class="<?php echo PREFIX;?>-style">
   <div class="alertbox alertbox--red">
      <ul>
         <?php if ( is_array( $errors ) ) : ?>
            <?php foreach( $errors as $key => $value ) : ?>
               <li><?php echo $value; ?></li>
            <?php endforeach; ?>
         <?php else : ?>
            <li><?php echo $errors; ?></li>
         <?php endif; ?>
      </ul>
   </div>
</div>