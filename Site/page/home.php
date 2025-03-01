<section class="hero-section">
  <div class="hero-container">
    <div class="hero-image">
      <img src="img/backpack.png" alt="Creative Backpack">
    </div>

    <div class="hero-content">
      <h4 class="hero-subtitle">Creative bag only for you.</h4>
      <h2 class="hero-title">Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod.</h2>
      <p class="hero-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
      <button class="cta-button" onclick="window.location.href='?page=product'">See more</button>
    </div>
  </div>
</section>


<section class="products-section">
<div class="section-header">
  <h2 class="section-title">Our available product</h2>
  <p class="section-subtitle">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore.</p>
</div>

<div class="products-grid home">
  <?php
  $backpacks = include 'api/fetch_backpacks.php';
  if (!$backpacks) {
      die('Ошибка: данные не получены. Проверьте путь к fetch_backpacks.php.');
  }

  // Ограничиваем количество элементов до 8
  $limitedBackpacks = array_slice($backpacks, 0, 8);

  foreach ($limitedBackpacks as $backpack) {
      echo '<article class="product-card home">';
      echo '<img class="product-image home" src="' . htmlspecialchars($backpack['MainPhoto']) . '"
           alt="' . htmlspecialchars($backpack['Title']) . '">';
      echo '</article>';
  }
  ?>
</div>

<button class="load-more" onclick="window.location.href='?page=product'">See More →</button>
</section>
