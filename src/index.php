<?php
$host = getenv('DB_HOST');       // RDS endpoint
$db = getenv('DB_NAME');         // Database name
$user = getenv('DB_USER');       // Username
$pass = getenv('DB_PASS');       // Password
$charset = 'utf8mb4';

// Connection string
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Check if table exists, create if not
    $pdo->exec("CREATE TABLE IF NOT EXISTS visits (
        id INT PRIMARY KEY AUTO_INCREMENT,
        count INT NOT NULL
    )");

    // Get current count or initialize
    $stmt = $pdo->query("SELECT * FROM visits LIMIT 1");
    $row = $stmt->fetch();

    if (!$row) {
        $count = 1;
        $pdo->exec("INSERT INTO visits (count) VALUES (1)");
    } else {
        $count = $row['count'] + 1;
        $pdo->prepare("UPDATE visits SET count = ? WHERE id = ?")
            ->execute([$count, $row['id']]);
    }

    echo "<h1>Visit count: $count</h1>";

} catch (PDOException $e) {
    echo "<h2>Database Error:</h2><pre>" . $e->getMessage() . "</pre>";
}
?>
