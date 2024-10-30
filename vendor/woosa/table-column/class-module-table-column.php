<?php
/**
 * Module Table Column
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Table_Column{


   /**
    * Table head of columns
    *
    * @param array $items
    * @return array
    */
   public static function table_head_columns($items) {

      $new_columns = array();

      foreach ( $items as $name => $label ) {

         $new_columns[ $name ] = $label;

         foreach(self::get_columns() as $key => $col){

            if( $name === $col['after_column'] || isset($new_columns[$col['after_column']]) ) {
               $new_columns[$key] = $col['label'];
            }
         }
      }

      return $new_columns;

   }



   /**
    * Table content of columns
    *
    * @param string $column
    * @param int $object_id
    * @return string
    */
   public static function table_content_columns($column, $object_id){

      foreach(self::get_columns() as $key => $col){

         if( $column === $key ) {
            call_user_func_array($col['callback'], [$object_id, $column]);
         }
      }

   }



   /**
    * The list of post types where to add the column.
    *
    * @return array
    */
   public static function get_post_types(){

      $result = [];

      foreach(self::get_columns() as $column){
         $result = array_merge($result, self::get_column_post_types($column));
      }

      return array_unique($result);
   }



   /**
    * List of available columns.
    *
    * @return array
    */
   protected static function get_columns(){

      global $post_type;

      $list = apply_filters(PREFIX . '\table_column\columns', []);

      foreach($list as $key => $item){
         if( ! in_array($post_type, Util::array($item)->get('post_type', [])) ){
            unset($list[$key]);
         }
      }

      return $list;
   }



   /**
    * Retrieves post types of an column.
    *
    * @param array $column
    * @return array
    */
   protected static function get_column_post_types($column){
      return array_filter((array) Util::array($column)->get('post_type'));
   }

}