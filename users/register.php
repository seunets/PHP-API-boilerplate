<?php
require_once '../lib/API.php';
require_once '../lib/Validation.php';
require_once '../model/User.php';

use Model\User;

   $api = new API( [ 'POST' ] );

   $requestData = $api-> getRequestData();

   if( property_exists( $requestData, 'email' ) && emailValid( $requestData-> email ) )
   {
      if( property_exists( $requestData, 'locale' ) )
      {
         if( property_exists( $requestData, 'password' ) )
         {
            try
            {
               $user = new User( [ 'email' => $requestData-> email, 'locale' => $requestData-> locale, 'basecurrency' => $requestData-> basecurrency, 'password' => $requestData-> password ] ) ;
               if( $user-> readByEmail() )
               {
                  $api-> setResponseCode( 409 )-> setBody( [ 'message' => $api-> say( 'REGISTER_FAIL_MESSAGE' ), 'reason' => $api-> say( 'EMAIL_ALREADY_REGISTERED' ) ] )-> send();
               } 

               $user-> create();
            }
            catch( Exception $e )
            {
               $api-> setResponseCode( 503 )-> setBody( [ 'message' => $api-> say( 'REGISTER_FAIL_MESSAGE' ), 'reason' => $e-> getMessage() ] )-> send();
            }

            $api-> setResponseCode( 201 )-> setBody( [ 'message' => $api-> say( 'USER_REGISTERED' ) ] )-> send();
         }
         else
         {
            $api-> setResponseCode( 400 )-> setBody( [ 'message' => $api-> say( 'REGISTER_FAIL_MESSAGE' ), 'reason' => $api-> say( 'INVALID_PASSWORD' ) ] )-> send();
         }
      }
      else
      {
         $api-> setResponseCode( 400 )-> setBody( [ 'message' => $api-> say( 'REGISTER_FAIL_MESSAGE' ), 'reason' => $api-> say( 'INVALID_LOCALE' ) ] )-> send();
      }
   }
   else
   {
      $api-> setResponseCode( 400 )-> setBody( [ 'message' => $api-> say( 'REGISTER_FAIL_MESSAGE' ), 'reason' => $api-> say( 'INVALID_EMAIL' ) ] )-> send();
   }
