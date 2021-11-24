<?php
require_once '../core/Config.php';
require_once '../lib/API.php';
require_once '../lib/Validation.php';
require_once '../model/User.php';

use Core\Config;
use Model\User;

   $api = new API( [ 'POST' ] );

   $requestData = $api-> getRequestData();

   if( property_exists( $requestData, 'email' ) && emailValid( $requestData-> email ) )
   {
      if( property_exists( $requestData, 'password' ) )
      {
         try
         {
            $user = new User( [ 'email' => $requestData-> email ] );
            $user-> readByEmail();

            if( password_verify( $requestData-> password, $user-> getPassword() ) )
            {
               $token = [ 'iss' => Config::ISS, 'iat' => time(), 'data' => [ 'id' => $user-> getId() ] ];

               require_once '../lib/JWT.php';
               $jwt = JWT::encode( $token );

               $user-> update( [ 'token' => $jwt, 'resettoken' => '' ] );

               $api-> setResponseCode( 200 )-> setBody( [ 'reason' => $api-> say( 'SUCCESSFUL_LOGIN' ), 'token' => $jwt, 'locale' => $user-> getLocale() ] )-> send();
            }
            else
            {
               $api-> setResponseCode( 401 )-> setBody( [ 'reason' => $api-> say( 'LOGIN_FAILED' ), 'message' => $api-> say( 'LOGIN_FAIL_MESSAGE' ) ] )-> send();
            }
         }
         catch( Exception $e )
         {
            $api-> setResponseCode( 503 )-> setBody( [ 'reason' => $e-> getMessage(), 'message' => $api-> say( 'LOGIN_FAIL_MESSAGE' ) ] )-> send();
         }
      }
      else
      {
         $api-> setResponseCode( 400 )-> setBody( [ 'reason' => $api-> say( 'INVALID_PASSWORD' ), 'message' => $api-> say( 'LOGIN_FAIL_MESSAGE' ) ] )-> send();
      }
   }
   else
   {
      $api-> setResponseCode( 400 )-> setBody( [ 'reason' => $api-> say( 'INVALID_EMAIL' ), 'message' => $api-> say( 'LOGIN_FAIL_MESSAGE' ) ] )-> send();
   }
