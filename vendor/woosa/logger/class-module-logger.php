<?php
/**
 * Module Logger
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Logger{


   /**
    * The instance of this class.
    *
    * @var null|object
    */
   protected static $instance = null;


   /**
    * List of logs.
    *
    * @var array
    */
   protected $logs = [];


   /**
    * Available actions.
    *
    * @var array
    */
   protected $actions = ['view_detail', 'toggle_visibility', 'remove'];



   /**
    * Returns an instance of this class.
    *
    * @return object A single instance of this class.
    */
    public static function instance() {

      // If the single instance hasn't been set, set it now.
      if ( null == static::$instance ) {
         static::$instance = new static;
      }

      return static::$instance;
   }



   /**
    * Construct of this class.
    *
    */
   public function __construct(){

      if(empty($this->logs)){
         $this->logs = Option::get('logs', []);
      }

   }




   /*
   |--------------------------------------------------------------------------
   | GETTERS
   |--------------------------------------------------------------------------
   */


   /**
    * Retrieves the list of saved logs.
    *
    * @return array
    */
   public function get_logs(){

      $logs = $this->logs;

      uasort($logs, function( $a, $b ) {
         return $b["date"] <=> $a["date"];
      });

      return $logs;
   }



   /**
    * Retrieves a log by its code.
    *
    * @param string $code
    * @return array|null
    */
   public function get_log($code){

      $logs = $this->get_logs();
      $code = $this->sanitize_key($code);

      if(isset($logs[$code])){

         $actions = [];

         //get the actions
         foreach($this->actions as $key){
            $actions[$key] = Option::get("log_{$code}_{$key}");
         }

         $logs[$code]['actions'] = array_filter($actions);

         return $logs[$code];
      }

   }



   /**
    * The list of options which are connected with the logger.
    *
    * @return array
    * [
    *    'my_option_name' => [
    *       'type'      => '', //the type of log
    *       'code'      => '', //a unique code for identification
    *       'show_only' => [], //set the log only if the option value matches the ones in the array
    *       'actions'   => [], //see `set_log()` for actions
    *    ]
    * ]
    */
   public function get_connected_options(){
      return apply_filters(PREFIX . '\logger\get_connected_options', []);
   }




   /*
   |--------------------------------------------------------------------------
   | SETTERS
   |--------------------------------------------------------------------------
   */


   /**
    * Sets the log.
    *
    * @param string $type
    * @param string $code
    * @param array $actions
    * [
    *    'view_detail' => [
    *       'data' => [],
    *       'callback' => []
    *    ],
    *    'toggle_visibility' => [
    *       'data' => [],
    *       'callback' => []
    *    ],
    *    'remove' => [
    *       'data' => [],
    *       'callback' => []
    *    ],
    * ]
    * @param string $path
    * @param string $line
    * @return void
    */
   protected function set_log($type, $code, $actions = [], $path = '', $line = ''){

      $logs = $this->get_logs();
      $location = $path;

      $code = $this->sanitize_key($code);

      if( ! empty($type) && ! empty($code) ){

         if( ! empty($path) && ! empty($line)  ){
            $location = $path.':'.$line;
         }

         //save the actions
         foreach($actions as $key => $value){
            if(in_array($key, $this->actions)){
               Option::set("log_{$code}_{$key}", $value);
            }
         }

         $logs[$code] = [
            'type' => $type,
            'code' => $code,
            'read' => false,
            'path' => $location,
            'date' => time(),
         ];

         Option::set('logs', $logs);

         $this->logs = $logs;
      }
   }



   /**
    * Sets error log.
    *
    * @param string $code
    * @param array $actions
    * @param string $path
    * @param string $line
    * @return void
    */
   public function set_error($code, $actions = [], $path = '', $line = ''){
      $this->set_log('error', $code, $actions, $path, $line, $line);
   }



   /**
    * Sets info log.
    *
    * @param string $code
    * @param array $actions
    * @param string $path
    * @param string $line
    * @return void
    */
   public function set_info($code, $actions = [], $path = '', $line = ''){
      $this->set_log('info', $code, $actions, $path, $line);
   }



   /**
    * Sets warning log.
    *
    * @param string $code
    * @param array $actions
    * @param string $path
    * @param string $line
    * @return void
    */
   public function set_warning($code, $actions = [], $path = '', $line = ''){
      $this->set_log('warning', $code, $actions, $path, $line);
   }



   /**
    * Sets debug log.
    *
    * @param string $code
    * @param array $actions
    * @param string $path
    * @param string $line
    * @return void
    */
   public function set_debug($code, $actions = [], $path = '', $line = ''){
      $this->set_log('debug', $code, $actions, $path, $line);
   }



   /**
    * Sets a log from a given connected option.
    *
    * @param string $option_key
    * @param bool $update
    * @return void
    */
   public function set_log_from_option(string $option_key, $update = false){

      $options = $this->get_connected_options();

      if(array_key_exists($option_key, $options)){

         $option = $options[$option_key];
         $value = Util::maybe_decode_json( Option::get($option_key) );
         $log = $this->get_log($option['code']);
         $show_only = ( isset( $option['show_only'] ) ) ? $option['show_only'] : [];

         if( empty($value) ){

            if(isset($log['code'])){
               $this->remove_log($log['code']);
            }

         }else{

            if ( empty( $show_only ) || in_array( $value, $show_only ) ) {
               if( ($update && isset($log['code'])) || ! isset($log['code']) ){
                  $this->set_log($option['type'], $option['code'], $option['actions']);
               }
            }

         }
      }
   }



   /**
    * Updates an existing log.
    *
    * @param array $log
    * @return void
    */
   public function update_log($log){

      if( isset($log['code']) ){

         $logs = $this->get_logs();

         if( isset( $logs[$log['code']] ) ){

            //remove the actions
            if(isset($log['actions'])){
               unset($log['actions']);
            }

            $logs[$log['code']] = $log;

            Option::set('logs', $logs);

            $this->logs = $logs;
         }
      }
   }



   /**
    * Removes an exising log.
    *
    * @param string $code
    * @return void
    */
   public function remove_log($code){

      $code = $this->sanitize_key($code);
      $logs = $this->get_logs();

      if( isset( $logs[$code] ) ){

         $log = $logs[$code];
         $remove_action = Util::array($log)->get('actions/remove');

         if( isset($remove_action['callback']) ){

            $data = Util::array($remove_action)->get('data');

            call_user_func_array($remove_action['callback'], [$data]);
         }

         //remove the option if the codes match
         foreach($this->get_connected_options() as $option_key => $option){
            if($code === $option['code']){
               Option::delete($option_key);
            }
         }

         //remove the actions
         foreach($this->actions as $key){
            Option::delete("log_{$code}_{$key}");
         }

         unset($logs[$code]);

         Option::set('logs', $logs, false);

         $this->logs = $logs;
      }
   }




   /*
   |--------------------------------------------------------------------------
   | MISCELLANEOUS
   |--------------------------------------------------------------------------
   */


   /**
    * Replace all special charanters (few exceptions) with underscore.
    *
    * @param string $string
    * @return string
    */
   public function sanitize_key($string){

      if(Util::is_valid_url($string)){
         $string = parse_url($string);
         $string = Util::array($string)->get('path').Util::array($string)->get('query');
         $string = trim($string, '/');
      }
      $string = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $string);
      $string = trim($string, '_');

      return $string;
   }



}