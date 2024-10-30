<?php
/**
 * Util File
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Util_File{


   /**
    * Checks the status of a given remove URL
    *
    * @param string $url
    * @return int
    */
   public static function check_remote_status( $url ) {

      $ch = curl_init( $url );

      curl_setopt( $ch, CURLOPT_NOBODY, true );
      curl_exec( $ch );
      $code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
      curl_close( $ch );

      return $code;

   }



   /**
    * Downloads a remote file locally.
    *
    * @param string $url - remote url
    * @param string $local_file - local file path
    * @return array status
    */
   public static function remote_download( $url, $local_file ) {

      $status = self::check_remote_status( $url );
      $response = [
         'status' => 'success',
      ];

      if ( $status >= 400 ) {

         $response = [
            'status' => 'error',
            'message' => __( 'The remote file cannot be accessed, please try again or contact us if this problem still persists. Status: ' . $status . '. File url: ' . $url, 'woosa-bol-content-connection' ),
         ];

      }else{

         //check if server allows opening remote URL
         if ( ini_get( 'allow_url_fopen' ) != 1 ) {

            $fileHandle = fopen( $local_file, "w" ); // Open the file on our server for writing.

            if ( false === $fileHandle ) {

               $response = [
                  'status' => 'error',
                  'message' => __( 'Unable to open file for writting at the followin location: ' . $local_file, 'woosa-bol-content-connection' ),
               ];

            }else{

               $handle     = curl_init();
               $max_time   = ini_get( "max_execution_time" ) - 1;

               curl_setopt_array(
                  $handle,
                  [
                     CURLOPT_URL       => $url,
                     CURLOPT_FILE      => $fileHandle,
                     CURLOPT_TIMEOUT   => $max_time,
                  ]
               );

               curl_exec( $handle );

               if ( curl_errno( $handle ) ) {

                  if ( stripos( curl_error( $handle ), 'Operation timed out' ) !== false  ) {

                     //remove the file
                     unlink( $local_file );

                     $response = [
                        'status' => 'error',
                        'message' => sprintf(
                           __( 'File download process failed because the process takes longer than the server allows, please increase the PHP <code>max_execution_time</code> then try again, %sclick here%s for more details.', 'woosa-bol-content-connection' ),
                           '<a href="https://support.woosa.nl/hc/en-us/articles/360006021138" target="_blank">',
                           '</a>'
                        ),
                     ];

                  }else{

                     Util::wc_error_log([
                        'error'  => curl_error( $handle ),
                        'url'    => $url
                     ], __FILE__, __LINE__ );

                     $response = [
                        'status' => 'error',
                        'message' => __( 'An error occured when download a remote file. For details check the log files.', 'woosa-bol-content-connection' ),
                     ];
                  }

               }

               curl_close( $handle );
               fclose( $fileHandle );

            }

         }else{

            if ( $fp_remote = fopen( $url, 'rb' ) ) {

               // read buffer, open in wb mode for writing
               if ( $fp_local = fopen( $local_file, 'wb' ) ) {

                  // read the file, buffer size 8k
                  while ($buffer = fread($fp_remote, 8192)) {
                     fwrite($fp_local, $buffer);
                  }

                  fclose($fp_local);

               }else{

                  Util::wc_error_log([
                     'error'  => error_get_last(),
                     'url'    => $url
                  ], __FILE__, __LINE__ );

                  $response = [
                     'status' => 'error',
                     'message' => __( 'An error occured when download a remote file. For details check the log files.', 'woosa-bol-content-connection' ),
                  ];
               }

               fclose($fp_remote);

            }else{

               Util::wc_error_log([
                  'error'  => error_get_last(),
                  'url'    => $url
               ], __FILE__, __LINE__ );

               $response = [
                  'status' => 'error',
                  'message' => __( 'An error occured when download a remote file. For details check the log files.', 'woosa-bol-content-connection' ),
               ];
            }

         }

      }

      return $response;

   }
}