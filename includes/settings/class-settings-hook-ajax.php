<?php
/**
 * Settings Hook AJAX
 *
 * @author Team WSA
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Settings_Hook_AJAX implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_action('wp_ajax_' . PREFIX . '_render_search_results', [__CLASS__, 'process_render_search_results']);

   }



   /**
    * Bulds the search URL.
    *
    * @return string
    */
   public static function process_render_search_results(){

      //check to make sure the request is from same server
      if(!check_ajax_referer( 'wsa-nonce', 'security', false )){
         return;
      }

      parse_str(Util::array($_POST)->get('fields'), $fields);

      $fields = Util::array($fields)->get(Util::prefix('fields'));

      $results   = [];
      $query     = [];
      $affiliate = Util::array($fields)->get('affiliate');
      $status    = Util::array($fields)->get('status', 'any');
      $type      = Util::array($fields)->get('type');

      if(in_array($type, ['shop_order', 'shop_subscription']) && ! empty($affiliate)){

         $query = new \WP_Query([
            'posts_per_page' => -1,
            'post_type' => $type,
            'post_status' => $status,
            'meta_query' => [
               [
                  'key' => Util::prefix('affiliate_id'),
                  'value' => $affiliate,
               ]
            ]
         ]);

         foreach($query->posts as $result){

            $products = [];
            $order    = wc_get_order($result->ID);

            foreach ( $order->get_items() as $item ) {
               $products[] = $item->get_name();
            }

            $results[] = [
               'id' => $order->get_id(),
               'status' => $order->get_status(),
               'products' => $products
            ];
         }
      }

      $html = Util::get_template('search-results.php', [
         'results' => $results
      ], dirname(__FILE__), '/templates');

      wp_send_json_success([
         'html' => $html,
      ]);
   }

}