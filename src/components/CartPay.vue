<template>
    <div class="shop">
      <!-- Список товарів -->
      <div class="products">
        <div v-for="product in products" :key="product.id" class="product-card">
          <img :src="product.image" class="product-image" :alt="product.name">
          <h3>{{ product.name }}</h3>
          <p>Ціна: {{ product.price }} грн</p>
          <button @click="addToCart(product.id)">Додати в кошик</button>
        </div>
      </div>
  
      <!-- Іконка кошика з кількістю товарів -->
      <div class="cart-icon" @click="toggleCart">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
          <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
        </svg>
        <span class="cart-count">{{ cartTotalItems }}</span>
      </div>
  
      <!-- Випливаючий кошик -->
      <div class="cart-container" :class="{ 'cart-open': isCartOpen }">
        <div class="cart-overlay" @click="toggleCart"></div>
        <div class="cart-panel">
          <div class="cart-header">
            <h2>Кошик</h2>
            <button class="close-btn" @click="toggleCart">×</button>
          </div>
          
          <div v-if="cart.length === 0" class="empty-cart">Кошик порожній</div>
          
          <div v-else>
            <div v-for="item in cart" :key="item.id" class="cart-item">
              <div class="item-info">
                <h4>{{ item.name }}</h4>
                <p>{{ item.price }} грн/шт</p>
              </div>
              <div class="item-controls">
                <div class="quantity-controls">
                  <button @click.stop="updateQuantity(item.id, -1)">−</button>
                  <span>{{ item.quantity }}</span>
                  <button @click.stop="updateQuantity(item.id, 1)">+</button>
                </div>
                <button class="remove-btn" @click.stop="removeItem(item.id)">Видалити</button>
              </div>
            </div>
            <div class="cart-footer">
              <p class="total-price">Загальна сума: {{ total }} грн</p>
              <button class="checkout-btn" @click="checkout">Оформити замовлення</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        products: [
          { id: 1, name: 'Автомат АК-47', price: 15000, image: require('@/assets/ak-47.jpg') },
          { id: 2, name: 'Пістолет Glock 17', price: 7000, image: require('@/assets/glock17.png') },
          { id: 3, name: 'Снайперська гвинтівка Dragunov', price: 22000, image: require('@/assets/dragunov.jpg') }
        ],
        cart: JSON.parse(localStorage.getItem('cart')) || [],
        isCartOpen: false
      };
    },
    computed: {
      total() {
        return this.cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
      },
      cartTotalItems() {
        return this.cart.reduce((sum, item) => sum + item.quantity, 0);
      }
    },
    methods: {
      // Всі методи залишаються незмінними як у вашому оригінальному коді
      addToCart(productId) {
        const product = this.products.find(p => p.id === productId);
        const existingItem = this.cart.find(item => item.id === productId);
  
        if (existingItem) {
          existingItem.quantity++;
        } else {
          this.cart.push({ ...product, quantity: 1 });
        }
        this.saveCart();
      },
      updateQuantity(productId, change) {
        const item = this.cart.find(item => item.id === productId);
        if (item) {
          item.quantity += change;
          if (item.quantity < 1) {
            this.cart = this.cart.filter(i => i.id !== productId);
          }
          this.saveCart();
        }
      },
      removeItem(productId) {
        this.cart = this.cart.filter(item => item.id !== productId);
        this.saveCart();
      },
      checkout() {
        if (this.cart.length === 0) {
          alert('Кошик порожній!');
          return;
        }
        if (confirm(`Підтвердити замовлення на суму ${this.total} грн?`)) {
          this.cart = [];
          this.saveCart();
          alert('Замовлення успішно оформлено!');
        }
      },
      toggleCart() {
        this.isCartOpen = !this.isCartOpen;
      },
      saveCart() {
        localStorage.setItem('cart', JSON.stringify(this.cart));
      }
    }
  };
  </script>
  
  <style scoped>
  .shop {
    position: relative;
    min-height: 100vh;
    padding-bottom: 60px;
  }
  
  .products {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
  }
  
  .product-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
  }
  
  .product-image {
    max-width: 100%;
    height: 200px;
    object-fit: contain;
    margin-bottom: 10px;
  }
  
  .cart-icon {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #007bff;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    transition: transform 0.2s;
  }
  
  .cart-icon:hover {
    transform: scale(1.1);
  }
  
  .cart-icon svg {
    width: 24px;
    height: 24px;
    fill: white;
  }
  
  .cart-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background: red;
    color: white;
    padding: 4px 8px;
    border-radius: 50%;
    font-size: 12px;
  }
  
  .cart-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1000;
  }
  
  .cart-overlay {
    background: rgba(0,0,0,0.5);
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.3s;
  }
  
  .cart-panel {
    position: absolute;
    bottom: -100%;
    left: 0;
    width: 100%;
    height: 70vh;
    background: white;
    border-radius: 16px 16px 0 0;
    padding: 20px;
    transition: bottom 0.3s;
  }
  
  .cart-open .cart-overlay {
    opacity: 1;
    pointer-events: all;
  }
  
  .cart-open .cart-panel {
    bottom: 0;
  }
  
  .cart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  
  .close-btn {
    font-size: 24px;
    background: none;
    border: none;
    color: #666;
  }
  
  .empty-cart {
    text-align: center;
    color: #666;
    padding: 40px 0;
  }
  
  .cart-item {
    display: flex;
    justify-content: space-between;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
  }
  
  .item-controls {
    display: flex;
    align-items: center;
    gap: 15px;
  }
  
  .quantity-controls {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .quantity-controls button {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    padding: 0;
  }
  
  .remove-btn {
    background: #dc3545;
  }
  
  .checkout-btn {
    width: 100%;
    padding: 15px;
    background: #28a745;
    font-size: 16px;
  }
  
  .total-price {
    font-size: 18px;
    font-weight: bold;
    margin: 20px 0;
  }
  
  button {
    transition: all 0.2s;
  }
  
  button:active {
    transform: scale(0.95);
  }
  </style>