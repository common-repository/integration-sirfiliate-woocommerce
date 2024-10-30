<?php
/**
 * Module Table Column Hook
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Table_Column_Hook implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_action('wp', [__CLASS__, 'process_column_list']);

   }



   /**
    * Defines the hooks for the column list.
    *
    * @return void
    */
   public static function process_column_list(){

      foreach(Module_Table_Column::get_post_types() as $post_type){

         $initiate = apply_filters(PREFIX . '\table_column\initiate', true, $post_type);

         if($initiate){
            add_filter('manage_edit-'.$post_type.'_columns', [Module_Table_Column::class, 'table_head_columns']);
            add_action('manage_'.$post_type.'_posts_custom_column', [Module_Table_Column::class, 'table_content_columns'], 10, 2);
         }
      }
   }
}