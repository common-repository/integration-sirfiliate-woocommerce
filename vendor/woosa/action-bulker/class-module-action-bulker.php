<?php
/**
 * Module Action Bulker
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Action_Bulker{


   /**
    * Adds new bulk actions.
    *
    * @param array $items
    * @return $items
    */
   public static function add_bulk_actions($items) {

      foreach(self::get_actions() as $action_id => $action){
         $items[$action_id] = $action['label'];
      }

      return $items;
   }



   /**
    * Handles the bulk actions.
    *
    * @param string $redirect_to
    * @param string $doaction
    * @param array $post_ids
    * @return string
    */
   public static function handle_bulk_actions($redirect_to, $doaction, $post_ids){

      self::perform($doaction, $post_ids);

      return $redirect_to;
   }



   /**
    * The list of post types where to add the bulk action.
    *
    * @return array
    */
   public static function get_post_types(){

      $result = [];

      foreach(self::get_actions() as $action_id => $action){
         $result = array_merge($result, self::get_action_post_types($action));
      }

      return array_unique($result);
   }



   /**
    * Retrieves the post types of an action.
    *
    * @param array $action
    * @return array
    */
   protected static function get_action_post_types($action){
      return array_filter((array) Util::array($action)->get('post_type'));
   }



   /**
    * List of available actions.
    *
    * [
    *    'my_action_id' => [
    *       'label'         => __('Action name', 'integration-sirfiliate-woocommerce'),
    *       'post_type'     => ['product'],
    *       'callback'      => [__CLASS__, 'action_callback'],
    *       'bulk_perform'  => true,
    *       'schedulable'   => true,
    *       'validate_item' => true,
    *    ]
    * ]
    *
    * @return array
    */
   protected static function get_actions(){
      return apply_filters(PREFIX . '\action_bulker\actions', []);
   }



   /**
    * Gets an action.
    *
    * @param string $action_id
    * @return false|array
    */
   protected static function get_action($action_id){

      $list   = self::get_actions();
      $output = false;

      if(isset($list[$action_id])){
         $output = array_merge(['id' => $action_id], $list[$action_id]);
      }

      return $output;
   }



   /**
    * Performs a given action.
    *
    * @param string $action_id
    * @param array $items
    * @return void
    */
   protected static function perform($action_id, $items){

      $action = self::get_action($action_id);
      $allow  = apply_filters(PREFIX . '\action_bulker\allow_perform', true, $action);
      $items  = array_filter((array) $items);

      if($allow && $action){

         $validate_item = Util::array($action)->get('validate_item', false);

         if($validate_item){

            foreach($items as $key => $item_id){

               $valid = apply_filters(PREFIX . '\action_bulker\validate_item', true, $item_id);

               if( $valid === false ) {
                  unset( $items[$key] );
               }
            }

         }

         $items = apply_filters(PREFIX . '\action_bulker\valid_items', $items, $action);

         if( empty($items) ){

            /**
             * Fires when the action is not performed due to no items available
             */
            do_action(PREFIX . '\action_bulker\no_items', $action, $items);

         }else{

            $items = array_values($items);//to reset the indexes

            if(Util::array($action)->get('bulk_perform') === false){

               foreach($items as $key => $item_id){

                  /**
                   * Fires before running the action
                   */
                  do_action(PREFIX . '\action_bulker\before_perform\item', $action, $item_id);

                  if($action['schedulable']){

                     /**
                      * Let 3rd-party to schedule the action
                      */
                     do_action(PREFIX . '\action_bulker\perform_schedulable\item', $action, $item_id);

                  }else{
                     call_user_func_array($action['callback'], [$item_id]);
                  }

               }

            }else{

               /**
                * Fires before running the action
                */
               do_action(PREFIX . '\action_bulker\before_perform', $action, $items);

               if($action['schedulable']){

                  /**
                   * Let 3rd-party to schedule the action
                   */
                  do_action(PREFIX . '\action_bulker\perform_schedulable', $action, $items);

               }else{
                  call_user_func_array($action['callback'], [$items]);
               }
            }
         }

      }
   }

}