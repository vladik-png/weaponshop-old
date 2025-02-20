<template>
  <div class="weapons-list">
    <!-- Кнопка "Назад" перенесена у верхню частину -->
    <button class="back-arrow" @click="goBack">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6"></polyline>
      </svg>
    </button>

    <h1>Список зброї</h1>

    <div class="filters">
      <div class="sort-icon" @click="toggleFilterMenu">
        <img src="@/assets/icons8-sort-100.png" alt="Сортування" class="sort-icon-img" />
      </div>
    </div>

    <div v-if="isFilterMenuVisible" class="filter-menu-overlay" @click="closeFilterMenu">
      <div class="filter-menu" @click.stop>
        <!-- Фільтр за категорією -->
        <div class="filter-group">
          <label for="category">Категорія:</label>
          <select id="category" v-model="selectedCategory">
            <option value="">Всі категорії</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>

        <!-- Сортування за -->
        <div class="filter-group">
          <label for="sortBy">Сортувати за:</label>
          <select id="sortBy" v-model="selectedSortBy">
            <option value="name">Назвою</option>
            <option value="price">Ціною</option>
            <option value="quantity">Кількістю</option>
          </select>
        </div>

        <!-- Напрямок сортування -->
        <div class="filter-group">
          <label for="order">Напрямок сортування:</label>
          <select id="order" v-model="selectedOrder">
            <option value="ASC">За зростанням</option>
            <option value="DESC">За спаданням</option>
          </select>
        </div>

        <div class="filter-actions">
          <button @click="applyFilters">Застосувати</button>
          <button @click="closeFilterMenu">Закрити</button>
        </div>
      </div>
    </div>

    <!-- Відображення товарів плиткою -->
    <div class="weapons-grid">
      <div class="weapon-item" v-for="weapon in filteredWeapons" :key="weapon.id">
        <img :src="weapon.image" :alt="weapon.name" class="weapon-image" />
        <div class="weapon-details">
          <h2>{{ weapon.name }}</h2>
          <p>{{ weapon.description }}</p>
          <p><strong>Ціна: </strong>{{ weapon.price }} грн</p>
          <p><strong>Кількість: </strong>{{ getAvailableQuantity(weapon) }}</p>
          <p><strong>Категорія: </strong>{{ getCategoryName(weapon.category_id) }}</p>
          <button 
            @click="addToCart(weapon)" 
            :disabled="getAvailableQuantity(weapon) <= 0"
            class="add-to-cart-btn"
          >
            Додати до кошика
          </button>
        </div>
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
      default: () => []
    }
  },
  data() {
    return {
      weapons: [],
      filteredWeapons: [],
      categories: [],
      errorMessage: null,
      isFilterMenuVisible: false,
      selectedCategory: '',
      selectedSortBy: 'name',
      selectedOrder: 'ASC',
    };
  },
  watch: {
    '$route.query.search': {
      immediate: true,
      handler() {
        this.filterWeapons();
      }
    }
  },
  mounted() {
    this.loadWeapons();
    this.loadCategories();
  },
  methods: {
    goBack() {
      this.$router.go(-1);
    },
    loadWeapons() {
      fetch("http://localhost/weaponshop/php/get_weapons.php")
        .then(response => response.json())
        .then(data => {
          this.weapons = data;
          this.filterWeapons();
        })
        .catch(error => {
          this.errorMessage = "Не вдалося завантажити список зброї.";
          console.error("Помилка завантаження:", error);
        });
    },
    loadCategories() {
      fetch("http://localhost/weaponshop/php/get_categories.php")
        .then(response => response.json())
        .then(data => {
          this.categories = data;
        })
        .catch(error => {
          console.error("Помилка завантаження категорій:", error);
        });
    },
    getCategoryName(categoryId) {
      const category = this.categories.find(cat => cat.id == categoryId);
      return category ? category.name : 'Невідома категорія';
    },
    addToCart(weapon) {
      const cartItem = this.cart.find(item => item.id === weapon.id);
      if (cartItem) {
        cartItem.quantity++;
      } else {
        this.$emit('add-to-cart', { ...weapon, quantity: 1 });
      }
      this.$emit('update-weapon-quantity', { id: weapon.id, quantity: weapon.quantity - 1 });
    },
    getAvailableQuantity(weapon) {
      const cartItem = this.cart.find(item => item.id === weapon.id);
      return cartItem ? weapon.quantity - cartItem.quantity : weapon.quantity;
    },
    toggleFilterMenu() {
      this.isFilterMenuVisible = !this.isFilterMenuVisible;
    },
    closeFilterMenu() {
      this.isFilterMenuVisible = false;
    },
    applyFilters() {
      this.filterWeapons();
      this.closeFilterMenu();
    },
    filterWeapons() {
      let filtered = this.weapons;
      let searchQuery = this.$route.query.search ? this.$route.query.search.toLowerCase() : '';
      if (searchQuery) {
        filtered = filtered.filter(weapon =>
          weapon.name.toLowerCase().includes(searchQuery)
        );
      }
      if (this.selectedCategory) {
        filtered = filtered.filter(weapon => weapon.category_id == this.selectedCategory);
      }
      this.filteredWeapons = filtered;
      this.sortWeapons();
    },
    sortWeapons() {
      this.filteredWeapons.sort((a, b) => {
        let comparison = 0;
        if (this.selectedSortBy === 'name') {
          comparison = a.name.localeCompare(b.name);
        } else if (this.selectedSortBy === 'price') {
          comparison = a.price - b.price;
        } else if (this.selectedSortBy === 'quantity') {
          comparison = a.quantity - b.quantity;
        }
        return this.selectedOrder === 'DESC' ? -comparison : comparison;
      });
    }
  }
};
</script>

<style scoped>
/* Встановлення шрифту "Roboto" */
.weapons-list {
  font-family: 'Roboto', sans-serif;
  padding: 20px;
  position: relative;
}

h1 {
  text-align: center;
  margin-bottom: 20px;
  color: #333;
}

/* Контейнер для сітки товарів з обмеженням максимальної ширини */
.weapons-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 300px));
  gap: 20px;
  margin: 20px auto;
  justify-content: center;
  padding: 0 20px;
}

/* Стилізація картки товару */
.weapon-item {
  background: #ffffff;
  border-radius: 15px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.weapon-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.weapon-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 10px;
  margin-bottom: 15px;
}

.weapon-details h2 {
  font-size: 20px;
  margin-bottom: 10px;
  color: #333;
}

.weapon-details p {
  font-size: 14px;
  margin: 5px 0;
  color: #555;
}

.add-to-cart-btn {
  padding: 10px 20px;
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

/* Стилізація фільтрів */
.filters {
  display: flex;
  justify-content: flex-end;
  width: 100%;
  margin-bottom: 20px;
}

.sort-icon {
  margin-left: -10px;
  position: relative;
  cursor: pointer;
}

.sort-icon-img {
  width: 30px;
  height: 30px;
}

.filter-menu-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.filter-menu {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  width: 80%;
  max-width: 400px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.filter-group {
  display: flex;
  flex-direction: column;
  margin: 15px 0;
}

.filter-group label {
  font-weight: bold;
  margin-bottom: 5px;
}

.filter-group select {
  padding: 8px;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.filter-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}

.filter-actions button {
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.filter-actions button:hover {
  background-color: #45a049;
}

/* Стилізація кнопки "Назад" */
.back-arrow {
  position: absolute;
  top: 20px;
  left: 20px;
  background: none;
  border: none;
  cursor: pointer;
}

.back-arrow svg {
  stroke: #333;
}
</style>
