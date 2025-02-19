<template>
  <div class="order-wrapper">
    <div class="order-left">
      <h2>Особисті дані</h2>
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
      <h2>Адреса доставки</h2>
      <div v-if="hasAddressError" class="error-box">
        <p>Будь ласка, заповніть всі поля адреси (країна, місто, відділення).</p>
      </div>
      <div class="form-group">
        <label for="country">Країна</label>
        <input type="text" id="country" v-model="address.country" />
      </div>
      <div class="form-group">
        <label for="city">Місто</label>
        <input type="text" id="city" v-model="address.city" />
      </div>
      <div class="form-group">
        <label for="branch">Відділення</label>
        <input type="text" id="branch" v-model="address.branch" />
      </div>
      <hr />
      <h2>Ваші замовлення</h2>
      <div class="order-item" v-for="(item, index) in cart" :key="index">
        <p>{{ item.name }} (x{{ item.quantity }}) — {{ item.price }} грн</p>
      </div>
      <hr />
      <h2>Доставка</h2>
      <div class="radio-options">
        <label><input type="radio" value="Самовивіз" v-model="order.shipping" /> Самовивіз</label>
        <label><input type="radio" value="Нова Пошта" v-model="order.shipping" /> Нова Пошта</label>
        <label><input type="radio" value="Кур'єр" v-model="order.shipping" /> Кур'єрська доставка</label>
      </div>
      <hr />
      <h2>Оплата</h2>
      <div class="radio-options">
        <label><input type="radio" value="Оплата банківською картою" v-model="order.payment" /> Оплата банківською картою</label>
        <label><input type="radio" value="Оплата при отриманні" v-model="order.payment" /> Оплата при отриманні</label>
        <label><input type="radio" value="Оплата через термінал" v-model="order.payment" /> Оплата через термінал</label>
      </div>
    </div>

    <div class="order-right">
      <h2>Склад замовлення</h2>
      <div class="order-summary">
        <p>Сума: {{ totalAmount }} грн</p>
      </div>
      <div v-if="message" class="order-message">
        {{ message }}
      </div>
      <button @click="submitOrder">Підтвердити замовлення</button>
    </div>
  </div>
</template>

<script>
export default {
  name: "OrderDel",
  props: {
    cart: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      order: {
        name: "",
        phone: "",
        email: "",
        shipping: "Нова Пошта",
        payment: "Оплата банківською картою"
      },
      address: {
        country: "",
        city: "",
        branch: ""
      },
      message: "",
      hasNameError: false,
      hasAddressError: false
    };
  },
  computed: {
    totalAmount() {
      return this.cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    }
  },
  methods: {
    async submitOrder() {
      if (this.order.name.length < 3) {
        this.hasNameError = true;
        return;
      }
      this.hasNameError = false;

      if (!this.address.country || !this.address.city || !this.address.branch) {
        this.hasAddressError = true;
        return;
      }
      this.hasAddressError = false;

      const payload = {
        cart: this.cart,
        name: this.order.name,
        phone: this.order.phone,
        email: this.order.email,
        shipping: this.order.shipping,
        payment: this.order.payment,
        address: this.address
      };

      console.log("Payload:", payload);

      try {
        const response = await fetch("http://localhost/weaponshop/php/order.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "include",
          body: JSON.stringify(payload)
        });

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(`Сервер повернув помилку (${response.status}): ${errorData.message}`);
        }

        const data = await response.json();
        this.message = data.message;
      } catch (error) {
        console.error("Помилка при оформленні замовлення:", error);
        this.message = "Сталася помилка при оформленні замовлення.";
      }
    }
  }
};
</script>

<style scoped>
.order-wrapper {
  display: flex;
  justify-content: space-between;
  max-width: 900px;
  margin: 40px auto;
  background: #fff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.order-left {
  flex: 2;
  padding-right: 20px;
}

.order-right {
  flex: 1;
  padding: 20px;
  background: #f9f9f9;
  border-radius: 10px;
  text-align: center;
}

.radio-options label {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 16px;
  cursor: pointer;
  margin-bottom: 5px;
}

button {
  background: #4caf50;
  color: white;
  padding: 10px 15px;
  border-radius: 5px;
  cursor: pointer;
  width: 100%;
  margin-top: 10px;
}
button:hover {
  background: #45a049;
}
</style>
