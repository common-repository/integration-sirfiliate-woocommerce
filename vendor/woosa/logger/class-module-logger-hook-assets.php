<?php
/**
 * Module Logger Hook Assets
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Logger_Hook_Assets{


   /**
    * Initiates.
    *
    * @return void
    */
   public static function init(){

      add_action('admin_enqueue_scripts', [__CLASS__ , 'enqueue_assets']);

   }




   /*
   |--------------------------------------------------------------------------
   | MISCELLANEOUS
   |--------------------------------------------------------------------------
   */


   /**
    * Enqueues CSS/JS files.
    *
    * @return void
    */
   public static function enqueue_assets(){

      add_thickbox();

      Util::enqueue_scripts([
         [
            'name' => 'logger',
            'css' => [
               'path' => untrailingslashit(plugin_dir_url(__FILE__)) . '/assets/css/',
            ],
            'js' => [
               'path' => untrailingslashit(plugin_dir_url(__FILE__)) . '/assets/js/',
               'dependency' => ['jquery'],
               'localize' => true,
            ],
         ],
      ]);

   }

}