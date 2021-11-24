<?php
namespace Model;
require_once '../core/Config.php';

use \Core\Config;
use \PDO;
use \Exception;

class DB
{
private $connection;


   public function execute( $sql, $parameters = [] )
   {
      $statement = $this-> getConnection()-> prepare( $sql );
      foreach( $parameters as $key => $value )
      {
         if( $value !== NULL )
         {
            $statement-> bindValue( $key + 1, $value );
         }
      }
      if( !$statement-> execute() )
      {
         $this-> getConnection()-> rollBack();
         throw new Exception( $statement-> errorInfo()[ 2 ] );
      }
      $colTypes = [];
      for( $i = 0; $i < $statement-> columnCount(); $i++ )
      {
         $columnMeta = $statement-> getColumnMeta( $i );
         $colTypes[ $columnMeta[ "name" ] ] = $columnMeta[ "native_type" ];
      }
      $results = [];
      if( $statement-> rowCount() > 0 )
      {
         while( $row = $statement-> fetch( PDO::FETCH_ASSOC ) )
         {
            array_walk( $row, function( &$item, $key, $colTypes )
            {
               $item = $colTypes[ $key ] == "numeric" ? $item + 0 : $item;
            }, $colTypes );
            $results[] = $row;
         }
      }

      return $results;
   }


   public function beginTransaction()
   {
      $this-> getConnection()-> beginTransaction();
   }


   public function commit()
   {
      $this-> getConnection()-> commit();
   }


   private function getConnection()
   {
      if( !$this-> connection )
      {
         try
         {
            $this-> connection = new PDO( Config::DBDRIVER . ':host=' . Config::DBHOST . ';dbname=' . Config::DBNAME, Config::DBUSER, Config::DBPASSWORD, [ PDO::ATTR_PERSISTENT => true, PDO::ATTR_EMULATE_PREPARES => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ] );
         }
         catch( PDOException $e )
         {
            throw new Exception( $e-> getMessage() );
         }
      }
      return $this-> connection;
   }
}
