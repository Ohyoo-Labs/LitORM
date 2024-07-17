<?php
/* 
 * ORM class for MySql Databases
 */
namespace LitOrm;

require_once './litautoload.php';
use LitOrm\LitRawFunctions;

class LitFunctions extends LitRawFunctions
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

  public function isOnlySelect(string $string): bool
  {
    // Quitar espacios en blanco al principio y al final y convertir la cadena a minúsculas
    $onlySelect = trim(strtolower($string));
    // Verificar si la cadena resultante es exactamente "select"
    return $onlySelect === 'select';
  }

  // Verificar si la ultima instruccion al final de la sentencia es HAVING o no
  public function isHaving(string $string): bool
  {
    // Eliminar espacios en blanco al final de la cadena
    $string = rtrim($string);
    // Dividir la cadena en palabras usando espacios en blanco como delimitador
    $words = explode(' ', $string);
    // Obtener la última palabra y convertirla a minúsculas
    $lastWord = strtolower(end($words));
    // Verificar si la última palabra es exactamente "having"
    return $lastWord === 'having';
  }

  // Función para cambiar el valor de la propiedad table
  protected function target(string $table): mixed
  {
    $this->table = $table;
    return $this;
  }

  // Función para contar registros
  protected function count(string $field, ?string $as = null): mixed
  {
    try {
      if ($field) {
        $count = "";
        if (!$this->isOnlySelect($this->sentence) && !$this->isHaving($this->sentence))
          $count = ",";
        if (!$field || $field == "") {
          $field = "*";
        } else {
          $field = explode(",", $field);
          if (sizeof($field) === 1) {
            $field = $this->sanitize($field[0]);
            $field = rtrim($field, ",");
            $field = " COUNT({$field})";
          } elseif (sizeof($field) === 2) {
            $field[0] = $this->sanitize($field[0]);
            $field[1] = $this->sanitize($field[1]);
            $field = " COUNT({$field[0]}) AS {$field[1]}";
          } elseif (sizeof($field) === 3) {
            $field[0] = $this->sanitize($field[0]);
            $field[2] = $this->sanitize($field[2]);
            $field = " COUNT({$field[0]}) {$field[1]} {$field[2]}";
          } elseif (sizeof($field) === 4) {
            $field[0] = $this->sanitize($field[0]);
            $field[2] = $this->sanitize($field[2]);
            $field[3] = $this->sanitize($field[3]);
            $field = " COUNT({$field[0]}) {$field[1]} {$field[2]} AS {$field[3]}";
          }
        }
        $count .= $field;
        $this->composeSentence($count);
      }
      return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta COUNT');
    }
  }

  // Función para sumar valores de una columna
  protected function sum(?string $field = ''): mixed
  {
    try {
      if ($field) {
        $sum = "";
        if (!$this->isOnlySelect($this->sentence) && !$this->isHaving($this->sentence))
          $sum = ",";
        if (!$field || $field == "") {
          $field = "*";
        } else {
          $field = explode(",", $field);
          if (sizeof($field) === 1) {
            $field = $this->sanitize($field[0]);
            $field = rtrim($field, ",");
            $field = " SUM({$field})";
          } elseif (sizeof($field) === 2) {
            $field[0] = $this->sanitize($field[0]);
            $field[1] = $this->sanitize($field[1]);
            $field = " SUM({$field[0]}) AS {$field[1]}";
          } elseif (sizeof($field) === 3) {
            $field[0] = $this->sanitize($field[0]);
            $field[2] = $this->sanitize($field[2]);
            $field = " SUM({$field[0]}) {$field[1]} {$field[2]}";
          } elseif (sizeof($field) === 4) {
            $field[0] = $this->sanitize($field[0]);
            $field[2] = $this->sanitize($field[2]);
            $field[3] = $this->sanitize($field[3]);
            $field = " SUM({$field[0]}) {$field[1]} {$field[2]} AS {$field[3]}";
          }
        }
        $sum .= $field;
        $this->composeSentence($sum);
      }
      return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta SUM');
    }
  }

  // Función para calcular el promedio de valores de una columna
  protected function avg(?string $field = ''): mixed
  {
    try {
      if ($field) {
        $avg = "";
        if (!$this->isOnlySelect($this->sentence) && !$this->isHaving($this->sentence))
          $avg = ",";
        if (!$field || $field == "") {
          $field = "*";
        } else {
          $field = explode(",", $field);
          if (sizeof($field) === 1) {
            $field = $this->sanitize($field[0]);
            $field = rtrim($field, ",");
            $field = " AVG({$field})";
          } elseif (sizeof($field) === 2) {
            $field[0] = $this->sanitize($field[0]);
            $field[1] = $this->sanitize($field[1]);
            $field = " AVG({$field[0]}) AS {$field[1]}";
          } elseif (sizeof($field) === 3) {
            $field[0] = $this->sanitize($field[0]);
            $field[2] = $this->sanitize($field[2]);
            $field = " AVG({$field[0]}) {$field[1]} {$field[2]}";
          } elseif (sizeof($field) === 4) {
            $field[0] = $this->sanitize($field[0]);
            $field[2] = $this->sanitize($field[2]);
            $field[3] = $this->sanitize($field[3]);
            $field = " AVG({$field[0]}) {$field[1]} {$field[2]} AS {$field[3]}";
          }
        }
        $avg .= $field;
        $this->composeSentence($avg);
      }
      return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta AVG');
    }

  }
  // Función para obtener el valor máximo de una columna
  protected function max(?string $field = ''): mixed
  {
    try {
      if ($field) {
        $max = "";
        if (!$this->isOnlySelect($this->sentence) && !$this->isHaving($this->sentence))
          $max = ",";
        if (!$field || $field == "") {
          $field = "*";
        } else {
          $field = explode(",", $field);
          if (sizeof($field) === 1) {
            $field = $this->sanitize($field[0]);
            $field = rtrim($field, ",");
            $field = " MAX({$field})";
          } elseif (sizeof($field) === 2) {
            $field[0] = $this->sanitize($field[0]);
            $field[1] = $this->sanitize($field[1]);
            $field = " MAX({$field[0]}) AS {$field[1]}";
          } elseif (sizeof($field) === 3) {
            $field[0] = $this->sanitize($field[0]);
            $field[2] = $this->sanitize($field[2]);
            $field = " MAX({$field[0]}) {$field[1]} {$field[2]}";
          } elseif (sizeof($field) === 4) {
            $field[0] = $this->sanitize($field[0]);
            $field[2] = $this->sanitize($field[2]);
            $field[3] = $this->sanitize($field[3]);
            $field = " MAX({$field[0]}) {$field[1]} {$field[2]} AS {$field[3]}";
          }
        }
        $max .= $field;
        $this->composeSentence($max);
      }
      return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta MAX');
    }
  }
  // Función para obtener el valor mínimo de una columna
  protected function min(?string $field = ''): mixed
  {
    try {
      if ($field) {
        $min = "";
        if (!$this->isOnlySelect($this->sentence) && !$this->isHaving($this->sentence))
          $min = ",";
        if (!$field || $field == "") {
          $field = "*";
        } else {
          $field = explode(",", $field);
          if (sizeof($field) === 1) {
            $field = $this->sanitize($field[0]);
            $field = rtrim($field, ",");
            $field = " MIN({$field})";
          } elseif (sizeof($field) === 2) {
            $field[0] = $this->sanitize($field[0]);
            $field[1] = $this->sanitize($field[1]);
            $field = " MIN({$field[0]}) AS {$field[1]}";
          } elseif (sizeof($field) === 3) {
            $field[0] = $this->sanitize($field[0]);
            $field[2] = $this->sanitize($field[2]);
            $field = " MIN({$field[0]}) {$field[1]} {$field[2]}";
          } elseif (sizeof($field) === 4) {
            $field[0] = $this->sanitize($field[0]);
            $field[2] = $this->sanitize($field[2]);
            $field[3] = $this->sanitize($field[3]);
            $field = " MIN({$field[0]}) {$field[1]} {$field[2]} AS {$field[3]}";
          }
        }
        $min .= $field;
        $this->composeSentence($min);
      }
      return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la consulta MIN');
    }
  }

  public function __destruct()
  {
    $this->db = null;
    $this->sentence = null;
    $this->table = null;
    $this->isTransact = null;
    $this->params = null;
  }
}
