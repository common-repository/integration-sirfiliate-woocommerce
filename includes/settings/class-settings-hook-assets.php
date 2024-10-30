<?php
/**
 * Settings Hook Assets
 *
 * @author Team WSA
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Settings_Hook_Assets implements Interface_Hook_Assets{


   /**
    * Initiates.
    *
    * @return void
    */
   public static function init(){

      add_action('admin_enqueue_scripts', [__CLASS__ , 'admin_assets']);

   }



   /**
    * Enqueues admin CSS/JS files.
    *
    * @return void
    */
   public static function public_assets(){}



   /**
    * Enqueues public CSS/JS files.
    *
    * @return void
    */
   public static function admin_assets(){

      Util::enqueue_scripts([
         [
            'name' => 'settings',
            'js' => [
               'path' => untrailingslashit(plugin_dir_url(__FILE__)) . '/assets/js/',
               'dependency' => ['jquery', PREFIX . '-module-core'],
            ],
         ],
      ]);

   }

}