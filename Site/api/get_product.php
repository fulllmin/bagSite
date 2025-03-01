<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
        throw new InvalidArgumentException('Invalid product ID');
    }

    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT * FROM backpacks WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch();

    if (!$product) {
        http_response_code(404);
        die(json_encode(['error' => 'Product not found']));
    }

    $photos = array_values(array_filter([
        $product['MainPhoto'],
        $product['DopPhoto1'],
        $product['DopPhoto2']
    ]));

    echo json_encode([
        'id' => (int)$product['id'],
        'title' => $product['Title'],
        'brand' => $product['Brend'],
        'category' => $product['Category'],
        'price' => $product['Price'],
        'description' => $product['Description'],
        'photos' => $photos,
        'specifications' => json_decode($product['Specs'] ?? '{}', true)
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
}
?>
