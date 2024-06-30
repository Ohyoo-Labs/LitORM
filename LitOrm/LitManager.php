<?php 
/* 
 * ORM class for MySql Databases
 */
namespace LitOrm;
require_once './litautoload.php';
class LitManager {
  protected $db = null;

  public function __construct(){}
  // Función para sanitizar nombres de tablas y campos
  protected function sanitize($name) {
    // Permitir solo letras, números y guiones bajos
    return preg_replace('/[^a-zA-Z0-9_*. \-]/', '', $name);
  }

  // Verifica si existe un registro en la tabla dada con la condición especificada
  protected function exists($table, $where) {
    $table = $this->sanitize($table);
    $sql = "SELECT EXISTS(SELECT 1 FROM $table WHERE $where)";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
  }

  // Modifica la estructura de una tabla
  protected function alter($table, $fields) {
    $table = $this->sanitize($table);
    try {
      $sql = "ALTER TABLE $table $fields";
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      return true;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $e->getMessage();
      }
      return false;
    }
  }

  // Elimina una tabla
  protected function drop($table) {
    $table = $this->sanitize($table);
    try {
      $sql = "DROP TABLE $table";
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      return true;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $e->getMessage();
      }
      return false;
    }
  }

  // Crea una nueva tabla
  protected function create($table, $fields) {
    $table = $this->sanitize($table);
    try {
      $this->db->beginTransaction();
      $sql = "CREATE TABLE $table ($fields)";
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $this->db->commit();
      return true;
    } catch (\Exception $e) {
      $this->db->rollBack();
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $e->getMessage();
      }
      return false;
    }
  }

  // Vacía una tabla
  protected function truncate($table) {
    $table = $this->sanitize($table);
    try {
      $this->db->beginTransaction();
      $sql = "TRUNCATE TABLE $table";
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $this->db->commit();
      return true;
    } catch (\Exception $e) {
      $this->db->rollBack();
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $e->getMessage();
      }
      return false;
    }
  }
  public function __destruct(){
    $this->db = null;
  }
}