<template>
  <div>
  <!-- Іконка кошика, яка використовує власне зображення -->
  <div class="cart-button" @click="toggleCart">
    <img src="@/assets/shopping.png" alt="Кошик" />
    <div v-if="cart.length > 0" class="cart-count">{{ cart.length }}</div>
  </div>

    <!-- Модальне вікно кошика -->
    <div v-if="showCart" class="cart-modal">
      <h2>Кошик</h2>
      <div v-if="cart.length === 0">
        Кошик порожній.
      </div>
      <div v-else>
        <ul>
          <li v-for="item in cart" :key="item.id">
            {{ item.name }} - {{ item.quantity }} x {{ item.price }} грн
          </li>
        </ul>
        <button @click="clearCart">Очистити кошик</button>
        <button @click="checkout">Оформити замовлення</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    cart: {
      type: Array,
      required: true,
    }
  },
  data() {
    return {
      showCart: false
    };
  },
  methods: {
    toggleCart() {
      this.showCart = !this.showCart; // Відкриває або закриває кошик
    },
    clearCart() {
      this.$emit('clear-cart'); // Викидає подію для очищення кошика
    },
    checkout() {
      // Логіка для оформлення замовлення
      alert("Оформлено замовлення!");
    }
  }
};
</script>

<style scoped>
.cart-button {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: transparent; /* Робимо фон прозорим, щоб тільки іконка була видна */
  border: none;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

.cart-button img {
  width: 40px;
  height: 40px;
}

.cart-button:hover {
  background-color: #45a049;
}

.cart-count {
  position: absolute;
  top: -10px;
  right: -10px;
  background-color: red;
  color: white;
  border-radius: 50%;
  padding: 2px 6px;
  font-size: 14px;
}

.cart-modal {
  position: fixed;
  bottom: 100px;
  right: 20px;
  background-color: white;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 10px;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
  width: 300px;
  z-index: 100;
}

.cart-modal button {
  margin-top: 10px;
  padding: 10px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.cart-modal button:hover {
  background-color: #45a049;
}
</style>