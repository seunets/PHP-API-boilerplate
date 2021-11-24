<?php
namespace Model;

use \InvalidArgumentException;

require_once '../lib/DB.php';

abstract class AbstractEntity
{
protected $DB;

   public function __construct( array $properties = [] )
   {
      foreach( $properties as $key => $value )
      {
         if( property_exists( $this, $key ) )
         {
            $setter = 'set' . ucfirst( $key );
            if( method_exists( $this, $setter ) )
            {
               $this-> $setter( $value );
            }
            else
            {
               $this-> $key = $value;
            }
         }
         else
         {
            throw new InvalidArgumentException( "There's no attribute $key in " . get_class( $this ) );
         }
      }
      $this-> DB = new DB;
      return $this;
   }

   public function toArray()
   {
      $obj = ( array ) $this;
      foreach( $obj as $key => $value )
      {
         if( substr( $key, 1, 1 ) !== '*' )
         {
            $obj[ trim( str_replace( get_class( $this ), '', $key ) ) ] = $value;
         }
         unset( $obj[ $key ] );
      }
      return $obj;
   }
}
