<template>
  <div class="weapons-list">
    <h1>Список зброї</h1>

    <div class="filters">
      <div class="sort-icon" @click="toggleFilterMenu">
        <img src="@/assets/icons8-sort-100.png" alt="Сортування" class="sort-icon-img" />
      </div>
    </div>

    <div v-if="isFilterMenuVisible" class="filter-menu-overlay" @click="closeFilterMenu">
      <div class="filter-menu" @click.stop>
        <div class="filter-group">
          <label for="sortBy">Сортувати за:</label>
          <select id="sortBy" v-model="selectedSortBy">
            <option value="name">Назвою</option>
            <option value="price">Ціною</option>
            <option value="quantity">Кількістю</option>
          </select>
        </div>

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
      const category = this.categories.find(cat => cat.id === categoryId);
      return category ? category.name : 'Невідома категорія';
    },
    addToCart(weapon) {
      this.$emit('add-to-cart', { ...weapon, quantity: 1 });
    },
    getAvailableQuantity(weapon) {
      return weapon.quantity;
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
      let searchQuery = this.$route.query.search ? this.$route.query.search.toLowerCase() : '';
      this.filteredWeapons = this.weapons.filter(weapon => 
        weapon.name.toLowerCase().includes(searchQuery)
      );
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

.filters {
  display: flex;
  justify-content: flex-end;
  width: 100%;
  margin-bottom: 20px;
}

.sort-icon {
  margin-left: -10px;
  left: -30px;
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

</style>