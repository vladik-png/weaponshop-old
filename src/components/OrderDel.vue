<template>
  <div class="order-wrapper">
    <div class="order-left">
      <h2>Особисті дані</h2>
      <div v-if="hasNameError" class="error-box">
        <p>Ваше ім'я надто коротке.</p>
      </div>
      <div v-if="hasPhoneError" class="error-box">
        <p>Введіть дійсний номер телефону.</p>
      </div>
      <div v-if="hasEmailError" class="error-box">
        <p>Введіть дійсну електронну пошту.</p>
      </div>
      <div class="form-group">
        <label for="name">Ім'я</label>
        <input type="text" id="name" v-model="order.name" placeholder="Ваше ім'я" />
      </div>
      <div class="form-group">
        <label for="phone">Телефон</label>
        <input type="tel" id="phone" v-model="order.phone" placeholder="+380..." />
      </div>
      <div class="form-group">
        <label for="email">Електронна пошта</label>
        <input type="email" id="email" v-model="order.email" placeholder="example@mail.com" />
      </div>
      <hr />
      <h2>Адреса доставки</h2>
      <div v-if="hasAddressError" class="error-box">
        <p>Будь ласка, заповніть всі поля адреси (країна, місто, відділення).</p>
      </div>
      <div class="form-group">
        <label for="country">Країна</label>
        <input type="text" id="country" v-model="address.country" placeholder="Країна" />
      </div>
      <div class="form-group">
        <label for="city">Місто</label>
        <input type="text" id="city" v-model="address.city" placeholder="Місто/село" />
      </div>
      <div class="form-group">
        <label for="branch">Відділення Нової пошти</label>
        <input type="text" id="branch" v-model="address.branch" placeholder="Відділення Нової пошти" />
      </div>
      <hr />
      <h2>Ваші замовлення</h2>
      <div class="order-item" v-for="(item, index) in cart" :key="index">
        <p>{{ item.name }} (x{{ item.quantity }}) — {{ item.price }} грн</p>
      </div>
      <hr />
      <h2>Доставка</h2>
      <div class="radio-options">
        <label>
          <input type="radio" value="Самовивіз" v-model="order.shipping" />
          <span>Самовивіз</span>
        </label>
        <label>
          <input type="radio" value="Нова Пошта" v-model="order.shipping" />
          <span>Нова Пошта</span>
        </label>
        <label>
          <input type="radio" value="Кур'єр" v-model="order.shipping" />
          <span>Кур'єрська доставка</span>
        </label>
      </div>
      <hr />
      <h2>Оплата</h2>
<div class="radio-options payment-options">
  <label>
    <input type="radio" value="Оплата банківською картою" v-model="order.payment" />
    <span>Оплата банківською картою</span>
  </label>
  <label>
    <input type="radio" value="Оплата при отриманні" v-model="order.payment" />
    <span>Оплата при отриманні</span>
  </label>
  <label>
    <input type="radio" value="Оплата через термінал" v-model="order.payment" />
    <span>Оплата через термінал</span>
  </label>
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
      hasPhoneError: false,
      hasEmailError: false,
      hasAddressError: false
    };
  },
  computed: {
    totalAmount() {
      return this.cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    }
  },
  methods: {
    validateEmail(email) {
      // Проста перевірка електронної пошти через регулярний вираз
      const re = /\S+@\S+\.\S+/;
      return re.test(email);
    },
    async submitOrder() {
      // Перевірка особистих даних
      if (this.order.name.length < 3) {
        this.hasNameError = true;
      } else {
        this.hasNameError = false;
      }
      if (!this.order.phone) {
        this.hasPhoneError = true;
      } else {
        this.hasPhoneError = false;
      }
      if (!this.order.email || !this.validateEmail(this.order.email)) {
        this.hasEmailError = true;
      } else {
        this.hasEmailError = false;
      }
      
      // Перевірка адреси доставки
      if (!this.address.country || !this.address.city || !this.address.branch) {
        this.hasAddressError = true;
      } else {
        this.hasAddressError = false;
      }

      // Якщо є помилки — припинити виконання
      if (this.hasNameError || this.hasPhoneError || this.hasEmailError || this.hasAddressError) {
        return;
      }

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
  flex-wrap: wrap;
  gap: 20px;
  max-width: 1000px;
  margin: 40px auto;
  background: #fff;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.order-left,
.order-right {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.order-left {
  padding-right: 20px;
}

.order-left .radio-options {
  margin-bottom: 15px;
}

/* Блок доставки – горизонтально */
.order-left h2:nth-of-type(4) + .radio-options {
  display: flex;
  flex-direction: row;
  gap: 10px;
}

/* Блок оплати – горизонтально */
.order-left h2:nth-of-type(5) + .radio-options {
  display: flex;
  flex-direction: row;
  gap: 10px;
}

/* Стара стилізація для блоку "Склад замовлення" */
.order-right {
  flex: 1;
  padding: 20px;
  background: #f9f9f9;
  border-radius: 10px;
  text-align: center;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  font-weight: 600;
  margin-bottom: 5px;
}

.form-group input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  transition: border 0.3s;
}

.form-group input:focus {
  border-color: #4caf50;
  outline: none;
}

.error-box {
  background: #ffe6e6;
  border: 1px solid #ff4d4d;
  color: #d8000c;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 10px;
  font-size: 14px;
}

.order-summary {
  font-size: 18px;
  margin-bottom: 20px;
  font-weight: 600;
}

.radio-options {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 15px;
}

.radio-options label {
  display: flex;
  align-items: center;
  background: #f0f0f0;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s;
}

.radio-options input[type="radio"] {
  margin-right: 8px;
}

.radio-options label:hover {
  background: #e0e0e0;
}

/* Якщо використовується клас payment-options – горизонтальне розташування */
.payment-options {
  display: flex;
  flex-direction: row;
  gap: 10px;
  flex-wrap: wrap;
}

button {
  background: #4caf50;
  color: #fff;
  padding: 12px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  transition: background 0.3s;
}

button:hover {
  background: #43a047;
}
</style>

