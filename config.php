<?php
$host = 'localhost';
$dbname = 'warehouse_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Проверяем подключение
    echo "<!-- База данных подключена успешно -->";
} catch(PDOException $e) {
    die("<div style='color: red; padding: 20px; border: 2px solid red;'>
        <h3>Ошибка подключения к базе данных:</h3>
        <p><strong>" . $e->getMessage() . "</strong></p>
        <p>Проверьте:</p>
        <ul>
            <li>Запущен ли MySQL в XAMPP</li>
            <li>Существует ли база 'warehouse_db'</li>
            <li>Созданы ли таблицы products и operations</li>
        </ul>
    </div>");
}
?>