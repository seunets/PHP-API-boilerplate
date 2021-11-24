<?php
require_once '../core/Config.php';
require_once '../lib/ProtectedAPI.php';
require_once '../model/User.php';

use Core\Config;
use Model\User;

   $api = new ProtectedAPI( [ 'PUT' ] );

   $requestData = $api-> getRequestData();

   if( property_exists( $requestData, 'password' ) )
   {
      try
      {
         $user = new User( [ 'id' => $api-> getUserId() ] );
         $user-> readById();

         $token = [ 'iss' => Config::ISS, 'iat' => time(), 'data' => [ 'id' => $user-> getId() ] ];

         require_once '../lib/JWT.php';
         $jwt = JWT::encode( $token );


         $user-> setPassword( $requestData-> password );
         $user-> update( [ 'password' => $user-> getPassword(), 'token' => $jwt, 'resettoken' => '' ] );

         $api-> setResponseCode( 200 )-> setBody( [ 'message' => $api-> say( 'PASSWORD_UPDATED' ), 'email' => $user-> getEmail() ] )-> send();
      }
      catch( Exception $e )
      {
         $api-> setResponseCode( 503 )-> setBody( [ 'message' => $e-> getMessage(), 'reason' => $api-> say( 'RESET_FAIL_MESSAGE' ) ] )-> send();
      }
   }
   else
   {
      $api-> setResponseCode( 400 )-> setBody( [ 'message' => $api-> say( 'INVALID_PASSWORD' ), 'reason' => $api-> say( 'RESET_FAIL_MESSAGE' ) ] )-> send();
   }
