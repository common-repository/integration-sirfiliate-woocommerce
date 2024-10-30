<?php
/**
 * Module Logger Hook AJAX
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Logger_Hook_AJAX{


   /**
    * Initiates.
    *
    * @return void
    */
   public static function init(){

      add_action('wp_ajax_' . PREFIX . '_log_action', [__CLASS__, 'process_log_action']);
      add_action('wp_ajax_' . PREFIX . '_log_notification', [__CLASS__, 'process_log_notification']);

   }



   /**
    * Processes the AJAX requested by a action button.
    *
    * @return string
    */
   public static function process_log_action(){

      //check to make sure the request is from same server
      if(!check_ajax_referer( 'wsa-nonce', 'security', false )){
         return;
      }

      $action = Util::array($_REQUEST)->get('log_action');

      if('view_detail' === $action){

         $code = Util::array($_REQUEST)->get('log_code');
         $log = Module_Logger::instance()->get_log($code);

         if(isset($log['code']) && array_key_exists('view_detail', $log['actions'])){

            $view_detail = $log['actions']['view_detail'];
            $data = array_key_exists('data', $view_detail) ? $view_detail['data'] : '';

            if(isset($view_detail['callback'])){

               call_user_func_array($view_detail['callback'], [$data]);

            }else{

               $output = is_string($data) ? $data : '<pre>'.print_r($data, 1).'</pre>';

               echo $output;
            }

         }else{

            echo '<h3>'.__('No detail available :(', 'integration-sirfiliate-woocommerce').'</h3>';
         }

         exit;
      }


      if('toggle_visibility' === $action){

         $codes = Util::array($_REQUEST)->get('log_codes');

         foreach($codes as $code){
            $log = Module_Logger::instance()->get_log($code);

            if(isset($log['code'])){
               $log['read'] = !$log['read'];
               Module_Logger::instance()->update_log($log);
            }
         }
      }

      if('remove' === $action){

         $codes = Util::array($_REQUEST)->get('log_codes');

         foreach($codes as $code){
            $log = Module_Logger::instance()->get_log($code);
            Module_Logger::instance()->remove_log($log['code']);
         }
      }

      wp_send_json_success([
         'action' => $action,
      ]);
   }



   /**
    * Processes the notification output.
    *
    * @return string
    */
   public static function process_log_notification(){

      //check to make sure the request is from same server
      if(!check_ajax_referer( 'wsa-nonce', 'security', false )){
         return;
      }

      $active = 0;
      $logs = Module_Logger::instance()->get_logs();

      foreach($logs as $log){
         if( in_array($log['type'], ['error', 'warning', 'info']) && ! $log['read'] ){
            $active = $active + 1;
         }
      }

      if($active == 0 || Module_Logger_Hook_Settings::$section === Util::array($_GET)->get('section')){
         return;
      }

      ob_start();
      ?>
      <div class="<?php echo PREFIX;?>-log-notification" style="display: none;">
         <div class="<?php echo PREFIX;?>-log-notification__name"><?php echo NAME;?></div>
         <p><?php _e('There are new logs detected!', 'integration-sirfiliate-woocommerce');?><br/><a href="<?php echo SETTINGS_URL;?>&section=logs"><?php _e('Click to view', 'integration-sirfiliate-woocommerce');?></a></p>
         <div class="<?php echo PREFIX;?>-log-notification__close">
            <span class="dashicons dashicons-dismiss"></span>
         </div>
      </div>
      <?php
      $html = apply_filters(PREFIX . '\logger\process_show_notification\html', ob_get_clean());

      wp_send_json_success([
         'html' => $html
      ]);
   }
}