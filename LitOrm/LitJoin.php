<?php
/* 
 * ORM class for MySql Databases
 */
namespace LitOrm;
require_once './litautoload.php';
use LitOrm\LitHelper;

class LitJoin extends LitHelper
{
  protected $db = null;
  protected ?string $sentence = '';

  public ?string $table = null;

  public function __construct()
  {
    parent::__construct();
  }
  
  protected function join(string $joined): mixed
  {
    try{
      if($joined) {
        if(sizeof(explode(',', $joined)) === 1) {
          //$joined = $this->sanitize($joined);
          $joined = " INNER JOIN $joined";
        }elseif(sizeof(explode(',', $joined)) === 2) {
          $joined = explode(',', $joined);
          //$joined = " INNER JOIN " . $this->sanitize($joined[0]) . " ON " . $this->sanitize($joined[1]);
          $joined = " INNER JOIN " . $joined[0] . " ON " . $joined[1];
        }
        $this->composeSentence($joined);
        return $this;
    } else 
      return $this;
    }
    catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta JOIN');
    }
  }

  protected function leftJoin (string $joined): mixed
  {
    try{
      if($joined) {
        if(sizeof(explode(',', $joined)) === 1) {
          //$joined = $this->sanitize($joined);
          $joined = " LEFT JOIN $joined";
        }elseif(sizeof(explode(',', $joined)) === 2) {
          $joined = explode(',', $joined);
          //$joined = " LEFT JOIN " . $this->sanitize($joined[0]) . " ON " . $this->sanitize($joined[1]);
          $joined = " LEFT JOIN " . $joined[0] . " ON " . $joined[1];
        }
        $this->composeSentence($joined);
        return $this;
    } else 
      return $this;
    }
    catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta LEFT JOIN');
    }
  }

  protected function rightJoin (string $joined): mixed
  {
    try{
      if($joined) {
        if(sizeof(explode(',', $joined)) === 1) {
          //$joined = $this->sanitize($joined);
          $joined = " RIGHT JOIN $joined";
        }elseif(sizeof(explode(',', $joined)) === 2) {
          $joined = explode(',', $joined);
          //$joined = " RIGHT JOIN " . $this->sanitize($joined[0]) . " ON " . $this->sanitize($joined[1]);
          $joined = " RIGHT JOIN " . $joined[0] . " ON " . $joined[1];
        }
        $this->composeSentence($joined);
        return $this;
    } else 
      return $this;
    }
    catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta RIGHT JOIN');
    }
  }
    
  public function __destruct()
  {
    parent::__destruct();
    $this->table = null;
    $this->sentence = '';
    $this->db = null;
  }  
}