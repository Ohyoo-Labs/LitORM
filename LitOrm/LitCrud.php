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

  protected ?bool $isTransact = false;

  public function __construct()
  {
    parent::__construct();
  }

  protected function transact(?bool $transact = true): mixed
  {
    $this->isTransact = $transact ?? false;
    return $this;
  }

  protected function select(?string $fields = null): mixed
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
  //Original
  /* protected function insert(?array $data): mixed
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
  } */

  // Metodo modificado para procesar e insertar los datso directamente en el mismo metodo
  //@param array $data (opcional) Si llega vacio no se ejecuta la consulta
  //@param bool $withId (opcional) Retorna el id del registro insertado
  protected function insert(?array $data): mixed //, ?bool $withId = false
  {
    try {
      if ($data) {
        /* if ($this->isTransact)
          $this->db->beginTransaction(); */
        $this->params = $data;
        $fields = implode(',', array_map([$this, 'sanitize'], array_keys($data)));
        $placeholders = implode(',', array_map(function ($v) {
          return ":{$v}";
        }, array_keys($data)));
        $this->composeSentence("INSERT INTO $this->table ({$fields}) VALUES ({$placeholders})");
        return $this;
        /* $stmt = $this->db->prepare("INSERT INTO $this->table ({$fields}) VALUES ({$placeholders})");
        foreach ($data as $key => $value) {
          $stmt->bindValue(':' . $key, $value);
        }
        $executionResult = $stmt->execute();
        if ($executionResult) {
          if ($withId) {
            $executionResult = $this->db->lastInsertId();
            if ($this->isTransact)
              $this->db->commit();
          }
        } else {
          throw new Exception('Error al insertar el registro');
        }
        return $executionResult; */
      } else {
        throw new \PDOException('No se han proporcionado datos para insertar');
      }
    } catch (\PDOException $e) {
      /* if ($this->isTransact)
        $this->db->rollBack(); */
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta INSERT');
    }
  }

  protected function update(?array $data): mixed //, ?string $condition
  {
    try {
      if ($data) {
        /* if($this->isTransact)
          $this->db->beginTransaction(); */
        $this->params = $data;
        $placeholders = implode(',', array_map(function ($v) {
          return $this->sanitize($v) . '=:' . $this->sanitize($v);
        }, array_keys($data)));
        $this->composeSentence("UPDATE {$this->table} SET {$placeholders}");
        return $this;
        /* if ($condition) {
          $this->where($condition);
        }
        $stmt = $this->db->prepare($this->sentence);
        foreach ($data as $key => $value) {
          $stmt->bindValue(':' . $key, $value);
        }
        $resultUpdate = $stmt->execute();
        if ($resultUpdate) {
          $resultUpdate = $stmt->rowCount();
          if ($this->isTransact)
            $this->db->commit();
        } else {
          throw new Exception('Error al actualizar el registro');
        }
        return $resultUpdate; */
        //$this->composeSentence("UPDATE {$this->table} SET {$fields}");
        //return $this;
      } else
        throw new \PDOException('No se han proporcionado datos para actualizar');
    } catch (\PDOException $e) {
      /* if ($this->isTransact)
        $this->db->rollBack(); */
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta UPDATE');
    }
  }

  protected function delete(?string $table = null): mixed
  {
    try {
      $table = $table ? $this->sanitize($table) : $this->table;
      $this->composeSentence("DELETE FROM $table");
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
    parent::__destruct();
    $this->sentence = null;
    $this->table = null;
    $this->db = null;
  }  
}