<?php
require_once '../core/Config.php';
require_once '../lib/API.php';
require_once '../lib/Validation.php';
require_once '../model/User.php';

use Core\Config;
use Model\User;

   $api = new API( [ 'POST' ] );

   $requestData = $api-> getRequestData();

   if( $requestData-> email && emailValid( $requestData-> email ) )
   {
      try
      {
         $user = new User( [ 'email' => $requestData-> email ] );
         if( $user-> readByEmail() )
         {
            if( $user-> getResetToken() )
            {
               $api-> setResponseCode( 401 )-> setBody( [ 'reason' => $api-> say( 'PENDING_RESET' ), 'message' => $api-> say( 'RESET_FAIL_MESSAGE' ) ] )-> send();
            }
            else
            {
               $resettoken = [ 'iss' => Config::ISS, 'iat' => time(), 'data' => [ 'id' => $user-> getId() ] ];

               require_once '../lib/JWT.php';
               $jwt = JWT::encode( $resettoken );

               $user-> update( [ 'resettoken' => $jwt ] );
            }
            xxmail( $user-> getEmail(), $api-> say( 'PASSWORD_RESET' ), Config::ISS . '/reset.html?resettoken=' . $jwt . '&token=' . $user-> getToken(), '' );
            $api-> setResponseCode( 200 )-> setBody( [ 'reason' => $api-> say( 'RESET_EMAIL_SENT' ), 'message' => $api-> say( 'RESET_REQUESTED' ) ] )-> send();
         }
         else
         {
            $api-> setResponseCode( 401 )-> setBody( [ 'reason' => $api-> say( 'NONEXISTENT_EMAIL' ), 'message' => $api-> say( 'RESET_FAIL_MESSAGE' ) ] )-> send();
         }
      }
      catch( Exception $e )
      {
         $api-> setResponseCode( 503 )-> setBody( [ 'reason' => $e-> getMessage(), 'message' => $api-> say( 'RESET_FAIL_MESSAGE' ) ] )-> send();
      }
   }
   else
   {
      $api-> setResponseCode( 400 )-> setBody( [ 'reason' => $api-> say( 'INVALID_EMAIL' ), 'message' => $api-> say( 'RESET_FAIL_MESSAGE' ) ] )-> send();
   }

function xxmail( $to, $subject, $body, $headers )
{
}
