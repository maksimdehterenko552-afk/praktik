<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>АИС Оптовый склад</title>
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
        .stats { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏢 АИС "Оптовый склад"</h1>
        
        <div class="menu">
            <a href="index.php">📦 Склад</a>
            <a href="add_product.php">➕ Добавить товар</a>
            <a href="sell_product.php">💰 Продать товар</a>
            <a href="operations.php">📋 Журнал операций</a>
        </div>

        <div class="stats">
            <h3>📊 Общая статистика:</h3>
            <?php
            $total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
            $total_quantity = $pdo->query("SELECT SUM(quantity) FROM products")->fetchColumn();
            echo "<p>Всего товаров: <strong>$total_products</strong></p>";
            echo "<p>Общее количество: <strong>$total_quantity</strong> единиц</p>";
            ?>
        </div>

        <h2>📦 Текущие остатки на складе</h2>
        
        <?php
        try {
            $stmt = $pdo->query("SELECT * FROM products ORDER BY name");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($products) > 0) {
                echo '<table>';
                echo '<tr><th>ID</th><th>Наименование</th><th>Количество</th><th>Цена</th><th>Общая стоимость</th><th>Дата добавления</th></tr>';
                
                foreach ($products as $product) {
                    $total_cost = $product['quantity'] * $product['price'];
                    echo '<tr>';
                    echo '<td>' . $product['id'] . '</td>';
                    echo '<td><strong>' . $product['name'] . '</strong></td>';
                    echo '<td>' . $product['quantity'] . ' шт.</td>';
                    echo '<td>' . number_format($product['price'], 2) . ' руб.</td>';
                    echo '<td>' . number_format($total_cost, 2) . ' руб.</td>';
                    echo '<td>' . $product['created_at'] . '</td>';
                    echo '</tr>';
                }
                
                echo '</table>';
            } else {
                echo '<p>Склад пуст</p>';
            }
        } catch(PDOException $e) {
            echo "Ошибка: " . $e->getMessage();
        }
        ?>
    </div>
</body>
</html>