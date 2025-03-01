<?php
require_once 'db.php';

function getCategories() {
    $cacheFile = __DIR__ . '/cache/categories.cache';

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 3600) {
        return json_decode(file_get_contents($cacheFile), true);
    }

    $pdo = getDatabaseConnection();
    $stmt = $pdo->query("SELECT DISTINCT Category FROM backpacks ORDER BY Category");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!file_exists(__DIR__ . '/cache')) {
        mkdir(__DIR__ . '/cache', 0755, true);
    }

    file_put_contents($cacheFile, json_encode($categories));
    return $categories;
}

return getCategories();
?>
