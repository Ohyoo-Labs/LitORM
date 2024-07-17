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
  protected ?bool $isTransact = false;
  protected ?array $params = null;

  public function __construct()
  {
    parent::__construct();
  }

  protected function query(?string $sql = null): mixed
  {
    try {
      if (!$sql || empty($sql))
        $sql = $this->sentence;
      $this->sentence = '';
      $result = $this->db->query($sql);
      if(str_starts_with(trim($sql), 'DELETE') || str_starts_with(trim($sql), 'delete'))
        return $result->rowCount();  
      return $result->fetchAll(\PDO::FETCH_ASSOC);
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

  protected function save(?bool $withId = false)
  {
    try {
      if ($this->isTransact)
          $this->db->beginTransaction();
      $sql = $this->sentence;
      $this->sentence = '';
      $stmt = $this->db->prepare($sql);
      foreach ($this->params as $key => $value) {
        $stmt->bindValue(":{$key}", $value);
      }
      $saved = $stmt->execute();
      if ($saved && $stmt->rowCount() > 0) {
        if(str_starts_with(trim($sql), 'INSERT') || str_starts_with(trim($sql), 'insert'))
            $saved = $withId ? $this->db->lastInsertId() : true;
        else
          $saved = $withId ? $stmt->rowCount() : true;
        if ($this->isTransact) {
          $this->db->commit();          
        }
        return $saved;
      } else {
        throw new \PDOException('Error al guardar los datos');
      }
      //return $fetchAll ? $stmt->fetchAll(PDO::FETCH_ASSOC) : $stmt->fetchColumn();
    } catch (\PDOException $e) {
      if ($this->isTransact)
        $this->db->rollBack();
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