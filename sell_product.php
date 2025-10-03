<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Продать товар - АИС Оптовый склад</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .menu { background: #0066cc; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .menu a { color: white; text-decoration: none; margin-right: 20px; font-weight: bold; }
        .menu a:hover { text-decoration: underline; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input[type="number"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #0066cc; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0055aa; }
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>💰 Продать товар</h1>
        
        <div class="menu">
            <a href="index.php">📦 Склад</a>
            <a href="add_product.php">➕ Добавить товар</a>
            <a href="sell_product.php">💰 Продать товар</a>
            <a href="operations.php">📋 Журнал операций</a>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $product_id = $_POST['product_id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 0;
            
            if ($product_id > 0 && $quantity > 0) {
                try {
                    // Проверяем наличие товара
                    $stmt = $pdo->prepare("SELECT quantity, price FROM products WHERE id = ?");
                    $stmt->execute([$product_id]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($product && $product['quantity'] >= $quantity) {
                        // Обновляем количество
                        $new_quantity = $product['quantity'] - $quantity;
                        $stmt = $pdo->prepare("UPDATE products SET quantity = ? WHERE id = ?");
                        $stmt->execute([$new_quantity, $product_id]);
                        
                        // Записываем операцию (исправленный запрос)
                        $total_amount = $quantity * $product['price'];
                        
                        // Сначала проверим структуру таблицы operations
                        $stmt = $pdo->prepare("SHOW COLUMNS FROM operations LIKE 'quantity'");
                        $stmt->execute();
                        $column_exists = $stmt->fetch();
                        
                        if ($column_exists) {
                            // Если колонка quantity существует
                            $stmt = $pdo->prepare("INSERT INTO operations (product_id, operation_type, quantity, price, total_amount) VALUES (?, 'sale', ?, ?, ?)");
                            $stmt->execute([$product_id, $quantity, $product['price'], $total_amount]);
                        } else {
                            // Если колонки quantity нет - используем альтернативный запрос
                            $stmt = $pdo->prepare("INSERT INTO operations (product_id, operation_type, price, total_amount) VALUES (?, 'sale', ?, ?)");
                            $stmt->execute([$product_id, $product['price'], $total_amount]);
                        }
                        
                        echo '<div class="message success">Товар продан успешно! Выручка: ' . number_format($total_amount, 2) . ' руб.</div>';
                    } else {
                        echo '<div class="message error">Недостаточно товара на складе!</div>';
                    }
                } catch(PDOException $e) {
                    echo '<div class="message error">Ошибка: ' . $e->getMessage() . '</div>';
                }
            } else {
                echo '<div class="message error">Заполните все поля корректно!</div>';
            }
        }
        
        // Получаем список товаров
        $products = $pdo->query("SELECT id, name, quantity FROM products WHERE quantity > 0 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if (count($products) > 0): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="product_id">Выберите товар:</label>
                    <select id="product_id" name="product_id" required>
                        <option value="">-- Выберите товар --</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= $product['id'] ?>">
                                <?= $product['name'] ?> (остаток: <?= $product['quantity'] ?> шт.)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="quantity">Количество для продажи:</label>
                    <input type="number" id="quantity" name="quantity" min="1" required>
                </div>
                
                <button type="submit">Продать</button>
            </form>
        <?php else: ?>
            <p>Нет товаров для продажи</p>
        <?php endif; ?>
    </div>
</body>
</html>