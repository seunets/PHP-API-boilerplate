<?php
class Response
{
   private $code;
   private $body;

   public function __construct( $code = 0, $body = NULL )
   {
      $this-> code = $code;
      $this-> body = $body;
   }

   public function setCode( $code )
   {
      $this-> code = $code;
      return $this;
   }

   public function setBody( $body = NULL )
   {
      $this-> body = $body;
      return $this;
   }

   public function send()
   {
      http_response_code( $this-> code );
      if( !is_null( $this-> body ) )
      {
         header( 'Content-type: application/json' );
         echo json_encode( $this-> body );
      }
      exit;
   }
}
