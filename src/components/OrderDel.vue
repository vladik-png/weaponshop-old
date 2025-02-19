<template>
  <div class="order-wrapper">
    <!-- ЛІВА КОЛОНКА -->
    <div class="order-left">
      <!-- ОСОБИСТІ ДАНІ -->
      <h2>Особисті дані</h2>

      <!-- Приклад повідомлення про помилку імені -->
      <div v-if="hasNameError" class="error-box">
        <p>Ваше ім'я надто коротке.</p>
      </div>

      <div class="form-group">
        <label for="name">Ім'я</label>
        <input type="text" id="name" v-model="order.name" />
      </div>

      <div class="form-group">
        <label for="phone">Телефон</label>
        <input type="tel" id="phone" v-model="order.phone" />
      </div>

      <div class="form-group">
        <label for="email">Електронна пошта</label>
        <input type="email" id="email" v-model="order.email" />
      </div>

      <hr />

      <!-- ВАШІ ЗАМОВЛЕННЯ (підтягуємо з props: cart) -->
      <h2>Ваші замовлення</h2>
      <div 
        class="order-item" 
        v-for="(item) in cart" 
        :key="item.id"
      >
        <p>
          {{ item.name }} (x{{ item.quantity }}) — {{ item.price }} грн
        </p>
      </div>

      <hr />

      <!-- ДОСТАВКА -->
      <h2>Доставка</h2>
      <div class="form-group">
        <label>
          <input
            type="radio"
            value="Самовивіз"
            v-model="order.shipping"
          />
          Самовивіз
        </label>
        <label>
          <input
            type="radio"
            value="Нова Пошта"
            v-model="order.shipping"
          />
          Нова Пошта
        </label>
        <label>
          <input
            type="radio"
            value="Кур'єр"
            v-model="order.shipping"
          />
          Кур'єрська доставка
        </label>
      </div>

      <hr />

      <!-- ОПЛАТА -->
      <h2>Оплата</h2>
      <div class="form-group">
        <label>
          <input
            type="radio"
            value="Оплата банківською картою"
            v-model="order.payment"
          />
          Оплата банківською картою
        </label>
        <label>
          <input
            type="radio"
            value="Оплата при отриманні"
            v-model="order.payment"
          />
          Оплата при отриманні
        </label>
        <label>
          <input
            type="radio"
            value="Оплата через термінал"
            v-model="order.payment"
          />
          Оплата через термінал
        </label>
      </div>
    </div>

    <!-- ПРАВА КОЛОНКА -->
    <div class="order-right">
      <h2>Склад замовлення</h2>
      <div class="order-summary">
        <p>Сума: {{ totalAmount }} грн</p>
      </div>

      <!-- ПОВІДОМЛЕННЯ ПРО РЕЗУЛЬТАТ ЗАМОВЛЕННЯ -->
      <div v-if="message" class="order-message">
        {{ message }}
      </div>

      <!-- КНОПКА ПІДТВЕРДИТИ ЗАМОВЛЕННЯ -->
      <button @click="submitOrder">Підтвердити замовлення</button>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "OrderDel",
  props: {
    cart: {
      type: Array,
      required: true
    },
    user: {
      type: Object,
      default: () => ({ id: 0 })
    },
    // Якщо передаєте готовий address_id з профілю
    addressId: {
      type: Number,
      default: null
    }
  },
  data() {
    return {
      order: {
        shipping: "Нова Пошта",
        payment: "Оплата банківською картою"
      },
      message: ""
    };
  },
  computed: {
    totalAmount() {
      return this.cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    }
  },
  methods: {
    async submitOrder() {
      try {
        const payload = {
          user_id: this.user.id,
          cart: this.cart,
          shipping: this.order.shipping,
          payment: this.order.payment,
          address_id: this.addressId,
        };

        const response = await axios.post("http://localhost/weaponshop/php/order.php", payload);
        this.message = response.data.message;
      } catch (error) {
        this.message = "Сталася помилка при оформленні замовлення.";
      }
    }
  }
};
</script>


<style scoped>
.order-wrapper {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}
.order-left {
  flex: 2;
  min-width: 300px;
}
.order-right {
  flex: 1;
  min-width: 250px;
  background: #f9f9f9;
  padding: 20px;
  border-radius: 4px;
}
.error-box {
  background-color: #fdd;
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid #f99;
}
.form-group {
  margin-bottom: 15px;
  display: flex;
  flex-direction: column;
}
.order-left h2,
.order-right h2 {
  margin-top: 0;
}
.order-item {
  margin-bottom: 10px;
}
.order-summary {
  margin-bottom: 20px;
  font-size: 16px;
}
.order-message {
  margin-bottom: 20px;
  color: green;
  font-weight: bold;
}
button {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  cursor: pointer;
  border: none;
  border-radius: 4px;
  background-color: #3498db;
  color: #fff;
}
button:hover {
  background-color: #2980b9;
}
</style>
