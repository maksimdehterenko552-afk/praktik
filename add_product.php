<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить товар - АИС Оптовый склад</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .menu { background: #0066cc; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .menu a { color: white; text-decoration: none; margin-right: 20px; font-weight: bold; }
        .menu a:hover { text-decoration: underline; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #0066cc; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0055aa; }
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>➕ Добавить товар</h1>
        
        <div class="menu">
            <a href="index.php">📦 Склад</a>
            <a href="add_product.php">➕ Добавить товар</a>
            <a href="sell_product.php">💰 Продать товар</a>
            <a href="operations.php">📋 Журнал операций</a>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $quantity = $_POST['quantity'] ?? 0;
            $price = $_POST['price'] ?? 0;
            
            if (!empty($name) && $quantity > 0 && $price > 0) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO products (name, quantity, price) VALUES (?, ?, ?)");
                    $stmt->execute([$name, $quantity, $price]);
                    
                    echo '<div class="message success">Товар успешно добавлен!</div>';
                } catch(PDOException $e) {
                    echo '<div class="message error">Ошибка: ' . $e->getMessage() . '</div>';
                }
            } else {
                echo '<div class="message error">Заполните все поля корректно!</div>';
            }
        }
        ?>

        <form method="POST">
            <div class="form-group">
                <label for="name">Наименование товара:</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="quantity">Количество:</label>
                <input type="number" id="quantity" name="quantity" min="1" required>
            </div>
            
            <div class="form-group">
                <label for="price">Цена (руб.):</label>
                <input type="number" id="price" name="price" min="0.01" step="0.01" required>
            </div>
            
            <button type="submit">Добавить товар</button>
        </form>
    </div>
</body>
</html>