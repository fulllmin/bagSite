<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Сайт, сайтушечка альтушечка">
    <meta name="keywords" content="Сайт, рюкзаки, бэкпэк, чпэкскрэкс">
    <meta name="author" content="Алексей Короткин">
    <title>Bag</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
  <?php include 'include/header.html'; ?>

  <?php
  //загрузка страниц
  $allowed_pages = ['home', 'product', 'cart'];
  $page = isset($_GET['page']) && in_array($_GET['page'], $allowed_pages)
          ? $_GET['page']
          : 'home';

  include "page/{$page}.php";
  ?>

  <?php include 'include/footer.html'; ?>

  <script src="js/script.js"></script>
  <script src="js/cart.js"></script>
</body>
</html>
