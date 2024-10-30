<?php
/**
 *  Module Validation
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Validation{


   /**
    * The value to be check
    *
    * @var mixed
    */
   public $value;


   /**
    * Condition parameters.
    *
    * @var array
    */
   public $params = [];

   /**
    * The messages of the validation children's
    *
    * @var array
    */
   protected $children_messages = [];

   /**
    * Construct of the class
    *
    * @param mixed $value - the value to apply the validation on
    * @param array $params
    */
   public function __construct($value = '', $params = []){

      $this->value    = $value;
      $this->params   = $params;
      $this->required = Util::array($this->params)->get('required', true);

   }



   /**
    * Checks whether or not the value is empty.
    *
    * @return boolean
    */
   public function is_empty(){

      if( $this->required && '' === $this->value){
         return true;
      }

      return false;
   }



   /**
    * Checks whether or not it has a valid length value.
    *
    * @return boolean
    */
   public function is_valid_length(){

      $valid      = [];
      $conditions = array_filter((array) Util::array($this->params)->get('length'));

      foreach($conditions as $condition){

         if(isset($condition['compare']) && isset($condition['operator'])){

            $compare  = $condition['compare'];
            $operator = $condition['operator'];
            $valid[]  = $this->compare($operator, strlen($this->value), $compare);
         }
      }

      return count(array_filter($valid)) == count($valid) ? true : false;
   }



   /**
    * Checks whether or not there is a valid value.
    *
    * @return boolean
    */
   public function is_valid_value(){

      $valid      = [];
      $conditions = array_filter((array) Util::array($this->params)->get('value'));

      foreach($conditions as $condition){

         if(isset($condition['compare']) && isset($condition['operator'])){

            $compare  = $condition['compare'];
            $operator = $condition['operator'];

            if(is_array($compare)){

               $type      = Util::array($compare)->get('type');
               $key       = Util::array($compare)->get('key');
               $npkey     = Util::unprefix($key);
               $object_id = Util::array($compare)->get('object_id');

               if('meta' === $type){

                  $meta = new Module_Meta($object_id);

                  if(isset($_POST['product-type']) && isset($_POST[PREFIX . '_fields'])){

                     $product_type = $_POST['product-type'];
                     $fields       = $_POST[PREFIX . '_fields'];

                     if( 'simple' === $product_type && isset($fields[$product_type][$npkey]) ){

                        $valid[] = $this->compare($operator, $this->value, $fields[$product_type][$npkey]);

                     }elseif( 'variable' === $product_type ){

                        if($meta->get_product()->is_type('variable') && isset($fields[$product_type]['parent'][$npkey])){
                           $valid[] = $this->compare($operator, $this->value, $fields[$product_type]['parent'][$npkey]);
                        }

                        if($meta->get_product()->is_type('variation') && isset($fields[$product_type]['variations'])){

                           foreach($fields[$product_type]['variations'] as $var_id => $var){

                              if( isset($var[$npkey]) && $object_id == $var_id  ){
                                 $valid[] = $this->compare($operator, $this->value, $var[$npkey]);
                                 break;
                              }
                           }

                        }
                     }

                  }else{

                     //use WP function to get the meta value to avoid infinite loops
                     $get_meta = "get_{$meta->meta_type}_meta";
                     $valid[] = $this->compare($operator, $this->value, $get_meta($object_id, $key, true));

                  }

               }elseif('option' === $type){

                  $v_to_comp = Util::array($_POST)->get($key, Option::get($key, false, false));
                  $valid[]   = $this->compare($operator, $this->value, $v_to_comp);

               }

            }else{

               $valid[] = $this->compare($operator, $this->value, $compare);
            }
         }
      }

      if ($this->has_children()) {
         if('OR' === Util::array($this->params)->get('children/relation')) {
            $children_validation = Util::array($this->params)->get("children/list/{$this->value}");
            if (!empty($children_validation)) {
               $object_id = Util::array($this->params)->get("children/object_id");
               $child_key = Util::array($children_validation)->get("key");
               $meta      = new Module_Meta($object_id);
               $npkey     = Util::unprefix($child_key);
               if(isset($_POST['product-type']) && isset($_POST[PREFIX . '_fields'])){
                  $product_type = $_POST['product-type'];
                  $fields       = Util::array($_POST)->get(PREFIX . '_fields/'.$product_type);
                  if( 'simple' === $product_type && isset($fields[$npkey]) ){
                     $valid[] = $this->compare('!=', '', $fields[$npkey]);
                  }elseif( 'variable' === $product_type ){
                     if($meta->get_product()->is_type('variable') && isset($fields['parent'][$npkey])){
                        $valid[] = $this->compare('!=', '', $fields['parent'][$npkey]);
                     }
                     if($meta->get_product()->is_type('variation') && isset($fields['variations'])){
                        foreach($fields['variations'] as $var_id => $var){
                           if( isset($var[$npkey]) && $object_id == $var_id  ){
                              $valid[] = $this->compare('!=', '', $var[$npkey]);
                              break;
                           }
                        }
                     }
                  }
               }
               $this->children_messages[$child_key] = Util::array($children_validation)->get("message");
            }
         }
      }

      return count(array_filter($valid)) == count($valid) ? true : false;
   }

   /**
    * Is has a children
    *
    * @return bool
    */
   public function has_children() {
      return !empty(Util::array($this->params)->get('children'));
   }

   /**
    * Get the children's messages
    *
    * @return array
    */
   public function get_children_messages() {
      return $this->children_messages;
   }

   /**
    * Get the childrens
    *
    * @return array
    */
   public function get_children() {
      return Util::array($this->params)->get("children/list", []);
   }

   /**
    * Checks whether or not the `disabled` attribute should be applied.
    *
    * @return boolean
    */
   public function is_disabled_field(){

      $output = false;
      $disable = Util::array($this->params)->get('disable');
      $conditions = [
         'if_authorized' => apply_filters(PREFIX . '\validation\disabled_field', true)
      ];

      if(is_array($disable)){
         foreach($disable as $condition){
            if(isset($conditions[$condition]) && $conditions[$condition]){
               $output = true;
            }
         }
      }else if($disable == true){
         $output = true;
      }

      return $output;
   }



   /**
    * Whether or not it passed the validation.
    *
    * @return boolean
    */
   public function is_valid(){

      $valid = true;

      if(
         $this->required &&
         (
            $this->is_empty() ||
            ! $this->is_valid_length() ||
            ! $this->is_valid_value()
         )
      ){

         $valid = false;

      }elseif(
         ! $this->required &&
         '' !== $this->value &&
         (
            ! $this->is_valid_length() ||
            ! $this->is_valid_value()
         )
      ){

         $valid = false;

      }

      return $valid;
   }



   /**
    * Compares a given value with a give compare value based on the given operator.
    *
    * @param string $operator
    * @param string $value
    * @param string $compare
    * @return boolean
    */
   protected function compare($operator, $value, $compare){

      $valid = true;

      switch($operator){
         case "=":
            $valid = $value == $compare;
            break;

         case "!=":
            $valid = $value != $compare;
            break;

         case ">=":
            $valid = $value >= $compare;
            break;

         case "<=":
            $valid = $value <= $compare;
            break;

         case ">":
            $valid = $value > $compare;
            break;

         case "<":
            $valid = $value < $compare;
            break;
      }

      return $valid;
   }
}