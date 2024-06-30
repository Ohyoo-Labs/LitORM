<?php
/* 
 * ORM class for MySql Databases
 */
namespace LitOrm;
require_once './litautoload.php';
use LitOrm\LitRawQueries;

class LitHelper extends LitRawQueries
{
  protected $db = null;
  protected ?string $sentence = '';

  public ?string $table = null;

  public function __construct()
  {
    parent::__construct();
  }

  protected function distinct(): mixed
  {
    try {
      $selectPosition = stripos($this->sentence, 'SELECT');
      if ($selectPosition !== false) {
        $selectPosition += strlen('SELECT');
        $sql = substr_replace($this->sentence, ' DISTINCT', $selectPosition, 0);
      }
      return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta DISTINCT');
    }
  }
  protected function between(string $column, string $value1, string $value2, string $concat = null): mixed
  {
    try {
      $column = $this->sanitize($column);
      $value1 = $this->sanitize($value1);
      $value2 = $this->sanitize($value2);
      if(!$concat)
        $between = " WHERE $column BETWEEN $value1 AND $value2";
      else
        $between = " $concat $column BETWEEN $value1 AND $value2";
      //$between = " $column BETWEEN $value1 AND $value2";
      $this->composeSentence($between);
      return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta BETWEEN');
    }
  }

  protected function union(): mixed
  {
    try {
      $this->composeSentence(' UNION ');
      return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta UNION');
    }
  }

  protected function groupBy(string $group): mixed
  {
    try {
      if ($group) {
        $group = $this->sanitize($group);
        $group = " GROUP BY $group";
        $this->composeSentence($group);
        return $this;
      } else
        return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta GROUP BY');
    }
  }

  protected function orderBy(string $order): mixed
  {
    try {
      if ($order) {
        $order = $this->sanitize($order);
        $order = " ORDER BY $order";
        $this->composeSentence($order);
        return $this;
      } else
        return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta ORDER BY');
    }
  }

  protected function limit(int $limit): mixed
  {
    try {
      if ($limit) {
        $limit = " LIMIT $limit";
        $this->composeSentence($limit);
        return $this;
      } else
        return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta LIMIT');
    }
  }

  protected function offset(int $offset): mixed
  {
    try {
      if ($offset) {
        $offset = " OFFSET $offset";
        $this->composeSentence($offset);
        return $this;
      } else
        return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta OFFSET');
    }
  }

  protected function having(?string $having): mixed
  {
    try {
      if ($having) {
        $having = $this->sanitize($having);
        $having = " HAVING $having";
        $this->composeSentence($having);
        return $this;
      } else
        return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta HAVING');
    }
  }

  public function __destruct()
  {
    $this->db = null;
  }
}