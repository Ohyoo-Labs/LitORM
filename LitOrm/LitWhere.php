<?php
/* 
 * ORM class for MySql Databases
 */
namespace LitOrm;
require_once './litautoload.php';
use LitOrm\LitJoin;
class LitWhere extends LitJoin
{
  protected $db = null;
  protected ?string $sentence = '';

  public ?string $table = null;

  public function __construct()
  {
    parent::__construct();
  }

  protected function formatWhere(string $where): mixed
  {
    try {
      if (sizeof(explode(',', $where)) === 1) {
        $where = $this->sanitize($where);
        $where = "$where = $where";
      } elseif (sizeof(explode(',', $where)) === 2) {
        $where = explode(',', $where);
        $where = implode(' = ', array_map(function ($v) {
          return $this->sanitize($v);
        }, $where));
      } else {
        //$where = str_replace(',', '', $where);
        $where = explode(',', $where);
        $where[0] = $this->sanitize($where[0]);
        $where[2] = $this->sanitize($where[2]);
        $where = implode(' ', $where);
      }
      return $where;
    } catch (\Throwable $th) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($th->getMessage());
      }
      return $this->sendError('Error en la consulta WHERE');
    }
  }

  protected function where(?string $where): mixed
  {
    try {
      if ($where) {
        /* if (sizeof(explode(',', $where)) === 1) {
          $where = $this->sanitize($where);
          $where = "$where = $where";
        } elseif (sizeof(explode(',', $where)) === 2) {
          $where = explode(',', $where);
          $where = implode(' = ', array_map(function ($v) {
            return $this->sanitize($v);
          }, $where));
        } else {
          //$where = str_replace(',', '', $where);
          $where = explode(',', $where);
          $where[0] = $this->sanitize($where[0]);
          $where[2] = $this->sanitize($where[2]);
          $where = implode(' ', $where);
        } */
        $where = $this->formatWhere($where);
        if (strpos($this->sentence, 'WHERE') === false) {
          $where = " WHERE $where";
        } else {
          $where = " AND $where";
        }
        $this->composeSentence($where);        
      }
        return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta WHERE');
    }
  }

  protected function andWhere(?string $where): mixed
  {
    try {
      if ($where) {
        /* if (sizeof(explode(',', $where)) === 1) {
          $where = $this->sanitize($where);
          $where = "$where = $where";
        } elseif (sizeof(explode(',', $where)) === 2) {
          $where = explode(',', $where);
          $where = implode(' = ', array_map(function ($v) {
            return $this->sanitize($v);
          }, $where));
        } else {
          //$where = str_replace(',', '', $where);
          $where = explode(',', $where);
          $where[0] = $this->sanitize($where[0]);
          $where[2] = $this->sanitize($where[2]);
          $where = implode(' ', $where);
        } */
        $where = $this->formatWhere($where);
        $where = " AND $where";
        $this->composeSentence($where);
      }
      return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta AND WHERE');
    }
  }

  protected function orWhere(?string $where): mixed
  {
    try {
      if ($where) {
        /* if (sizeof(explode(',', $where)) === 1) {
          $where = $this->sanitize($where);
          $where = "$where = $where";
        } elseif (sizeof(explode(',', $where)) === 2) {
          $where = explode(',', $where);
          $where = implode(' = ', array_map(function ($v) {
            return $this->sanitize($v);
          }, $where));
        } else {
          //$where = str_replace(',', '', $where);
          $where = explode(',', $where);
          $where[0] = $this->sanitize($where[0]);
          $where[2] = $this->sanitize($where[2]);
          $where = implode(' ', $where);
        } */
        $where = $this->formatWhere($where);
        $where = " OR $where";
        $this->composeSentence($where);
      }
        return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta OR WHERE');
    }
  }

  protected function subAnd(?string $where, ?bool $close = false): mixed
  {
    try {
      if ($where) {
        /* if (sizeof(explode(',', $where)) === 1) {
          $where = $this->sanitize($where);
          $where = "$where = $where";
        } elseif (sizeof(explode(',', $where)) === 2) {
          $where = explode(',', $where);
          $where = implode(' = ', array_map(function ($v) {
            return $this->sanitize($v);
          }, $where));
        } else {
          //$where = str_replace(',', '', $where);
          $where = explode(',', $where);
          $where[0] = $this->sanitize($where[0]);
          $where[2] = $this->sanitize($where[2]);
          $where = implode(' ', $where);
        } */
        $where = $this->formatWhere($where);
        $where = $close ? " AND ($where)" : " AND ($where";
        $this->composeSentence($where);
      }
      return $this;
    } catch (\Throwable $th) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($th->getMessage());
      }
      return $this->sendError('Error en la sub consulta AND');
    }
  }

  protected function subOr(?string $where, ?bool $close = false): mixed
  {
    try {
      if ($where) {
        /* if (sizeof(explode(',', $where)) === 1) {
          $where = $this->sanitize($where);
          $where = "$where = $where";
        } elseif (sizeof(explode(',', $where)) === 2) {
          $where = explode(',', $where);
          $where = implode(' = ', array_map(function ($v) {
            return $this->sanitize($v);
          }, $where));
        } else {
          //$where = str_replace(',', '', $where);
          $where = explode(',', $where);
          $where[0] = $this->sanitize($where[0]);
          $where[2] = $this->sanitize($where[2]);
          $where = implode(' ', $where);
        } */
        $where = $this->formatWhere($where);
        $where = $close ? " OR ($where)" : " OR ($where";
        $this->composeSentence($where);
      }
      return $this;
    } catch (\Throwable $th) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($th->getMessage());
      }
      return $this->sendError('Error en la sub consulta OR');
    }
  }

  public function __destruct()
  {
    parent::__destruct();
    $this->table = null;
    $this->sentence = null;
    $this->db = null;
  }
}