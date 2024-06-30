<?php
/* 
 * ORM class for MySql Databases
 */
namespace LitOrm;
require_once './litautoload.php';
use LitOrm\LitCrud;
class LitExecutor extends LitCrud
{
  protected $db = null;
  protected ?string $sentence = '';

  public ?string $table = null;

  public function __construct()
  {
    parent::__construct();
  }

  protected function query($sql)
  {
    try {
      $resukt = $this->db->query($sql);
      return $resukt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\Throwable $th) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development')
        return $th->getMessage();
      return false;
    }
  }
  protected function raw($sql)
  {
    return $this->executeQuery($sql, true);
  }

  protected function executeQuery($sql, $fetchAll = false)
  {
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

  // Función para ejecutar una consulta con parámetros y manejar errores (veremos su utilidad más adelante)
  private function executeQueryWithParams($sql, $params, $fetchAll = false)
  {
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
  
  public function get(): ?array
  {
    try {
      $sql = $this->sentence;
      $this->sentence = '';
      //return $sql;
      return $this->executeQuery($sql, true);
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta GET');
    }
  }  
  
  public function __destruct()
  {
    $this->db = null;
  }  
}