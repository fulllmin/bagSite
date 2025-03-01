<?php
require_once 'db.php';

function fetchBackpacks($params = []) {
    $pdo = getDatabaseConnection();

    $sql = "SELECT * FROM backpacks WHERE 1=1";
    $bindings = [];

    // Фильтр по категории
    if (!empty($params['category'])) {
        $sql .= " AND Category = ?";
        $bindings[] = $params['category'];
    }

    // Фильтр по цене
    if (!empty($params['min_price'])) {
        $sql .= " AND Price >= ?";
        $bindings[] = (float)$params['min_price'];
    }

    if (!empty($params['max_price'])) {
        $sql .= " AND Price <= ?";
        $bindings[] = (float)$params['max_price'];
    }

    // Сортировка
    $sortOptions = [
        'price_asc' => 'Price ASC',
        'price_desc' => 'Price DESC',
        'newest' => 'id DESC' // Если нет created_at, сортируем по id
    ];
    $sort = $sortOptions[$params['sort'] ?? 'newest'] ?? 'id DESC';
    $sql .= " ORDER BY $sort";

    // Пагинация
    if (isset($params['limit'])) {
        $sql .= " LIMIT ? OFFSET ?";
        $bindings[] = (int)$params['limit'];
        $bindings[] = (int)($params['offset'] ?? 0);
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Ошибка запроса: " . $e->getMessage());
        return [];
    }
}

// Возвращаем данные
return fetchBackpacks($_GET);
?>
