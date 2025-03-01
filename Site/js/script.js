document.addEventListener('DOMContentLoaded', function() {
  const urlParams = new URLSearchParams(window.location.search);
  const currentPage = urlParams.get('page') || 'home';

  // Функция для установки активного состояния меню
  function setActiveMenu() {
    document.querySelectorAll('.menu').forEach(link => {
      const pageParam = link.href.split('page=')[1];
      link.classList.toggle('active', pageParam === currentPage);
    });
  }

  // Функция для обработки кликов по меню
  function handleMenuClick(e) {
    if (this.href.includes('page=')) {
      e.preventDefault();
      const page = this.href.split('page=')[1];
      window.history.replaceState(null, null, `?page=${page}`);
      document.location.reload();
    }
  }

  // Функция для переключения фильтров
  function toggleFilters() {
    document.querySelector('.filter-menu').classList.toggle('active');
  }

  // Функция для применения фильтров
  function applyFilters() {
    const params = new URLSearchParams();
    document.querySelectorAll('.filter-select, .sort-select, input').forEach(el => {
      if (el.value) params.append(el.name, el.value);
    });
    window.location.search = params.toString();
  }

  // Функция для быстрого просмотра товара
  async function quickViewHandler() {
    try {
      const productId = this.dataset.productId;
      const response = await fetch(`api/get_product.php?id=${productId}`);
      if (!response.ok) throw new Error('Ошибка загрузки');
      const product = await response.json();

      let currentSlide = 0;

      const modal = createModal(product);
      document.body.appendChild(modal);

      setupSlider(modal, product.photos, currentSlide);
      setupModalClose(modal);
      setupAddToCart(modal, product);

    } catch (error) {
      alert('Не удалось загрузить товар');
    }
  }

  // Функция для создания модального окна
  function createModal(product) {
    const modal = document.createElement('div');
    modal.className = 'quickview-modal active';
    modal.innerHTML = `
      <div class="quickview-content">
        <span class="close-modal">&times;</span>
        <div class="gallery-slider">
          ${product.photos.map((photo, index) => `
            <img class="slider-image ${index === 0 ? 'active' : ''}" src="${photo}" alt="${product.title}">
          `).join('')}
          ${product.photos.length > 1 ? `
            <div class="slider-nav prev"></div>
            <div class="slider-nav next"></div>
          ` : ''}
        </div>
        <div class="product-info">
          <h2>${product.title}</h2>
          <p class="brand">Бренд: ${product.brand}</p>
          <p class="category">Категория: ${product.category}</p>
          <p class="price">${product.price} руб.</p>
          <p class="description">${product.description}</p>
          <button class="add-to-cart"
              data-product-id="${Number(product.id)}"
              data-product-title="${product.title}"
              data-product-price="${Number(product.price)}"
              data-product-image="${product.photos[0]}">
              Добавить в корзину
      </button>
        </div>
      </div>
    `;
    return modal;
  }

  // Функция для настройки слайдера
  function setupSlider(modal, photos, currentSlide) {
    const showSlide = (index) => {
      modal.querySelectorAll('.slider-image').forEach((img, i) => {
        img.classList.toggle('active', i === index);
      });
      modal.querySelectorAll('.slider-dot').forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
      });
    };

    if (photos.length > 1) {
      modal.querySelector('.prev').addEventListener('click', () => {
        currentSlide = (currentSlide - 1 + photos.length) % photos.length;
        showSlide(currentSlide);
      });

      modal.querySelector('.next').addEventListener('click', () => {
        currentSlide = (currentSlide + 1) % photos.length;
        showSlide(currentSlide);
      });

      let touchStartX = 0;
      const slider = modal.querySelector('.gallery-slider');
      slider.addEventListener('touchstart', e => {
        touchStartX = e.touches[0].clientX;
      });

      slider.addEventListener('touchend', e => {
        const touchEndX = e.changedTouches[0].clientX;
        const diff = touchStartX - touchEndX;
        if (Math.abs(diff) > 50) {
          currentSlide = diff > 0
            ? (currentSlide + 1) % photos.length
            : (currentSlide - 1 + photos.length) % photos.length;
          showSlide(currentSlide);
        }
      });

      const dotsContainer = document.createElement('div');
      dotsContainer.className = 'slider-dots';
      photos.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.className = `slider-dot ${index === 0 ? 'active' : ''}`;
        dot.addEventListener('click', () => {
          currentSlide = index;
          showSlide(currentSlide);
        });
        dotsContainer.appendChild(dot);
      });
      modal.querySelector('.gallery-slider').after(dotsContainer);
    }
  }

  // Функция для закрытия модального окна
  function setupModalClose(modal) {
    const closeModal = () => modal.remove();
    modal.querySelector('.close-modal').addEventListener('click', closeModal);
    modal.addEventListener('click', e => e.target === modal && closeModal());
    document.addEventListener('keydown', e => e.key === 'Escape' && closeModal());
  }

  // Инициализация
  setActiveMenu();
  document.querySelectorAll('.menu').forEach(link => link.addEventListener('click', handleMenuClick));
  const filterToggle = document.querySelector('.filter-toggle');
  if (filterToggle) {
      filterToggle.addEventListener('click', toggleFilters);
  }
  const applyfilters = document.querySelector('.apply-filters');
  if (applyfilters) {
      applyfilters.addEventListener('click', applyFilters);
  }
  document.querySelectorAll('.quick-view').forEach(btn => btn.addEventListener('click', quickViewHandler));
  const filterform = document.getElementById('filter-form');
  if (filterform) {
      filterform.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData);
        window.location.search = params.toString();
      });
  }
  const resetfilters = document.querySelector('.reset-filters');
  if (resetfilters) {
      resetfilters.addEventListener('click', function(e) {
        e.preventDefault();
        window.location.search = '?page=product';
      });
  }
  document.addEventListener('click', function (e) {
    let filterMenu = document.querySelector('.filter-menu');
     const isClickInside = filterMenu.contains(e.target) || filterToggle.contains(e.target);
     if (!isClickInside) {
       filterMenu.classList.remove('active');
     }
   });


  const subform = document.getElementById('subformEmail');
  if (subform) {
    const emailInput = subform.querySelector('input[type="email"]');
    const subscribeButton = subform.querySelector('.button_sumbitEmael');
    if (subscribeButton && emailInput) {
      function isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
      }

      subscribeButton.addEventListener('click', function() {
        const email = emailInput.value.trim();

        if (isValidEmail(email)) {
          emailInput.value = '';

          const notification = document.createElement('div');
          notification.textContent = "Спасибо! Мы запомнили ваш email.";
          notification.classList.add('notification');
          document.body.appendChild(notification);

          setTimeout(() => {
            notification.remove();
          }, 2000);

        } else {
          emailInput.classList.add('error');

          setTimeout(() => {
            emailInput.classList.remove('error');
          }, 1500);
        }
      });
    } else {
      console.error("Не найден emailInput или subscribeButton внутри subformEmail.");
    }
  } else {
    console.error("Элемент с id 'subformEmail' не найден.");
  }



});
