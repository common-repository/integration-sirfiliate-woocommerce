<?php
/**
 *  Module Validation Hook
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Validation_Hook implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_filter(PREFIX . '\meta\validate_item', [__CLASS__, 'validate_meta'], 10, 5);

      add_filter(PREFIX . '\action_bulker\validate_item', [__CLASS__, 'validate_object_meta'], 10, 2);
      add_filter(PREFIX . '\product\action_bulker\validate_variation', [__CLASS__, 'validate_object_meta'], 10, 2);

      add_filter(PREFIX . '\settings\validate_item', [__CLASS__, 'validate_setting'], 10, 3);
      add_filter(PREFIX . '\settings\disable_field', [__CLASS__, 'disable_setting'], 10, 2);
   }



   /**
    * Runs the validation before saving the metadata.
    *
    * @param bool $valid
    * @param Module_Meta $meta
    * @param string $key
    * @param mixed $value
    * @param mixed $prev_value
    * @return bool
    */
   public static function validate_meta($valid, $meta, $key, $value, $prev_value){

      $mvm = new Module_Validation_Meta($meta);
      $mvm->set_key($key);
      $mvm->set_value($value);

      if( ! $mvm->validate() ){
         $valid = false;
      }

      return $valid;
   }



   /**
    * Runs validation on the given item for all defined metadata.
    *
    * @param bool $valid
    * @param int $item_id
    * @return bool
    */
   public static function validate_object_meta($valid, $item_id){

      $mvm = new Module_Validation_Meta($item_id);

      if( ! $mvm->validate_by_object() ){
         $valid = false;
      }

      return $valid;
   }



   /**
    * Runs validation before saving the setting option.
    *
    * @param bool $valid
    * @param string $value
    * @param array $data
    * @return bool
    */
   public static function validate_setting($valid, $value, $data){

      $mv = new Module_Validation( $value, $data );

      if( ! $mv->is_valid() ){
         $valid = false;
      }

      return $valid;
   }



   /**
    * Set the given setting as disabled.
    *
    * @param bool $disabled
    * @param array $field
    * @return bool
    */
   public static function disable_setting($disabled, $field){

      $mv = new Module_Validation('', $field);

      if( $mv->is_disabled_field() ){
         $disabled = true;
      }

      return $disabled;
   }

}