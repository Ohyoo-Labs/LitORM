<?php
/* 
 * ORM class for MySql Databases
 */
namespace LitOrm;

require_once './litautoload.php';
use LitOrm\LitFunctions;

class LitRawQueries extends LitFunctions
{
  protected $db = null;
  public ?string $table = null;
  public function __construct()
  {
    parent::__construct();
  }

  // Método para obtener registros
  protected function selectRaw($table, $where = null, $fields = null): array
  {
    $table = $this->sanitize($table);
    $sql = $fields ? "SELECT $fields FROM $table" : "SELECT * FROM $table";
    if ($where) {
      $sql .= " WHERE $where";
    }
    return $this->executeQuery($sql, true);
  }

  // Método para insertar registros
  protected function insertRaw($table, $data)
  {
    $table = $this->sanitize($table);
    try {
      $data = (array) $data;
      $fields = implode(',', array_map([$this, 'sanitize'], array_keys($data)));
      $values = implode(',', array_map(function ($v) {
        return ':' . $v;
      }, array_keys($data)));
      $sql = "INSERT INTO $table ($fields) VALUES ($values)";
      $stmt = $this->db->prepare($sql);
      foreach ($data as $key => $value) {
        $stmt->bindValue(':' . $this->sanitize($key), $value);
      }
      $stmt->execute();
      return $this->db->lastInsertId();
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development')
        return $e->getMessage();
      return false;
    }
  }

  // Método para actualizar registros
  protected function updateRaw($table, $data, $where)
  {
    $table = $this->sanitize($table);
    try {
      $data = (array) $data;
      $id = $this->selectRaw($table, $where, 'id');
      $fields = implode(',', array_map(function ($v) {
        return $this->sanitize($v) . '=:' . $this->sanitize($v);
      }, array_keys($data)));
      $sql = "UPDATE $table SET $fields WHERE $where";
      $stmt = $this->db->prepare($sql);
      foreach ($data as $key => $value) {
        $stmt->bindValue(':' . $this->sanitize($key), $value);
      }
      $stmt->execute();
      return $id['id'];
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development')
        return $e->getMessage();
      return false;
    }
  }

  // Método para eliminar registros
  protected function deleteRaw($table, $where)
  {
    $table = $this->sanitize($table);
    try {
      $id = $this->selectRaw($table, $where, 'id');
      $sql = "DELETE FROM $table WHERE $where";
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      return $id['id'];
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development')
        return $e->getMessage();
      return false;
    }
  }

  // Método para realizar una unión simple entre dos tablas
  protected function rawJoin($table1, $table2, $on, $fields = null)
  {
    $table1 = $this->sanitize($table1);
    $table2 = $this->sanitize($table2);
    $sql = $fields ? "SELECT $fields FROM $table1 JOIN $table2 ON $on" : "SELECT * FROM $table1 JOIN $table2 ON $on";
    return $this->executeQuery($sql, true);
  }

  // Método para realizar una unión izquierda entre dos tablas
  protected function rawLeftJoin($table1, $table2, $on, $fields = null)
  {
    $table1 = $this->sanitize($table1);
    $table2 = $this->sanitize($table2);
    $sql = $fields ? "SELECT $fields FROM $table1 LEFT JOIN $table2 ON $on" : "SELECT * FROM $table1 LEFT JOIN $table2 ON $on";
    return $this->executeQuery($sql, true);
  }

  // Método para realizar una unión derecha entre dos tablas
  protected function rawRightJoin($table1, $table2, $on, $fields = null)
  {
    $table1 = $this->sanitize($table1);
    $table2 = $this->sanitize($table2);
    $sql = $fields ? "SELECT $fields FROM $table1 RIGHT JOIN $table2 ON $on" : "SELECT * FROM $table1 RIGHT JOIN $table2 ON $on";
    return $this->executeQuery($sql, true);
  }

  // Método para realizar una unión completa entre dos tablas
  protected function rawFullJoin($table1, $table2, $on, $fields = null)
  {
    $table1 = $this->sanitize($table1);
    $table2 = $this->sanitize($table2);
    $sql = $fields ? "SELECT $fields FROM $table1 FULL JOIN $table2 ON $on" : "SELECT * FROM $table1 FULL JOIN $table2 ON $on";
    return $this->executeQuery($sql, true);
  }

  // Método para realizar múltiples uniones entre varias tablas
  protected function rawMultiJoin($tables, $ons, $fields)
  {
    if (count($tables) < 2 || count($tables) !== count($ons) + 1) {
      throw new \Exception("Invalid number of tables or join conditions");
    }

    $sanitizedTables = array_map([$this, 'sanitize'], $tables);
    $sanitizedOns = array_map([$this, 'sanitize'], $ons);
    $sql = "SELECT $fields FROM " . array_shift($sanitizedTables);

    foreach ($sanitizedTables as $index => $table) {
      $sql .= " JOIN $table ON " . $sanitizedOns[$index];
    }

    return $this->executeQuery($sql, true);
  }

  public function __destruct()
  {
    parent::__destruct();
    $this->db = null;
  }
}