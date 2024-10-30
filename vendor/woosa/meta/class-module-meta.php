<?php
/**
 * Module Meta
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Meta extends Module_Meta_Abstract{



   /*
   |--------------------------------------------------------------------------
   | STATUS
   |--------------------------------------------------------------------------
   */

   /**
    * Retrieves the status.
    *
    * @return string
    */
   public function get_status(){

      $default_keys = [
         'product'           => Module_Meta_Util::product_status_meta(),
         'product_variation' => Module_Meta_Util::product_status_meta(),
         'shop_order'        => Module_Meta_Util::order_status_meta(),
      ];

      $statuses = [];
      $value    = $this->is_post_type(['product', 'product_variation']) ? 'not_published' : 'not_available';
      $keys     = Util::array($default_keys)->get($this->get_post_type(), []);

      foreach($keys as $index => $key){
         if( ! empty($this->get($key)) ){
            $statuses[] = $this->get($key);
         }
      }

      if(in_array('in_progress', $statuses)){
         $value = 'in_progress';
      }else{

         if(empty($this->get_errors())){
            $value = Util::array($statuses)->get(0, $value);
         }else{
            $value = 'error';
         }

      }

      return $value;
   }



   /**
    * Sets the given status.
    *
    * @param string $value
    * @return self
    */
   public function set_status($value){

      $p_meta = Module_Meta_Util::product_status_meta();
      $o_meta = Module_Meta_Util::order_status_meta();
      $key    = $this->is_post_type('shop_order') ? Util::array( $o_meta )->get(PREFIX, $o_meta[0]) : Util::array( $p_meta )->get(PREFIX, $p_meta[0]);

      $this->set($key, $value);

      return $this;

   }



   /**
    * Deletes the status.
    *
    * @return self
    */
   public function delete_status(){

      $value = [];
      $keys  = $this->is_post_type('shop_order') ? Module_Meta_Util::order_status_meta() : Module_Meta_Util::product_status_meta();

      foreach($keys as $index => $key){
         $this->delete($key);
      }

      return $this;

   }



   /**
    * Displays the product status.
    *
    * @return string
    */
   public function display_product_status(){

      $statuses = Util::status_list();

      if ( $this->is_product_type( 'variable' ) ) {

         $status_list = [];
         $meta = new Module_Meta( $this->get_product()->get_id() );

         if('error' === $meta->get_status()){

            $status = $this->get_status();
            $color  = Util::array($statuses)->get($status.'/color');
            $title  = Util::array($statuses)->get($status.'/title');

            echo '<span style="color: '.$color.';">'.$title.'</span>';

         }else{

            foreach ( $this->get_product()->get_children() as $var_id ) {
               $_meta = new Module_Meta( $var_id );
               $status_list[] = $_meta->get_status();
            }

            $status_list = array_count_values(array_map(function($value) {
               return $value == "" ? 'not_published' : $value;
            }, $status_list));

            foreach( $status_list as $status => $status_total ) {

               $title = Util::array($statuses)->get("{$status}/title" );
               $color = Util::array($statuses)->get("{$status}/color" );
               $count = sprintf( _n( '%s variation', '%s variations', $status_total, 'integration-sirfiliate-woocommerce' ), $status_total );
               echo '<div class="' . PREFIX . '-product-status"><span style="color: ' . $color . '">' . $title . '</span><em>' . $count . '</em></div>';

            }
         }

      } else {

         $status = $this->get_status();
         $color  = Util::array($statuses)->get($status.'/color');
         $title  = Util::array($statuses)->get($status.'/title');

         echo '<span style="color: '.$color.';">'.$title.'</span>';
      }

   }



   /**
    * Displays the status (simple way).
    *
    * @return string
    */
   public function display_status(){

      $status   = $this->get_status();
      $statuses = Util::status_list();
      $color    = Util::array($statuses)->get("{$status}/color");
      $title    = Util::array($statuses)->get("{$status}/title");

      echo '<span style="color: '.$color.';">'.$title.'</span>';

   }



   /*
   |--------------------------------------------------------------------------
   | ERRORS
   |--------------------------------------------------------------------------
   */

   /**
    * Retrieves the errors
    *
    * @return array
    */
   public function get_errors(){

      $value = [];
      $keys  = $this->is_post_type('shop_order') ? Module_Meta_Util::order_error_meta() : Module_Meta_Util::product_error_meta();

      foreach($keys as $index => $key){
         $value = array_replace($value, array_filter((array) $this->get($key)));
      }

      return $value;
   }



   /**
    * Sets the given error message.
    *
    * @param string $value
    * @param string $index_key
    * @return self
    */
   public function set_error($value, $index_key = ''){

      $errors = $this->get_errors();

      if( ! in_array($value, $errors) ){

         $key = $this->is_post_type('shop_order') ? 'order_errors' : 'product_errors';

         if(empty($index_key)){
            $errors[] = $value;
         }else{
            $errors[$index_key] = $value;
         }

         $this->set($key, $errors);
      }

      return $this;

   }



   /**
    * Deletes an error by the given index key.
    *
    * @param string $index_key
    * @return self
    */
   public function delete_error($index_key){

      $errors = $this->get_errors();

      if(isset($errors[$index_key])){

         $key = $this->is_post_type('shop_order') ? 'order_errors' : 'product_errors';

         unset($errors[$index_key]);

         $this->set($key, $errors);
      }

      return $this;

   }



   /**
    * Deletes the errors.
    *
    * @return self
    */
   public function delete_errors(){

      $value = [];
      $keys  = $this->is_post_type('shop_order') ? Module_Meta_Util::order_error_meta() : Module_Meta_Util::product_error_meta();

      foreach($keys as $index => $key){
         $this->delete($key);
      }

      return $this;

   }



   /**
    * Displayes the errors.
    *
    * @return string
    */
   public function display_errors(){

      $errors = $this->get_errors();
      $output = '';

      if ( ! empty( $errors ) ) {

         $path      = dirname(__FILE__).'/templates/errors.php';
         $incl_path = DIR_PATH.'/includes/meta/templates/errors.php';

         if(file_exists($incl_path)){
            $path = $incl_path;
         }

         $output = Util::get_template($path, [
            'errors' => $errors,
         ]);
      }

      echo $output;
   }



   /*
   |--------------------------------------------------------------------------
   | CONDITIONALS
   |--------------------------------------------------------------------------
   */

   /**
    * Checks whether or not it's considered "published".
    *
    * @return boolean
    */
    public function is_published(){

      $value = true;
      $id    = '';
      $keys  = $this->is_post_type('shop_order') ? Module_Meta_Util::order_id_meta() : Module_Meta_Util::product_id_meta();

      foreach($keys as $index => $key){
         if( ! empty($this->get($key)) ){
            $id = $this->get($key);
            break;
         }
      }

      if( (empty($id) && 'published' !== $this->get_status()) || 'published' !== $this->get_status()){
         $value = false;
      }

      return $value;
   }




   /*
   |--------------------------------------------------------------------------
   | MISC
   |--------------------------------------------------------------------------
   */

   /**
    * Retrieves the service category connected with the assigned WooCommerce category.
    *
    * @return string
    */
   public function get_connected_category(){

      $value = '';
      $terms = get_the_terms($this->object_id, 'product_cat');

      if( ! is_array($terms) ){

         $product = wc_get_product($this->object_id);

         if($product->is_type('variation')){
            $terms = get_the_terms($product->get_parent_id(), 'product_cat');
         }
      }

      if(is_array($terms)){

         foreach($terms as $term){

            $term_meta = new self($term->term_id, 'term');
            $value     = $term_meta->get('category');

            if( ! empty($value) ){
               break;
            }
         }

         if(empty($value)){

            foreach($terms as $term){
               $term_ancs = get_ancestors($term->term_id, 'product_cat');

               foreach($term_ancs as $term_anc_id){
                  $term_meta = new self($term_anc_id, 'term');
                  $value     = $term_meta->get('category');

                  if( ! empty($value) ){
                     break;
                  }
               }
               if( ! empty($value) ){
                  break;
               }
            }

         }

      }

      return $value;
   }



   /**
    * Gets the value of International Article Number based on the global setting.
    *
    * @param string $source_option_key - this is the setting option key which defines what source to use
    * @param string $default_meta - this is the default meta key where to get the value from
    * @param string $source_custom_field_option_key - this is the custom meta key where to get the value from
    * @param string $source_attribute_option_key - this is the name/slug of an attribute where to get the value from
    * @return string
    */
   public function get_ian_value($source_option_key = 'ean_source', $default_meta = 'ean', $source_custom_field_option_key = 'ian_custom_field_name', $source_attribute_option_key = 'ian_attribute_name'){
      wc_deprecated_function( 'Module_Meta::get_ian_value', '1.0.1', 'Module_Meta::get_value_by_source' );
      $this->get_value_by_source($source_option_key, $default_meta, $source_custom_field_option_key, $source_attribute_option_key);
   }



   /**
    * Retrieves a value based on the source.
    *
    * @param string $source_option_key - this is the setting option key which defines what source to use
    * @param string $default_meta - this is the default meta key where to get the value from
    * @param string $source_custom_field_option_key - this is the custom meta key where to get the value from
    * @param string $source_attribute_option_key - this is the name/slug of an attribute where to get the value from
    * @return string
    */
   public function get_value_by_source($source_option_key, $default_meta, $source_custom_field_option_key, $source_attribute_option_key){

      $source  = Option::get($source_option_key, 'default');
      $value   = get_post_meta($this->object_id, Util::prefix($default_meta), true);
      $product = wc_get_product($this->object_id);

      if( ! $product instanceof \WC_Product ){
         return $value;
      }

      switch($source){

         case 'custom_field':

            $field_name = Option::get($source_custom_field_option_key);

            if( !empty($field_name) ){

               $value = get_post_meta($this->object_id, $field_name, true);

               if(is_array($value)){
                  $value = $value[0];
               }
            }

            break;


         case 'sku':

            $value = $product->get_sku();

            break;


         case 'attribute':

            if( $product->is_type('variable') || $product->is_type('variation') ){
               return $value;
            }

            $name  = sanitize_title( Option::get($source_attribute_option_key) );
            $attrs = $product->get_attributes();
            $data  = [];

            foreach($attrs as $item){

               if("pa_{$name}" === $item->get_name()){
                  $data = $item->get_data();
                  break;
               }
            }

            if(isset($data['id'])){

               if($data['id'] > 0){

                  $options = $data['options'];
                  $term_id = $options[0];
                  $term = get_term($term_id, "pa_{$name}");

                  if(is_wp_error( $term )){
                     Utility::wc_error_log($term, __FILE__, __LINE__);
                  }elseif(isset($term->name)){
                     $value = $term->name;
                  }else{
                     Utility::wc_error_log("Term no found for attribute taxonomy: pa_{$name}", __FILE__, __LINE__);
                  }

               }

            }

            break;

      }

      return $value;
   }

}
