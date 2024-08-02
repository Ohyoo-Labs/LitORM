<?php 
/* 
  * ORM class for MySql Databases
 */
namespace LitOrm;
require_once './litautoload.php';
use LitOrm\LitManager;

class LitRawFunctions extends LitManager {
  public function __construct(){
    parent::__construct();
  }

  // Función para contar registros
  protected function rawCount($table, $where = null) {
    $table = $this->sanitize($table);
    $sql = $where ? "SELECT COUNT(*) FROM $table WHERE $where" : "SELECT COUNT(*) FROM $table";
    return $this->executeQuery($sql);
  }

  // Función para sumar valores de una columna
  protected function rawSum($table, $field, $where = null) {
    $table = $this->sanitize($table);
    $field = $this->sanitize($field);
    $sql = $where ? "SELECT SUM($field) FROM $table WHERE $where" : "SELECT SUM($field) FROM $table";
    return $this->executeQuery($sql);
  }

  // Función para calcular el promedio de valores de una columna
  protected function rawAvg($table, $field, $where = null) {
    $table = $this->sanitize($table);
    $field = $this->sanitize($field);
    $sql = $where ? "SELECT AVG($field) FROM $table WHERE $where" : "SELECT AVG($field) FROM $table";
    return $this->executeQuery($sql);
  }

  // Función para obtener el valor máximo de una columna
  protected function rawMax($table, $field, $where = null) {
    $table = $this->sanitize($table);
    $field = $this->sanitize($field);
    $sql = $where ? "SELECT MAX($field) FROM $table WHERE $where" : "SELECT MAX($field) FROM $table";
    return $this->executeQuery($sql);
  }

  // Función para obtener el valor mínimo de una columna
  protected function rawMin($table, $field, $where = null) {
    $table = $this->sanitize($table);
    $field = $this->sanitize($field);
    $sql = $where ? "SELECT MIN($field) FROM $table WHERE $where" : "SELECT MIN($field) FROM $table";
    return $this->executeQuery($sql);
  }

  // Función para obtener valores distintos de una columna
  protected function rawDistinct($table, $field, $where = null) {
    $table = $this->sanitize($table);
    $field = $this->sanitize($field);
    $sql = $where ? "SELECT DISTINCT $field FROM $table WHERE $where" : "SELECT DISTINCT $field FROM $table";
    return $this->executeQuery($sql, true);
  }

  // Función para agrupar valores de una columna
  protected function rawGroupBy($table, $field, $where = null) {
    $table = $this->sanitize($table);
    $field = $this->sanitize($field);
    $sql = $where ? "SELECT $field FROM $table WHERE $where GROUP BY $field" : "SELECT $field FROM $table GROUP BY $field";
    return $this->executeQuery($sql, true);
  }

  // Función para ordenar registros por una columna
  protected function rawOrderBy($table, $field, $order = 'ASC', $where = null) {
    $table = $this->sanitize($table);
    $field = $this->sanitize($field);
    $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC'; // Default to 'ASC' if invalid order
    $sql = $where ? "SELECT * FROM $table WHERE $where ORDER BY $field $order" : "SELECT * FROM $table ORDER BY $field $order";
    return $this->executeQuery($sql, true);
  }

  // Función para seleccionar registros donde el valor de un campo esté entre dos valores
  protected function rawBetween($table, $field, $value1, $value2) {
    $table = $this->sanitize($table);
    $field = $this->sanitize($field);
    $sql = "SELECT * FROM $table WHERE $field BETWEEN :value1 AND :value2";
    return $this->rawExecuteQueryWithParams($sql, ['value1' => $value1, 'value2' => $value2], true);
  }

  // Función para ejecutar una consulta y manejar errores
  private function rawExecuteQuery($sql, $fetchAll = false) {
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      return $fetchAll ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : $stmt->fetchColumn();
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $e->getMessage();
      }
      return false;
    }
  }

  // Función para ejecutar una consulta con parámetros y manejar errores
  private function rawExecuteQueryWithParams($sql, $params, $fetchAll = false) {
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute($params);
      return $fetchAll ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : $stmt->fetchColumn();
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $e->getMessage();
      }
      return false;
    }
  }

  public function __destruct()
  {
    parent::__destruct();
    $this->db = null;
  }
}
