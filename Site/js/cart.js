class Cart {
  constructor() {
    this.key = 'cart';
    this.items = JSON.parse(localStorage.getItem(this.key)) || [];
  }

  save() {
    localStorage.setItem(this.key, JSON.stringify(this.items));
  }

  addItem(product) {
    const existing = this.items.find(item => item.id === product.id);
    if (existing) {
      existing.quantity++;
    } else {
      this.items.push({...product, quantity: 1});
    }
    this.save();
  }

  removeItem(id) {
    this.items = this.items.filter(item => item.id !== id);
    this.save();
  }

  updateQuantity(id, newQuantity) {
  const itemIndex = this.items.findIndex(item => item.id === id);
  if (itemIndex === -1) return;

  if (newQuantity <= 0) {
    this.items.splice(itemIndex, 1);
  } else {
    this.items[itemIndex].quantity = newQuantity;
  }
  this.save();
}

  getTotal() {
    return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  }

  clear() {
    this.items = [];
    this.save();
  }

  updateCartCounter() {
    const counter = document.getElementById('cartCounter');
    if (counter) {
      counter.textContent = this.items.reduce((sum, item) => sum + item.quantity, 0);
    }
  }
}

const cart = new Cart();


// Отрисовка корзины
function renderCart() {
  cart.items = JSON.parse(localStorage.getItem('cart')) || [];
  // Нормализуем цены для каждого элемента
cart.items = cart.items.map(item => {
  // Проверяем наличие свойства price
  if (item && typeof item.price !== 'undefined') {
    return {
      ...item,
      price: normalizePrice(item.price)
    };
  }
  return item;
});

  const container = document.getElementById('cartItems');
  const totalElement = document.getElementById('totalAmount');

  const checkoutBtn = document.getElementById('checkoutBtn');

  if (cart.items.length === 0) {
    checkoutBtn.disabled = true;
    container.innerHTML = `
      <div class="empty-cart">
        <p>Ваша корзина пуста</p>
      </div>
    `;
    totalElement.textContent = '0';
    return;
  }

  checkoutBtn.disabled = false;

  container.innerHTML = cart.items.map(item => `
    <div class="cart-item">
      <img class="cart-item-image" src="${item.MainPhoto}" alt="${item.Title}">
      <div class="cart-item-info">
        <h3>${item.Title}</h3>
        <p>Цена: ${Number(normalizePrice(item.price)).toLocaleString()} руб.</p>
        <div class="quantity-controls">
          <button class="quantity-btn minus" data-id="${item.id}">-</button>
          <span>${item.quantity}</span>
          <button class="quantity-btn plus" data-id="${item.id}">+</button>
        </div>
        <button class="remove-btn" data-id="${item.id}">Удалить</button>
      </div>
    </div>
  `).join('');
  totalElement.textContent = cart.getTotal().toLocaleString();

  // Добавляем обработчики
  document.querySelectorAll('.quantity-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const id = Number(this.dataset.id);
      const item = cart.items.find(item => item.id == id);
      if (this.classList.contains('plus')) {
        cart.updateQuantity(id, item.quantity + 1);
      } else {
        cart.updateQuantity(id, item.quantity - 1);
      }
      renderCart();
    });
  });

  document.querySelectorAll('.remove-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      cart.removeItem(Number(this.dataset.id));
      renderCart();
    });
  });

  cart.updateCartCounter();
}

function normalizePrice(price) {
  if (typeof price === 'string') {
    let cleaned = price
      .replace(/\s/g, '')
      .replace(/\..*/, '');
    return Number(cleaned);
  }
  return price;
}

// Функция для добавления товара в корзину
function setupAddToCart(modal, product) {
  const mappedProduct = {
    id: product.id,
    Title: product.title,
    price: product.price,
    MainPhoto: product.photos[0]
  };

  modal.querySelector('.add-to-cart').addEventListener('click', () => {
      if (mappedProduct.id && mappedProduct.price) {
        cart.addItem(mappedProduct);
        cart.updateCartCounter();
        modal.remove();
        alert('Товар добавлен в корзину');
      } else {
        alert('Ошибка: данные товара некорректны');
      }
    });
}

function initCart() {
document.querySelectorAll('.add-to-cart').forEach(btn => {
  btn.addEventListener('click', function() {
    const product = {
      id: Number(this.dataset.productId),
      Title: this.dataset.productTitle,
      price: Number(this.dataset.productPrice),
      MainPhoto: this.dataset.productImage
    };
    cart.addItem(product);
    cart.updateCartCounter();
    alert('Товар добавлен в корзину');
  });
});
}

document.getElementById('checkoutBtn')?.addEventListener('click', () => {
  cart.clear();
  alert('Спасибо за покупку! Корзина очищена.');
  renderCart();
});


if (window.location.search.includes('page=cart')) {
  renderCart();
}

initCart();
cart.updateCartCounter();
