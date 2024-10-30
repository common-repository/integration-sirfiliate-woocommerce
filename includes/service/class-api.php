<?php
/**
 * API
 *
 * @author Team WSA
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class API{


   /**
    * The API key required to authorize the requests.
    *
    * @var string
    */
   protected $key;


   /**
    * Whether or not to use sandbox.
    *
    * @var bool
    */
   protected $sandbox = false;



   /**
    * Construct of this class.
    *
    * @param string $key
    */
   public function __construct(string $key = ''){

      $this->key     = empty($key) ? Option::get('api_key') : $key;
      $this->sandbox = Util::string_to_bool(Option::get('api_sandbox', 'yes'));
   }



   /**
    * List of request headers
    *
    * @return array
    */
   public function headers(){

      $items = [
         'Accept' => 'application/json',
         'Authorization' => 'Bearer '. $this->key
      ];

      if($this->sandbox){
         $items['x-woosa-test-mode'] = 1;
      }

      return $items;
   }



   /**
    * API URL
    *
    * @param string $endpoint
    * @return string
    */
   public function endpoint(string $endpoint){

      if( defined('\WOOSA_TEST') && \WOOSA_TEST ) return 'https://midlayer-dev.woosa.nl/woocommerce/sir-filiate/'.ltrim($endpoint, '/');

      if( defined('\WOOSA_STA') && \WOOSA_STA ) return 'https://midlayer-sta.woosa.nl/woocommerce/sir-filiate/'.ltrim($endpoint, '/');

      return 'https://midlayer.woosa.nl/woocommerce/sir-filiate/'.ltrim($endpoint, '/');

   }



   /**
    * Checks whether or not the plugin is authorized.
    *
    * @return boolean
    */
   public function is_authorized(){

      $ma = new Module_Authorization;

      return $ma->is_authorized() ? true : false;
   }



   /**
    * Checks whether or not the key is valid.
    *
    * @return boolean
    */
   public function is_valid_key(){

      $valid    = false;
      $response = Request::GET([
         'headers' => $this->headers(),
         'cache'   => false,
      ])->send($this->endpoint('affiliates'));

      if($response->status == 200){
         $valid = true;
      }

      return $valid;
   }



   /**
    * Adds a transaction.
    *
    * @param array $payload
    * @return false|object
    */
   public function add_transaction(array $payload){

      $response = Request::POST([
         'headers'    => $this->headers(),
         'authorized' => $this->is_authorized(),
         'body'       => [
            'affiliate'   => $payload['affiliate'],
            'value'       => $payload['value'] * 100, //in cents,
            'description' => Util::array($payload)->get('description', sprintf('Created by %s', parse_url(home_url(), PHP_URL_HOST))),
            'metadata'    => Util::array($payload)->get('metadata', []),
         ],
      ])->send($this->endpoint('transactions'));

      return $response->status == 201 ? $response->body : false;

   }



   /**
    * Deletes a transaction.
    *
    * @param string $id
    * @return false|object
    */
   public function delete_transaction($id){

      $response = Request::DELETE([
         'headers'    => $this->headers(),
         'authorized' => $this->is_authorized(),
      ])->send($this->endpoint("transactions/{$id}"));

      return $response->status == 200 ? $response->body : false;

   }



   /**
    * Retrieves affiliate list.
    *
    * @return array
    */
   public function get_affiliates(){

      $items = [];
      $response = Request::GET([
         'headers'    => $this->headers(),
         'authorized' => $this->is_authorized(),
         'cache'      => false,
      ])->send($this->endpoint('affiliates'));

      if($response->status == 200){
         $items = $response->body;
      }

      return $items;
   }


}