<?php
require_once 'db.php';

function countBackpacks($params = []) {
    $pdo = getDatabaseConnection();

    $sql = "SELECT COUNT(*) as total FROM backpacks WHERE 1=1";
    $bindings = [];

    if (!empty($params['category'])) {
        $sql .= " AND Category = ?";
        $bindings[] = $params['category'];
    }

    if (isset($params['min_price'])) {
        $sql .= " AND Price >= ?";
        $bindings[] = (float)$params['min_price'];
    }

    if (isset($params['max_price'])) {
        $sql .= " AND Price <= ?";
        $bindings[] = (float)$params['max_price'];
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($bindings);
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Count query failed: " . $e->getMessage());
        return 0;
    }
}

// Возвращаем количество товаров
return countBackpacks($_GET);
?>
