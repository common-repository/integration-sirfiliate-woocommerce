<?php
/**
 * Module Action Bulker Hook
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Action_Bulker_Hook implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_action('init', [__CLASS__, 'process_action_list']);
   }



   /**
    * Defines the hooks for the action list.
    *
    * @return void
    */
   public static function process_action_list(){

      foreach(Module_Action_Bulker::get_post_types() as $post_type){

         $initiate = apply_filters(PREFIX . '\action_bulker\initiate', true, $post_type);

         if($initiate){
            add_filter('bulk_actions-edit-'.$post_type, [Module_Action_Bulker::class, 'add_bulk_actions']);
            add_action('handle_bulk_actions-edit-'.$post_type, [Module_Action_Bulker::class, 'handle_bulk_actions'], 10, 3);
         }

      }
   }
}