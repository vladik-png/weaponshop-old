<template>
  <div class="weapons-list">
    <h1>Список зброї</h1>
    
    <!-- Покажемо повідомлення, якщо не вдалося завантажити зброю -->
    <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
    
    <!-- Покажемо повідомлення, якщо список зброї порожній -->
    <div v-if="weapons.length === 0 && !errorMessage" class="empty-message">Немає доступних товарів.</div>
    
    <div class="weapon-item" v-for="weapon in weapons" :key="weapon.id">
      <img :src="weapon.image" :alt="weapon.name" class="weapon-image" />
      <div class="weapon-details">
        <h2>{{ weapon.name }}</h2>
        <p>{{ weapon.description }}</p>
        <p><strong>Ціна: </strong>{{ weapon.price }} грн</p>
        <p><strong>Кількість: </strong>{{ weapon.quantity }}</p>
        <button 
          @click="addToCart(weapon)" 
          :disabled="weapon.quantity <= 0"
          class="add-to-cart-btn"
        >
          Додати до кошика
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'WeaponsList',
  props: {
    cart: {
      type: Array,
      required: true,
      default: () => [] // Встановлення дефолтного значення
    }
  },
  data() {
    return {
      weapons: [],
      errorMessage: null // Для зберігання повідомлення про помилку
    };
  },
  mounted() {
    fetch("http://localhost/weaponshop/php/get_weapons.php")
      .then(response => {
        if (!response.ok) {
          throw new Error("Помилка сервера: " + response.status);
        }
        return response.json();
      })
      .then(data => {
        this.weapons = data;
      })
      .catch(error => {
        this.errorMessage = "Не вдалося завантажити список зброї. Спробуйте пізніше."; // Виведемо повідомлення про помилку
        console.error("Помилка завантаження:", error);
      });
  },
  methods: {
    addToCart(weapon) {
      const existingItem = this.cart.find(item => item.id === weapon.id);
      if (existingItem) {
        existingItem.quantity++; // Збільшуємо кількість, якщо товар вже в кошику
      } else {
        this.$emit('add-to-cart', { ...weapon, quantity: 1 }); // Додаємо товар до кошика, якщо його ще немає
      }
      weapon.quantity--; // Зменшуємо кількість на складі
    },
    isInCart(weapon) {
      return this.cart.some(item => item.id === weapon.id);
    }
  }
};
</script>

<style scoped>
.weapons-list {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.weapon-item {
  display: flex;
  align-items: center;
  margin: 20px;
  border: 1px solid #ccc;
  padding: 10px;
  border-radius: 5px;
  width: 80%;
}

.weapon-image {
  width: 200px;
  height: auto;
  margin-right: 20px;
}

.weapon-details {
  flex-grow: 1;
}

.weapon-details h2 {
  font-size: 1.5em;
  margin-bottom: 10px;
}

.weapon-details p {
  margin: 5px 0;
}

.add-to-cart-btn {
  padding: 8px 16px;
  margin-top: 10px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.add-to-cart-btn:hover {
  background-color: #45a049;
}

.add-to-cart-btn:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.error-message {
  color: red;
  font-weight: bold;
  margin-top: 20px;
}

.empty-message {
  color: #888;
  font-style: italic;
  margin-top: 20px;
}
</style>
