<?php
/**
 * Module Authorization
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Authorization{


   /**
    * The environment (live, test, etc).
    * Based on this the authorization status and the actions connect/disconnect will be processed separately.
    *
    * @var string|null
    */
   protected $environment = null;


   /**
    * Whether or not is marked as authorized.
    *
    * @var bool|null
    */
   protected $is_authorized = null;



   /**
    * Sets the current environment.
    *
    * @param string $value
    * @return void
    */
   public function set_env(string $value){
      $this->environment = $value;
   }



   /**
    * Sets the authorized flag.
    *
    * @return void
    */
   public function set_as_authorized(){
      Option::set( $this->env_flag(), 'yes' );
   }



   /**
    * Remove authorized flag.
    *
    * @return void
    */
   public function set_as_unauthorized(){
      Option::delete( $this->env_flag() );
   }



   /**
    * Retrieves the current environment.
    *
    * @return string
    */
   public function get_env(){

      if(is_null($this->environment)){

         $test_mode = Util::string_to_bool( Option::get('test_mode', Option::get('testmode', 'no')) );

         if( $test_mode ) {
            $this->environment = 'test';
         }else{
            $this->environment = 'live';
         }

      }

      return $this->environment;
   }



   /**
    * Gets the access status.
    *
    * @return string
    */
   public function get_status(){

      $value = __('Unauthorized', 'integration-sirfiliate-woocommerce');

      if( $this->is_authorized() ){
         $value = __('Authorized', 'integration-sirfiliate-woocommerce');
      }

      return apply_filters(PREFIX . '\authorization\get_status', $value, $this);
   }



   /**
    * Checks whether or not it's marked as authorized.
    *
    * @var bool
    */
   public function is_authorized(){

      if(is_null($this->is_authorized)){
         $this->is_authorized = Util::string_to_bool( Option::get( $this->env_flag() ) );
      }

      return apply_filters(PREFIX . '\authorization\is_authorized', $this->is_authorized, $this);
   }



   /**
    * Grant access
    *
    * @return object
    */
   public function connect(){
      return $this->do_connection('connect');
   }



   /**
    * Revoke access.
    *
    * @return object
    */
   public function disconnect(){
      return $this->do_connection('disconnect');
   }



   /**
    * Runs connect or disconnect actions. Here other modules can hook up and run their logic.
    *
    * @param string $action
    * @return array - ['success' => true|false, 'message' => 'my message']
    */
   protected function do_connection(string $action){

      if( ! in_array($action, ['connect', 'disconnect']) ){
         return [
            'success' => false,
            'message' => 'Invalid action supplied!'
         ];
      }

      $output = ['success' => true];

      if( 'connect' === $action ) {

         $output = apply_filters(PREFIX . '\authorization\connect', $output, $this);

         if($output['success']){

            $this->set_as_authorized();

            do_action(PREFIX . '\authorization\access_granted', $this->environment);
         }

      }else{

         $output = apply_filters(PREFIX . '\authorization\disconnect', $output, $this);

         if($output['success']){

            $this->set_as_unauthorized();

            do_action(PREFIX . '\authorization\access_revoked', $this->environment);
         }
      }

      return $output;
   }



   /**
    * The authorized flag based on the environment.
    *
    * @return string
    */
   protected function env_flag(){
      return empty($this->get_env()) ? 'is_authorized' : "is_authorized_{$this->get_env()}";
   }



   /**
    * Displays the status and action button.
    *
    * @param string $environment
    * @return string
    */
   public static function render_status($environment = ''){

      $ma = new self();

      if( ! empty($environment) ){
         $ma->set_env($environment);
      }

      $data = json_encode([
         'action' => $ma->is_authorized() ? 'revoke' : 'authorize'
      ]);

      $color = $ma->is_authorized() ? 'green' : '#cc0000';
      $status = '<b>'.__('Status:', 'integration-sirfiliate-woocommerce').'</b> <span style="color: '.$color.';">'.$ma->get_status().'</span>';

      $btn_attr = "data-" . PREFIX . "-authorization='{$data}'";
      $btn_label = $ma->is_authorized() ? __( 'click to revoke', 'integration-sirfiliate-woocommerce' ) : __( 'click to authorize', 'integration-sirfiliate-woocommerce' );
      $btn = ' <button type="button" class="button button-link" '.$btn_attr.'>('.$btn_label.')</button>';

      $html = $status.$btn;

      return apply_filters(PREFIX . '\authorization\render_status', $html, $ma);
   }



   /**
    * Displays the section content.
    *
    * @param array $values
    * @param string $environment
    * @return string
    */
   public static function render($values = [], $environment = ''){

      $ma = new self();

      if( ! empty($environment) ){
         $ma->set_env($environment);
      }

      $abs_path  = \dirname(__FILE__);
      $incl_path = DIR_PATH.'/includes/authorization/templates/output-section.php';

      if(file_exists($incl_path)){
         $abs_path = DIR_PATH.'/includes/authorization';
      }

      $data = json_encode([
         'action' => $ma->is_authorized() ? 'revoke' : 'authorize'
      ]);

      $color     = $ma->is_authorized() ? 'green' : '#cc0000';
      $status    = '<span style="color: '.$color.';">'.$ma->get_status().'</span>';
      $btn_attr  = "data-" . PREFIX . "-authorization='{$data}'";
      $btn_label = $ma->is_authorized() ? __( 'Click to revoke', 'integration-sirfiliate-woocommerce' ) : __( 'Click to authorize', 'integration-sirfiliate-woocommerce' );

      echo Util::get_template('output-section.php', [
         'authorization' => $ma,
         'status' => $status,
         'button' => [
            'label' => $btn_label,
            'data-attr' => $btn_attr,
         ]
      ], $abs_path, '/templates');
   }

}