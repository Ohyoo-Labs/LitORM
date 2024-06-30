<?php
/* 
 * ORM class for MySql Databases
 */
namespace LitOrm;
require_once './litautoload.php';
use LitOrm\LitWhere;

class LitCrud extends LitWhere
{
  protected $db = null;
  protected ?string $sentence = '';

  public ?string $table = null;

  public function __construct()
  {
    parent::__construct();
  }
  protected function select(?string $fields): mixed
  {
    try{
    if(!$fields && $fields !== '') {
      $fields = '*';
    }
    elseif (sizeof(explode(',', $fields)) > 1) {
      $fields = implode(',', array_map(function ($v) {
        return $this->sanitize($v);
      }, explode(',', $fields)));
    } else {
      $fields = $this->sanitize($fields);
    } 
    $select = !empty($fields) ? "SELECT $fields" : "SELECT";  
    $this->composeSentence($select);
    return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta SELECT');
    }
  }
  protected function from (?string $table): mixed
  {
    try{
    if($table) {
      $this->table = $this->sanitize($table);
      // Verificar si el ultimo caracter en $this->sentence es una coma y si es asi, eliminarla
      $this->sentence = rtrim($this->sentence, ',');
      $this->composeSentence(" FROM $this->table");
      return $this;
    }else 
      return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta FROM');
    }
  }
  protected function insert(?array $data): mixed
  {
    try{
    if($data) {
      $fields = implode(',', array_map([$this, 'sanitize'], array_keys($data)));
      $values = implode(',', array_map(function ($v) {
        return ':' . $v;
      }, array_keys($data)));
      $this->composeSentence("INSERT INTO $this->table ($fields) VALUES ($values)");
      return $this;
    }else 
      return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta INSERT');
    }
  }

  protected function update(?array $data): mixed
  {
    try{
    if($data) {
      $fields = implode(',', array_map(function ($v) {
        return $this->sanitize($v) . '=:' . $this->sanitize($v);
      }, array_keys($data)));
      $this->composeSentence("UPDATE $this->table SET $fields");
      return $this;
    }else 
      return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta UPDATE');
    }
  }

  protected function delete(): mixed
  {
    try{
    $this->composeSentence("DELETE FROM $this->table");
    return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta DELETE');
    }
  }
  public function __destruct()
  {
    $this->db = null;
  }  
}