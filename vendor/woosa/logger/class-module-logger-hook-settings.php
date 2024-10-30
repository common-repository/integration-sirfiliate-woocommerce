<?php
/**
 * Module Logger Hook Settings
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Logger_Hook_Settings{


   /**
    * Name of the settings section.
    *
    * @var string
    */
   public static $section = 'logs';


   /**
    * Number of logs per page.
    *
    * @var integer
    */
   protected static $per_page = 10;


   /**
    * Max execution time required.
    *
    * @var integer
    */
   protected static $max_exec_time = 0;


   /**
    * Initiates.
    *
    * @return void
    */
   public static function init(){

      add_filter(PREFIX . '\settings\sections\\' . SETTINGS_TAB_ID, [__CLASS__, 'add_section'], 100);
      add_filter(PREFIX . '\settings\fields\\' . SETTINGS_TAB_ID . '\\'.self::$section, [__CLASS__, 'add_section_fields']);
      add_action('woocommerce_admin_field_' . PREFIX . '_logs', [__CLASS__, 'output_section']);
   }



   /**
    * Adds settings section.
    *
    * @param array $items
    * @return array
    */
   public static function add_section($items){

      $items[self::$section] = __('Logs', 'integration-sirfiliate-woocommerce');

      return $items;
   }



   /**
    * Adds settings section fields.
    *
    * @param array $items
    * @return array
    */
   public static function add_section_fields($items){

      $items = [
         [
            'name' => sprintf(__('Logs (%s)', 'integration-sirfiliate-woocommerce'), count(Module_Logger::instance()->get_logs())),
            'id'   => PREFIX . '_logs_title',
            'type' => 'title',
         ],
         [
            'id'   => PREFIX .'_logs',
            'type' => PREFIX .'_logs',
         ],
         [
            'id'   => PREFIX . '_logs_sectionend',
            'type' => 'sectionend',
         ],
      ];

      return apply_filters( PREFIX . '\logger\add_section_fields', $items );
   }



   /**
    * Renders settings section content.
    *
    * @param array $values
    * @return string
    */
   public static function output_section($values){

      $GLOBALS['hide_save_button'] = true;

      ob_start();
      ?>
      <tr class="<?php echo PREFIX;?>-style">
         <td class="p-0">
            <div class="<?php echo PREFIX;?>-logs">
               <?php self::render_logs();?>
            </div>
         </td>
      </tr>
      <?php
      $html = ob_get_clean();

      echo apply_filters( PREFIX . '\logger\output_section\html', $html, $values );
   }




   /*
   |--------------------------------------------------------------------------
   | GETTERS
   |--------------------------------------------------------------------------
   */


   /**
    * Gets the message for "No results".
    *
    * @return string
    */
    public static function get_no_results_text(){
      return __('There are no logs for the moment.', 'integration-sirfiliate-woocommerce');
   }



   /**
    * The list of messages.
    *
    * @return array
    */
   public static function get_messages(){
      return apply_filters(PREFIX .'\logger\messages', []);
   }



   /**
    * Gets a message by code.
    *
    * @param string $code
    * @return string
    */
   public static function get_message_by_code($code){

      $message = __('An unknown log has been created.', 'integration-sirfiliate-woocommerce');
      $code = Module_Logger::instance()->sanitize_key($code);

      if(isset(self::get_messages()[$code])){
         $message = self::get_messages()[$code];
      }

      return $message;
   }



   /**
    * Retrieves the log template.
    *
    * @param array $log
    * @return string
    */
   protected static function get_template($log){

      $active_class = $log['read'] ? '' : PREFIX . '-logs__item--active';

      ob_start();

      ?>
      <div class="<?php echo PREFIX;?>-logs__item <?php echo PREFIX;?>-logs__item--<?php echo $log['type'];?> <?php echo $active_class;?>" data-<?php echo PREFIX;?>-log-code="<?php echo $log['code'];?>">
         <div class="<?php echo PREFIX;?>-log-meta">
            <div class="<?php echo PREFIX;?>-log-meta__left">
                  <div class="<?php echo PREFIX;?>-log-type">
                     <label>
                        <input type="checkbox" data-<?php echo PREFIX;?>-log-checkbox name="<?php echo PREFIX;?>-log-selected[]" value="<?php echo $log['code'];?>">
                        <span><?php echo strtoupper($log['type']);?></span>
                     </label>
                  </div>
            </div>
            <div class="<?php echo PREFIX;?>-log-meta__right">
               <div class="<?php echo PREFIX;?>-log-date"><?php echo __('Date:', 'integration-sirfiliate-woocommerce') .' '. date('Y/m/d', $log['date']) . ' '.__('at', 'integration-sirfiliate-woocommerce').' ' . date('h:i:s a', $log['date']);?></div>
            </div>
         </div>
         <div class="<?php echo PREFIX;?>-log-message"><?php echo self::get_message_by_code($log['code']);?></div>

         <div class="<?php echo PREFIX;?>-log-meta">
            <?php if(isset($log['path']) && ! empty($log['path']) ):?>
               <div class="<?php echo PREFIX;?>-log-path"><?php printf(__('Thrown in: %s', 'integration-sirfiliate-woocommerce'), $log['path']);?></div>
            <?php endif;?>

            <div class="<?php echo PREFIX;?>-log-meta__left">
               <div class="<?php echo PREFIX;?>-log-action">
                  <button type="button" class="button button-small" data-<?php echo PREFIX;?>-log-action="view_detail"><?php _e('View Detail', 'integration-sirfiliate-woocommerce');?></button>
               </div>
            </div>

            <div class="<?php echo PREFIX;?>-log-meta__right">
               <div class="<?php echo PREFIX;?>-log-code"><?php echo __('Code:', 'integration-sirfiliate-woocommerce') . ' '. $log['code'];?></div>
            </div>
         </div>
      </div>
      <?php
      $html = ob_get_clean();

      return apply_filters( PREFIX . '\logger\get_template\html', $html, $log );
   }




   /*
   |--------------------------------------------------------------------------
   | RENDERS
   |
   | Functions whose HTML output is displayed directly
   |--------------------------------------------------------------------------
   */


   /**
    * Displays the logs.
    *
    * @return string
    */
   protected static function render_logs(){

      $logs = Module_Logger::instance()->get_logs();

      $paged = isset($_GET['log_page']) ? (int) $_GET['log_page'] : 0;
      $offset = $paged > 0 ? self::$per_page * ($paged - 1) : 0;

      $logs = array_slice($logs, $offset, self::$per_page);

      if( empty($logs) ){

         echo '<div>'.self::get_no_results_text(),'</div>';

      }else{

         self::render_actions();

         foreach($logs as $log){
            echo self::get_template($log);
         }

         self::render_pagination();

      }

   }



   /**
    * Displays the pagination.
    *
    * @return string
    */
   protected static function render_pagination(){

      $total = count(Module_Logger::instance()->get_logs());
      $pages = ceil($total / self::$per_page);

      if($pages > 1):
      ?>
      <ul class="pagination">
         <?php for($page = 1; $page <= $pages; $page++):
            $current = Util::array($_GET)->get('log_page')
            ?>
            <li>
               <?php if( (empty($current) && $page == 1) || $current == $page):?>
                  <span class="button button-small disabled"><?php echo $page;?></span>
               <?php else:?>
                  <a class="button button-small" href="<?php echo SETTINGS_URL .'&section=logs&log_page='.$page;?>"><?php echo $page;?></a>
               <?php endif;?>
            </li>
         <?php endfor;?>
      </ul>
      <?php
      endif;
   }



   /**
    * Displayes the action buttons.
    *
    * @return string
    */
   protected static function render_actions(){

      ?>
      <div class="<?php echo PREFIX;?>-log-actions">
         <div class="<?php echo PREFIX;?>-log-actions__left">
            <button type="button" class="button button-small" data-<?php echo PREFIX;?>-log-action="select_all"><?php _e('Select / unselect all', 'integration-sirfiliate-woocommerce');?></button>
         </div>
         <div class="<?php echo PREFIX;?>-log-actions__right">
            <button type="button" class="button button-small" disabled="disabled" data-<?php echo PREFIX;?>-log-action="toggle_visibility"><?php _e('Mark as read / unread', 'integration-sirfiliate-woocommerce');?></button>
            <button type="button" class="button button-small" disabled="disabled" data-<?php echo PREFIX;?>-log-action="remove"><?php _e('Remove', 'integration-sirfiliate-woocommerce');?></button>
         </div>
      </div>
      <?php
   }

}