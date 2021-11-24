<?php
require_once '../lib/ProtectedAPI.php';

use Model\User;

   $api = new ProtectedAPI( [ 'PUT' ] );

   $requestData = $api-> getRequestData();

   $properties = array();

   if( property_exists( $requestData, 'locale' ) )
   {
      if( $requestData-> locale !== '' )
      {
         $properties[ 'locale' ] = $requestData-> locale;
      }
      else
      {
         $api-> setResponseCode( 400 )-> setBody( [ 'reason' => $api-> say( 'INVALID_LOCALE' ), 'message' => $api-> say( 'UPDATE_FAIL_MESSAGE' ) ] )-> send();
      }
   }

   if( empty( $properties ) )
   {
      $api-> setResponseCode( 400 )-> setBody( [ 'reason' => $api-> say( 'NOTHING_TO_UPDATE' ), 'message' => $api-> say( 'UPDATE_FAIL_MESSAGE' ) ] )-> send();
   }

   try
   {
      $user = new User( [ 'id' => $api-> getUserId() ] );
      $user-> readById();
      $user-> update( $properties );
      $api-> setResponseCode( 200 )-> setBody( [ 'reason' => $api-> say( 'USER_UPDATED' ) ] )-> send();
   }
   catch( Throwable $e )
   {
      $api-> setResponseCode( 503 )-> setBody( [ 'reason' => $e-> getMessage(), 'message' => $api-> say( 'UPDATE_FAIL_MESSAGE' ) ] )-> send();
   }
