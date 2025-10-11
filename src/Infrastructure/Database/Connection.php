<?php
namespace OpenBook\Infrastructure\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                // Ruta relativa al proyecto (funciona en Windows y Linux)
                $databasePath = __DIR__ . '/../../sqlite/database.sqlite';

                // Crear carpeta si no existe
                $dir = dirname($databasePath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                // Crear archivo si no existe
                if (!file_exists($databasePath)) {
                    touch($databasePath);
                }

                self::$instance = new PDO('sqlite:' . $databasePath);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
