<?php
/**
 * Interface Hook
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


interface Interface_Hook{

   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init();
}