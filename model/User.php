<?php
namespace Model;

require_once 'AbstractEntity.php';


class User extends AbstractEntity
{
private $table = 'users';
private $id;
private $email;
private $locale;
private $password;
private $token;
private $resettoken;


   public function __construct( $attributes = [] )
   {
      parent::__construct( $attributes );
   }


   public function setId( $id )
   {
      $this-> id = $id;
      return $this;
   }


   public function getId()
   {
      return $this-> id;
   }


   public function setEmail( $email )
   {
      $this-> email = $email;
      return $this;
   }


   public function getEmail()
   {
      return $this-> email;
   }


   public function setLocale( $locale )
   {
      $this-> locale = $locale;
      return $this;
   }


   public function getLocale()
   {
      return $this-> locale;
   }


   public function setPassword( $password )
   {
      $this-> password = password_hash( $password, PASSWORD_BCRYPT );
      return $this;
   }


   public function getPassword()
   {
      return $this-> password;
   }


   public function setToken( $token )
   {
      $this-> token = $token;
      return $this;
   }


   public function getToken()
   {
      return $this-> token;
   }

   public function setResetToken( $token )
   {
      $this-> resettoken = $token;
      return $this;
   }


   public function getResetToken()
   {
      return $this-> resettoken;
   }


   public function create()
   {
      $result = $this-> DB-> execute( "INSERT INTO $this->table( email, locale, password ) VALUES( ?, ?, ?, ? ) RETURNING id", [ $this-> email, $this-> locale, $this-> password ] );
      $this-> id = $result[ 0 ][ 'id' ];
   }


   public function readById()
   {
      $result = $this-> DB-> execute( "SELECT email, locale, password, token, resettoken FROM $this->table WHERE id = ?", [ $this-> id ] );

      if( empty( $result ) )
      {
         return FALSE;
      }
      else
      {
         $this-> fromArray( $result[ 0 ] );
      }
      return TRUE;
   }


   public function readByEmail()
   {
      $result = $this-> DB-> execute( "SELECT id, locale, password, token, resettoken FROM $this->table WHERE email = ?", [ $this-> email ] );
      if( empty( $result ) )
      {
         return FALSE;
      }
      else
      {
         $this-> fromArray( $result[ 0 ] );
      }
      return TRUE;
   }


   public function update( $properties = [] )
   {
      if( !empty( $properties ) )
      {
         $sql = "UPDATE $this->table SET ";

         $sql .= implode( ' = ?, ', array_keys( $properties ) ) . ' = ? WHERE id = ?';
      
         $properties[] = $this-> id;
         $this-> DB-> execute( $sql, array_values( $properties ) );
      }
   }


   private function fromArray( $properties = [] )
   {
      foreach( $properties as $key => $value )
      {
         if( property_exists( $this, $key ) )
         {
            $this-> $key = $value;
         }
         else
         {
            throw new InvalidArgumentException( "There's no attribute $key in " . __CLASS__ );
         }
      }
   }
}
