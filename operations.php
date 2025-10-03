<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Журнал операций - АИС Оптовый склад</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .menu { background: #0066cc; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .menu a { color: white; text-decoration: none; margin-right: 20px; font-weight: bold; }
        .menu a:hover { text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #0066cc; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .sale { color: #d9534f; }
        .purchase { color: #5cb85c; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Журнал операций</h1>
        
        <div class="menu">
            <a href="index.php">📦 Склад</a>
            <a href="add_product.php">➕ Добавить товар</a>
            <a href="sell_product.php">💰 Продать товар</a>
            <a href="operations.php">📋 Журнал операций</a>
        </div>

        <?php
        try {
            $stmt = $pdo->query("
                SELECT o.*, p.name as product_name 
                FROM operations o 
                LEFT JOIN products p ON o.product_id = p.id 
                ORDER BY o.operation_date DESC
            ");
            $operations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($operations) > 0) {
                echo '<table>';
                echo '<tr><th>ID</th><th>Дата</th><th>Товар</th><th>Тип операции</th><th>Количество</th><th>Цена</th><th>Сумма</th></tr>';
                
                foreach ($operations as $operation) {
                    $operation_type = $operation['operation_type'] == 'sale' ? '💰 Продажа' : '📥 Поступление';
                    $class = $operation['operation_type'] == 'sale' ? 'sale' : 'purchase';
                    
                    echo '<tr>';
                    echo '<td>' . $operation['id'] . '</td>';
                    echo '<td>' . $operation['operation_date'] . '</td>';
                    echo '<td>' . $operation['product_name'] . '</td>';
                    echo '<td class="' . $class . '"><strong>' . $operation_type . '</strong></td>';
                    echo '<td>' . $operation['quantity'] . ' шт.</td>';
                    echo '<td>' . number_format($operation['price'], 2) . ' руб.</td>';
                    echo '<td><strong>' . number_format($operation['total_amount'], 2) . ' руб.</strong></td>';
                    echo '</tr>';
                }
                
                echo '</table>';
            } else {
                echo '<p>Операций пока нет</p>';
            }
        } catch(PDOException $e) {
            echo "Ошибка: " . $e->getMessage();
        }
        ?>
    </div>
</body>
</html>