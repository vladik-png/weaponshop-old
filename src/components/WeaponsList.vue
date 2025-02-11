<template>
  <div class="weapons-list">
    <h1>Список зброї</h1>
    
    <!-- Фільтр за категоріями -->
    <div class="filters">
      <select v-model="selectedCategory" @change="filterWeapons">
        <option value="">Виберіть категорію</option>
        <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
      </select>

      <select v-model="sortOrder" @change="sortWeapons">
        <option value="name">Сортувати за назвою</option>
        <option value="price">Сортувати за ціною</option>
        <option value="quantity">Сортувати за кількістю</option>
      </select>
    </div>

    <!-- Покажемо повідомлення, якщо не вдалося завантажити зброю -->
    <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
    
    <!-- Покажемо повідомлення, якщо список зброї порожній -->
    <div v-if="weapons.length === 0 && !errorMessage" class="empty-message">Немає доступних товарів.</div>
    
    <div class="weapon-item" v-for="weapon in filteredWeapons" :key="weapon.id">
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
      weapons: [],  // Список зброї
      filteredWeapons: [], // Фільтрований список зброї
      categories: ['Категорія 1', 'Категорія 2', 'Категорія 3'], // Категорії зброї
      selectedCategory: '',
      sortOrder: 'name', // Сортування за назвою за замовчуванням
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
        this.filteredWeapons = data; // Початково відображаємо всі товари
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
    filterWeapons() {
      if (this.selectedCategory) {
        this.filteredWeapons = this.weapons.filter(weapon => weapon.category === this.selectedCategory);
      } else {
        this.filteredWeapons = this.weapons; // Якщо не вибрана категорія, показуємо всі товари
      }
    },
    sortWeapons() {
      if (this.sortOrder === 'name') {
        this.filteredWeapons.sort((a, b) => a.name.localeCompare(b.name));
      } else if (this.sortOrder === 'price') {
        this.filteredWeapons.sort((a, b) => a.price - b.price);
      } else if (this.sortOrder === 'quantity') {
        this.filteredWeapons.sort((a, b) => a.quantity - b.quantity);
      }
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
