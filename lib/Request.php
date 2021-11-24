<?php
class Request
{
   private $data;
   private $headers;
   private $method;

   public function __construct()
   {
      $this-> method = strtoupper( $_SERVER[ 'REQUEST_METHOD' ] );
      if( $this-> method == 'GET' )
      {
         $this-> data = ( object ) $_GET;
      }
      else
      {
         $this-> data = json_decode( file_get_contents( 'php://input' ) );
      }
      $this-> headers = ( object ) getallheaders();
   }

   public function getHeaders()
   {
      return $this-> headers;
   }

   public function getMethod()
   {
      return $this-> method;
   }

   public function getData()
   {
      return $this-> data;
   }
}
