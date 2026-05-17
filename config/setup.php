<?php

declare(strict_types=1);

require __DIR__ . "/../autoload.php";

use App\Core\EnvCore;

EnvCore::load(__DIR__ . "/../.env");

$host = EnvCore::get("DB_HOST", "127.0.0.1");
$port = (int) EnvCore::get("DB_PORT", "3306");
$name = EnvCore::get("DB_NAME", "");
$user = EnvCore::get("DB_USER", "");
$pass = EnvCore::get("DB_PASS", "");

if ($name === "" || $user === "") {
    fwrite(STDERR, "ERROR: DB_NAME and DB_USER must be set in .env\n");
    exit(1);
}

try {
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$name}`");

    $sql = file_get_contents(__DIR__ . "/../db/init.sql");
    if ($sql === false) {
        throw new RuntimeException("Cannot read db/init.sql");
    }
    $pdo->exec($sql);

    echo "OK: database `{$name}` is ready (tables: users, images, comments, likes).\n";
} catch (Throwable $e) {
    fwrite(STDERR, "SETUP ERROR: " . $e->getMessage() . "\n");
    exit(1);
}
