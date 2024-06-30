<?php 
namespace LitOrm\Boostrap;

class Boost 
{
  const SETTINGS = [
    'ENVIROMENT' => 'development',
  ];

  const DB_SETTINGS = [
    'HOST' => '',
    'DBNAME' => '',
    'USERNAME' => '',
    'PASSWORD' => '',
    'CHARSET' => '',
  ];
  public function boost (): void
  {
    foreach (Boost::SETTINGS as $key => $value){
      if (!defined($key))
        define($key, $value);
    }
  }
}
