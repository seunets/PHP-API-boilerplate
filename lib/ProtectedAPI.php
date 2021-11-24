<?php
require_once 'API.php';
require_once 'JWT.php';

use Model\User;

class ProtectedAPI extends API
{
private $userid;

   public function __construct( $allowedMethods = [ 'POST', 'GET', 'PUT', 'DELETE' ] )
   {
      parent::__construct( $allowedMethods );
      if( $this-> getHeaders()-> Authorization && ( $jwt = sscanf( $this-> getHeaders()-> Authorization, "Bearer %s" )[ 0 ] ) !== NULL )
      {
         try
         {
            $decoded = JWT::decode( $jwt );

            require_once '../model/User.php';

            $user = new User( [ 'id' => $decoded-> data-> id ] );

            try
            {
               $user-> readById();

               if( $user-> getToken() != $jwt )
               {
                  $this-> setResponseCode( 403 )-> setBody( [ 'reason' => $this-> say( 'INVALID_TOKEN' ), 'message' => $this-> say( 'ACCESS_DENIED' ) ] )-> send();
               }
            }
            catch( Exception $e )
            {
               $this-> setResponseCode( 503 )-> setBody( [ 'reason' => $e-> getMessage(), 'message' => $this-> say( 'DATABASE_EXCEPTION' ) ] )-> send();
            }
         }
         catch( Exception $e )
         {
            $this-> setResponseCode( 401 )-> setBody( [ 'reason' => $e-> getMessage() ] )-> send();
         }
         $this-> userid = $user-> getId();
      }
      else
      {
         $this-> setResponseCode( 400 )-> setBody( [ 'message' => $this-> say( 'ACCESS_DENIED' ), 'reason' => $this-> say( 'MISSING_TOKEN' ) ] )-> send();
      }
   }


   public function getUserId()
   {
      return $this-> userid;
   }
}
