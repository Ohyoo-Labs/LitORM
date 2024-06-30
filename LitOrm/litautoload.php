<?php 
// autoload.php

spl_autoload_register(function ($className) {
  // Directorio raíz donde se encuentran las clases de LitORM
  $litOrmDir = __DIR__ . '/LitORM/';

  // Convertir el nombre de la clase en la ruta del archivo
  $classFile = $litOrmDir . str_replace('\\', '/', $className) . '.php';

  // Verificar si el archivo de la clase existe y cargarlo
  if (file_exists($classFile)) {
      require_once $classFile;
      return;
  }

  // Si el archivo no se encuentra, buscar en subdirectorios recursivamente
  $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($litOrmDir));
  foreach ($iterator as $file) {
      if ($file->isDir()) {
          continue; // Saltar directorios
      }

      // Obtener el nombre del archivo y comparar con el nombre de la clase
      $filename = $file->getBasename('.php');
      if ($filename === $className) {
          require_once $file->getPathname();
          return;
      }
  }
});
