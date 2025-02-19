<template>   
  <div v-if="cart.length > 0 && $route.name !== 'Order'">
    <!-- Іконка кошика -->
    <div class="cart-button" @click="toggleCart">
      <img src="@/assets/shopping.png" alt="Кошик" />
      <div v-if="cart.length > 0" class="cart-count">
        {{ cart.reduce((total, item) => total + item.quantity, 0) }}
      </div>
    </div>

    <!-- Модальне вікно кошика -->
    <div v-if="showCart" class="cart-modal">
      <h2>Кошик</h2>
      <div v-if="cart.length === 0">Кошик порожній.</div>
      <div v-else>
        <div class="cart-items-list">
          <ul>
            <li v-for="item in cart" :key="item.id" class="cart-item">
              <img :src="item.image" alt="Product image" class="cart-item-image" />
              <span>{{ item.name }} - {{ item.quantity }} x {{ item.price }} грн</span>
            </li>
          </ul>
        </div>
        <div class="cart-actions">
          <button @click="clearCart" class="action-btn clear-btn">Очистити</button>
          <button @click="checkout" class="action-btn checkout-btn">Оформити</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from "vue";
import { useRouter, useRoute } from "vue-router";

export default {
  props: {
    cart: {
      type: Array,
      required: true
    }
  },
  setup(props, { emit }) {
    const showCart = ref(false);
    const router = useRouter();
    const route = useRoute();

    // Визначаємо, чи кошик має бути видимим
    const isCartVisible = computed(() => props.cart.length > 0 && route.name !== "Order");

    const toggleCart = () => {
      showCart.value = !showCart.value;
    };

    const clearCart = () => {
      emit("clear-cart");
    };

    const checkout = () => {
      if (props.cart.length === 0) return;
      router.push({ name: "Order" });
    };

    return { showCart, toggleCart, clearCart, checkout, isCartVisible };
  }
};
</script>


<style scoped>
.cart-button {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: transparent;
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
  padding: 8px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
}

.cart-modal button:hover {
  background-color: #45a049;
}

.cart-items-list {
  max-height: 200px;
  overflow-y: auto;
}

.cart-item {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.cart-item-image {
  width: 50px;
  height: 50px;
  margin-right: 10px;
}

.quantity-controls {
  display: flex;
  align-items: center;
  margin-left: 10px;
}

.quantity-btn {
  width: 30px;
  height: 30px;
  border-radius: 5px;
  border: none;
  background-color: #f0f0f0;
  cursor: pointer;
  font-size: 18px;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 5px;
}

.decrease-btn {
  background-color: #f44336;
  color: white;
}

.increase-btn {
  background-color: #4CAF50;
  color: white;
}

.quantity-btn:hover {
  opacity: 0.8;
}

.cart-actions {
  display: flex;
  justify-content: space-between;
}

.action-btn {
  padding: 8px 12px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  flex: 1;
  margin-top: 10px;
  margin-right: 10px;
}

.action-btn:hover {
  background-color: #45a049;
}

.clear-btn {
  background-color: #f44336;
}

.clear-btn:hover {
  background-color: #d32f2f;
}

.checkout-btn {
  background-color: #2196F3;
}

.checkout-btn:hover {
  background-color: #1976D2;
}
</style>
