<?php 
// LitConnection Class

namespace LitORM\Boostrap;

use PDO;
use PDOException;

class LitConnection {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $charset;
    private $pdo;

    public function __construct($host, $dbname, $username, $password, $charset = 'utf8mb4') {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
        $this->charset = $charset;
    }

    public function connect() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            // Configurar el modo de error de PDO para lanzar excepciones
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new PDOException("Error al conectar a la base de datos: " . $e->getMessage());
        }

        return $this->pdo;
    }

    public function getPdo() {
        // Devolver la instancia de PDO establecida
        return $this->pdo;
    }

    public function __destruct() {
        // Cerrar la conexión PDO al destruir el objeto
        $this->pdo = null;
    }
}
