<?php
require_once 'Request.php';
require_once 'Response.php';

class API
{
   private $request;
   private $response;
   private $language;
   private $messages;

   public function __construct( $allowedMethods = [ 'POST', 'GET', 'PUT', 'DELETE' ] )
   {
      $this-> request = new Request;
      $this-> response = new Response;

      $this-> setLanguage( isset( $_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ] ) ? strtolower( explode( ',', $_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ] )[ 0 ] ) : 'en-us' );

      $allowedMethods = array_map( 'strtoupper', $allowedMethods );

      if( in_array( $this-> request-> getMethod(), $allowedMethods ) )
      {
         if( $this-> getRequestMethod() !== 'GET' && ( is_null( $this-> request-> getData() ) || $this-> request-> getData() == '' ) )
         {
            $this-> setResponseCode( 400 )-> setBody( [ 'reason' => $this-> say( 'INVALID_JSON' ) ] )-> send();
         }
      }
      else
      {
         $this-> setResponseCode( 405 )-> send();
      }
   }

   public function getHeaders()
   {
      return $this-> request-> getHeaders();
   }

   public function getRequestData()
   {
      return $this-> request-> getData();
   }

   public function getRequestMethod()
   {
      return $this-> request-> getMethod();
   }

   public function setResponseCode( $code )
   {
      return $this-> response-> setCode( $code );
   }

   public function setLanguage( $language )
   {
      $this-> language = $language;

      $this-> messages = array();

      $this-> messages = @array_merge( $this-> messages, @parse_ini_file( '../lang/' . $this-> language . '.ini' ) );

      $backtrace = debug_backtrace();

      $pathParts = pathinfo( end( $backtrace )[ 'file' ] );

      if( is_file( $langFile = $pathParts[ 'dirname' ] . '/lang/' . $language . '.ini' ) )
      {
         $this-> messages = array_merge( $this-> messages, parse_ini_file( $langFile ) );
      }
   }

   public function say( $message )
   {
      if( @array_key_exists( $message, $this-> messages ) )
      {
         return $this-> messages[ $message ];
      }
      else
      {
         return "NONEXISTENT MESSAGE ($message)";
      }
   }
}
