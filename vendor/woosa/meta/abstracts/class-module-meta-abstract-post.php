<?php
/**
 * Module Meta Abstract Post
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


abstract class Module_Meta_Abstract_Post{


   /**
    * Object id
    *
    * @var int
    */
   public $object_id = 0;


   /**
    * Instance of WP_Post.
    *
    * @var null|\WP_Post
    */
   public $post = null;


   /**
    * Instance of WC_Product.
    *
    * @var null|\WC_Product
    */
   public $product = null;


   /**
    * Instance of WC_Order.
    *
    * @var null|\WC_Order
    */
   public $order = null;



   /**
    * Construct of the class.
    *
    * @param integer $object_id
    * @param string $meta_type
    */
   public function __construct($object_id = 0){

      $this->object_id = $object_id;
   }



   /**
    * Retrieves WP_Post instance.
    *
    * @return \WP_Post
    */
   public function get_post(){

      if(is_null($this->post)){
         $this->post = get_post($this->object_id);
      }

      return $this->post;

   }



   /**
    * Retrieves the post type.
    *
    * @return void|string
    */
   public function get_post_type(){

      if($this->get_post() instanceof \WP_Post){
         return $this->get_post()->post_type;
      }
   }



   /**
    * Checks whether or not the post is published.
    *
    * @param string $key
    * @param boolean $prefix
    * @return boolean
    */
   public function is_post_published(){

      if($this->get_post() instanceof \WP_Post){
         return 'publish' === $this->get_post()->post_status ? true : false;
      }

      return false;
   }



   /**
    * Checks whether or not the post has the given type(s).
    *
    * @param string|array $type
    * @return boolean
    */
   public function is_post_type($type){

      $type = array_filter((array) $type);

      return in_array($this->get_post_type(), $type) ? true : false;
   }




   /*
   |--------------------------------------------------------------------------
   | WC PRODUCT
   |--------------------------------------------------------------------------
   */


   /**
    * Retrieves WC_Product instance.
    *
    * @return \WC_Product
    */
   public function get_product(){

      global $product;

      if(is_null($this->product)){

         if($product instanceof \WC_Product){
            $this->product = $product;
         }elseif(function_exists('\\wc_get_product')){
            $this->product = wc_get_product($this->object_id);
         }
      }

      return $this->product;

   }



   /**
    * Checks whether or not the product has the given type(s).
    *
    * @param string|array $type
    * @return boolean
    */
   public function is_product_type($type){

      if($this->get_product() instanceof \WC_Product){
         return $this->get_product()->is_type($type) ? true : false;
      }

      return false;
   }




   /*
   |--------------------------------------------------------------------------
   | WC ORDER
   |--------------------------------------------------------------------------
   */


   /**
    * Retrieves WC_Order instance.
    *
    * @return \WC_Order
    */
   public function get_order(){

      global $order;

      if(is_null($this->order)){

         if($order instanceof \WC_Order){
            $this->order = $order;
         }elseif(function_exists('\\wc_get_order')){
            $this->order = wc_get_order($this->object_id);
         }
      }

      return $this->order;

   }


}