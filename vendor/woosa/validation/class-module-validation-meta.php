<?php
/**
 *  Module Validation Meta
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Validation_Meta{


   /**
    * Meta key.
    *
    * @var string
    */
   public $key = '';


   /**
    * Meta value.
    *
    * @var string
    */
   public $value = null;



   /**
    * Construct of the class
    *
    * @param int|Module_Meta $meta
    */
   public function __construct($meta){

      if( ! $meta instanceof Module_Meta ){
         $meta = new Module_Meta($meta);
      }

      $this->meta     = $meta;
      $this->criteria = $this->criteria($this->meta);
   }



   /**
    * Sets thea key.
    *
    * @param string $value
    * @return void
    */
   public function set_key($value){
      $this->key = $value;
   }



   /**
    * Sets the value.
    *
    * @param mixed $value
    * @return void
    */
   public function set_value($value){

      $value = empty($value) && !is_numeric($value) ? '' : $value;

      $this->value = $value;
   }



   /**
    * List of special keys.
    *
    * @return array
    */
   public static function special_keys(){

      return [
         '_stock',
         '_thumbnail_id',
         '_prod_description', //this is not a default WC meta
         '_regular_price',
      ];
   }



   /**
    * Validates the entire defined list of meta keys.
    *
    * @return bool
    */
   public function validate_by_object(){

      $valid = true;

      foreach($this->criteria as $key => $data){

         $mvm = new self($this->meta);
         $mvm->set_key($key);

         if( ! $mvm->validate() ){
            $valid = false;
         }

      }

      return $valid;
   }



   /**
    * Runs the validation if the meta key exists.
    *
    * @return bool
    */
   public function validate(){

      $valid = true;

      if(isset($this->criteria[$this->key])){

         $data      = $this->criteria[$this->key];
         $post_type = array_filter((array) Util::array($data)->get('post_type'));
         $meta      = new Module_Meta($this->meta->object_id); //we need a new instance of meta to avoid infinite loops

         //skip the validation if the post type does not match
         if( ! $this->meta->is_post_type($post_type) ){
            return true;
         }


         //check if it's a product
         if( ! empty( array_intersect(['product', 'product_variation'], $post_type) ) ){

            $product_type = isset($data['product_type']) ? $data['product_type'] : ['simple', 'variation'];

            //skip the validation if the product type is not in the defined list
            if( ! $this->meta->is_product_type($product_type) ){
               return true;
            }

         }


         //check if it's a special meta key
         if(in_array($this->key, self::special_keys())){

            $valid = $this->validate_special_meta($meta);

         }else{

            $value   = is_null($this->value) ? $this->meta->get($this->key) : $this->value;
            $message = Util::array($data)->get('message', "{$this->key} - invalid value provided!");
            $message = sprintf(__('%s %sProvided value: %s', 'integration-sirfiliate-woocommerce'), $message, '<em>', "{$value}</em>");
            $mv      = new Module_Validation( $value, $data );

            if( $mv->is_valid() ){
               if ($mv->has_children()) {
                  foreach ($mv->get_children() as $children) {
                     $meta->delete_error(Util::array($children)->get("key"));
                  }
               }
               $meta->delete_error($this->key);
            }else{
               $valid = false;
               if ($mv->has_children()) {
                  // main value not empty, but children are not valid
                  if (!$mv->is_empty()) {
                     $update = "update_{$meta->meta_type}_meta";
                     $updated = $update($this->meta->object_id, $this->key, $value);
                  }

                  foreach ($mv->get_children() as $children) {// remove previous errors
                     $meta->delete_error(Util::array($children)->get("key"));
                  }

                  foreach ($mv->get_children_messages() as $children_key => $children_message) {// set the errors
                     $meta->set_error($children_message, $children_key);
                  }

                  if (empty($mv->get_children_messages())) {// empty main value
                     $meta->set_error($message, $this->key);
                  } else {
                     $meta->delete_error($this->key);
                  }
               } else {
                  $meta->set_error($message, $this->key);
               }
            }

            $meta->save();

         }
      }

      return $valid;
   }



   /**
    * Validates special meta.
    *
    * @param Module_Meta $meta
    * @return bool
    */
   protected function validate_special_meta($meta){

      $valid   = true;
      $data    = $this->criteria[$this->key];
      $message = Util::array($data)->get('message', "{$this->key} - invalid value provided!");

      switch($this->key){

         case '_stock':

            if( ! $this->meta->is_published() ){

               $value   = is_null($this->value) ? $this->meta->get_product()->get_stock_quantity() : $this->value;
               $message = sprintf(__('%s %sProvided value: %s', 'integration-sirfiliate-woocommerce'), $message, '<em>', "{$value}</em>");

               if( $value <= Option::get('preserve_stock_offset', 0) ) {
                  $valid = false;
               }
            }

            break;

         case '_thumbnail_id':

            $value   = is_null($this->value) ? $this->meta->get_product()->get_image_id() : $this->value;
            $message = sprintf(__('%s %sProvided value: %s', 'integration-sirfiliate-woocommerce'), $message, '<em>', "{$value}</em>");

            if($this->meta->is_product_type('variation')){
               $p_product = wc_get_product($this->meta->get_product()->get_parent_id());
               $value     = empty($value) ? $p_product->get_image_id() : $value;
            }

            if( empty( $value ) ) {
               $valid = false;
            }

            break;

         case '_prod_description':

            $value   = is_null($this->value) ? $this->meta->get_product()->get_description() : $this->value;
            $message = sprintf(__('%s %sProvided value: %s', 'integration-sirfiliate-woocommerce'), $message, '<em>', "{$value}</em>");

            if( empty( $value ) ) {
               $valid = false;
            }

            break;

         case '_regular_price':

            $value   = is_null($this->value) ? $this->meta->get_product()->get_regular_price() : $this->value;
            $message = sprintf(__('%s %sProvided value: %s', 'integration-sirfiliate-woocommerce'), $message, '<em>', "{$value}</em>");

            if( empty( $value ) ) {
               $valid = false;
            }

            break;
      }

      if($valid === false){
         $meta->set_error($message, $this->key);
         $meta->save();
      }

      return $valid;

   }



   /**
    * List of validation criteria.
    *
    * @return array
    */
   protected function criteria($meta){

      $items = [];

      if(defined(__NAMESPACE__ . '\PRESERVE_STOCK_OFFSET') && PRESERVE_STOCK_OFFSET){
         $items['_stock'] = [
            'message' => __('Please make sure the product stock is enabled and greater than the "Preserve Stock Offset" value set in the plugin settings.', 'integration-sirfiliate-woocommerce'),
            'post_type' => ['product', 'product_variation'],
            'product_type' => [
               'simple',
               'variation',
            ],
         ];
      }

      return apply_filters(PREFIX . '\validation\meta\criteria', $items, $meta);
   }
}