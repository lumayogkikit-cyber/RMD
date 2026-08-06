<?php

$host = getenv('DB_HOST') ?: getenv('DATABASE_HOST');
$port = getenv('DB_PORT') ?: 3306;
$db = getenv('DB_DATABASE') ?: getenv('DATABASE_NAME');
$user = getenv('DB_USERNAME') ?: getenv('DATABASE_USERNAME');
$pass = getenv('DB_PASSWORD') ?: getenv('DATABASE_PASSWORD');

echo "DB_HOST={$host}\n";
echo "DB_PORT={$port}\n";
echo "DB_DATABASE={$db}\n";
echo "DB_USERNAME={$user}\n";
echo "DB_PASSWORD=" . ($pass === false ? '(unset)' : ($pass === '' ? '(empty)' : 'set')) . "\n";

try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db);
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $count = $pdo->query('select count(*) from users')->fetchColumn();
    echo "USER_COUNT={$count}\n";
    $rows = $pdo->query('select id, email, role, status from users order by id asc limit 10');
    foreach ($rows as $row) {
        echo implode(',', $row) . "\n";
    }
} catch (Throwable $e) {
    echo 'ERROR=' . $e->getMessage() . "\n";
}
