<?php
/**
 * Interface Hook Assets
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


interface Interface_Hook_Assets{

   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init();


   /**
    * Enqueues public CSS/JS files.
    *
    * @return void
    */
   public static function public_assets();



   /**
    * Enqueues admin CSS/JS files.
    *
    * @return void
    */
   public static function admin_assets();
}