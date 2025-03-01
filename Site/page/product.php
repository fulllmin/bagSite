<?php
// Параметры фильтрации
$filterParams = [
    'category' => $_GET['category'] ?? null,
    'min_price' => $_GET['min_price'] ?? null,
    'max_price' => $_GET['max_price'] ?? null,
    'sort' => $_GET['sort'] ?? 'newest',
    'limit' => 12,
    'offset' => isset($_GET['p']) ? ($_GET['p'] - 1) * 12 : 0
];

// Получение данных
$backpacks = include 'api/fetch_backpacks.php';
if (!is_array($backpacks)) {
    die("Ошибка: данные не получены или получены в неверном формате.");
}

$categories = include 'api/get_categories.php';
$totalItems = include 'api/count_backpacks.php';
$totalPages = ceil($totalItems / $filterParams['limit']);

// Генерация URL с параметрами
function buildUrl($params) {
    $currentParams = $_GET;
    unset($currentParams['p']); // Удаляем старый номер страницы
    $mergedParams = array_merge(
  array_map('htmlspecialchars', $currentParams),
  array_map('htmlspecialchars', $params)
);
    $query = http_build_query(array_filter($mergedParams));
    return '?' . $query;
}
?>

<section class="products-section">
  <div class="section-header">
    <h1 class="section-title">Наши продукты</h1>
    <div class="filters">
      <button class="filter-toggle">Фильтры ▼</button>
      <div class="filter-menu">
        <form id="filter-form" method="get" action="">
          <input type="hidden" name="page" value="product">

          <div class="filter-group">
            <label>Категория:</label>
            <select name="category" class="filter-select">
              <option value="">Все</option>
              <?php foreach($categories as $cat): ?>
                <option value="<?= $cat ?>" <?= $filterParams['category'] === $cat ? 'selected' : '' ?>>
                  <?= $cat ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="filter-group">
            <label>Цена (руб.):</label>
            <div class="price-range">
              <input type="number" name="min_price"
                     placeholder="От"
                     value="<?= $filterParams['min_price'] ?>"
                     min="0">
              <span class="range-separator">-</span>
              <input type="number" name="max_price"
                     placeholder="До"
                     value="<?= $filterParams['max_price'] ?>"
                     min="0">
            </div>
          </div>

          <div class="filter-group">
            <label>Сортировка:</label>
            <select name="sort" class="sort-select">
              <option value="price_asc" <?= $filterParams['sort'] === 'price_asc' ? 'selected' : '' ?>>
                По возрастанию цены
              </option>
              <option value="price_desc" <?= $filterParams['sort'] === 'price_desc' ? 'selected' : '' ?>>
                По убыванию цены
              </option>
              <option value="newest" <?= $filterParams['sort'] === 'newest' ? 'selected' : '' ?>>
                Новинки
              </option>
            </select>
          </div>

          <div class="filter-actions">
            <button type="submit" class="apply-filters">Применить</button>
            <a href="?page=product" class="reset-filters">Сбросить</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="products-grid">
    <?php if (!empty($backpacks)): ?>
      <?php foreach ($backpacks as $backpack): ?>
        <article class="product-card">
          <div class="product-image-container">
            <img class="product-image"
                 src="<?= htmlspecialchars($backpack['MainPhoto']) ?>"
                 alt="<?= htmlspecialchars($backpack['Title']) ?>"
                 loading="lazy">

          </div>

          <div class="product-info">
            <h3 class="product-title"><?= htmlspecialchars($backpack['Title']) ?></h3>
            <p class="product-category"><?= htmlspecialchars($backpack['Category']) ?></p>

            <div class="price-block">

              <div class="current-price">
                <?= number_format($backpack['Price'], 0, '', ' ') ?> руб.
              </div>
            </div>

            <div class="product-actions">
              <button class="add-to-cart"
                  data-product-id="<?= $backpack['id'] ?>"
                  data-product-title="<?= $backpack['Title'] ?>"
                  data-product-price="<?= $backpack['Price'] ?>"
                  data-product-image="<?= $backpack['MainPhoto'] ?>">
                  <i class="cart-icon"></i>В корзину
              </button>


              <button class="quick-view" data-product-id="<?= $backpack['id'] ?>">
                Быстрый просмотр
              </button>

            </div>
          </div>
        </article>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="no-results">
        <img src="/img/empty-state.svg" alt="Товары не найдены">
        <p>По вашему запросу ничего не найдено</p>
        <a href="?page=product" class="reset-link">Сбросить фильтры</a>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="<?= buildUrl(['p' => $i] + $_GET) ?>"
           class="<?= isset($_GET['p']) && $i == $_GET['p'] ? 'active' : '' ?>"
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</section>
