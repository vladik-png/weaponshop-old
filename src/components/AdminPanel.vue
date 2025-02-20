<template>
  <div class="admin-container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>Адмін-панель</h2>
      <ul>
        <li @click="currentSection = 'dashboard'">Головна</li>
        <li @click="currentSection = 'weapons'">Зброя</li>
        <li v-if="userRole === 'admin'" @click="currentSection = 'categories'">Категорії</li>
        <li v-if="userRole === 'admin'" @click="currentSection = 'orders'">Замовлення</li>
        <li v-if="userRole === 'admin'" @click="currentSection = 'stats'">Статистика</li>
      </ul>
    </aside>

    <!-- Основний контент -->
    <main class="content">
      <!-- Dashboard -->
      <div v-if="currentSection === 'dashboard'">
        <h1>Вітаємо!</h1>
        <p v-if="userRole === 'admin'">
          Оберіть розділ у меню зліва для роботи зі зброєю, категоріями, замовленнями або статистикою.
        </p>
        <p v-else>
          Нижче наведено список зброї.
        </p>
      </div>

      <!-- Зброя -->
      <div v-if="currentSection === 'weapons'">
        <div v-if="userRole === 'admin'">
          <h2>{{ editing ? 'Редагувати зброю' : 'Додати зброю' }}</h2>
          <form @submit.prevent="editing ? updateWeapon() : addWeapon()">
            <label>Назва:</label>
            <input v-model="weapon.name" required />

            <label>Опис:</label>
            <textarea v-model="weapon.description" required></textarea>

            <label>Ціна:</label>
            <input type="number" v-model="weapon.price" required />

            <label>Кількість:</label>
            <input type="number" v-model="weapon.quantity" required />

            <label>Зображення (URL):</label>
            <input v-model="weapon.image" required />

            <label>Категорія:</label>
            <select v-model="weapon.category_id" required>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>

            <button type="submit">{{ editing ? 'Оновити' : 'Додати' }}</button>
            <button v-if="editing" type="button" @click="cancelEdit">Скасувати</button>
          </form>
        </div>

        <h2>Список зброї</h2>
        <table>
          <thead>
            <tr>
              <th @click="sortWeaponsBy('name')">
                Назва <span v-if="sortKeyWeapons === 'name'">{{ sortOrderWeapons === 'asc' ? '↑' : '↓' }}</span>
              </th>
              <th @click="sortWeaponsBy('description')">
                Опис <span v-if="sortKeyWeapons === 'description'">{{ sortOrderWeapons === 'asc' ? '↑' : '↓' }}</span>
              </th>
              <th @click="sortWeaponsBy('price')">
                Ціна <span v-if="sortKeyWeapons === 'price'">{{ sortOrderWeapons === 'asc' ? '↑' : '↓' }}</span>
              </th>
              <th @click="sortWeaponsBy('quantity')">
                Кількість <span v-if="sortKeyWeapons === 'quantity'">{{ sortOrderWeapons === 'asc' ? '↑' : '↓' }}</span>
              </th>
              <th @click="sortWeaponsBy('category_name')">
                Категорія <span v-if="sortKeyWeapons === 'category_name'">{{ sortOrderWeapons === 'asc' ? '↑' : '↓' }}</span>
              </th>
              <th v-if="userRole === 'admin'">Дії</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in sortedWeapons" :key="item.id">
              <td>{{ item.name }}</td>
              <td>{{ item.description }}</td>
              <td>{{ item.price }}</td>
              <td>{{ item.quantity }}</td>
              <td>{{ item.category_name }}</td>
              <td v-if="userRole === 'admin'">
                <button @click="editWeapon(item)">Редагувати</button>
                <button @click="deleteWeapon(item.id)">Видалити</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Категорії -->
      <div v-if="currentSection === 'categories' && userRole === 'admin'">
        <div>
          <h2>{{ editingCategory ? 'Редагувати категорію' : 'Додати категорію' }}</h2>
          <form @submit.prevent="editingCategory ? updateCategory() : addCategory()">
            <label>Назва категорії:</label>
            <input v-model="category.name" required />
            <button type="submit">{{ editingCategory ? 'Оновити' : 'Додати' }}</button>
            <button v-if="editingCategory" type="button" @click="cancelCategoryEdit">Скасувати</button>
          </form>
        </div>

        <h2>Список категорій</h2>
        <table>
          <thead>
            <tr>
              <th @click="sortCategoriesBy('name')">
                Назва <span v-if="sortKeyCategories === 'name'">{{ sortOrderCategories === 'asc' ? '↑' : '↓' }}</span>
              </th>
              <th>Дії</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cat in sortedCategories" :key="cat.id">
              <td>{{ cat.name }}</td>
              <td>
                <button @click="editCategory(cat)">Редагувати</button>
                <button @click="deleteCategory(cat.id)">Видалити</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Замовлення -->
      <div v-if="currentSection === 'orders' && userRole === 'admin'">
        <h2>Замовлення</h2>
        <table>
          <thead>
            <tr>
              <th @click="sortBy('id')">
                ID Замовлення <span v-if="sortKey === 'id'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
              </th>
              <th @click="sortBy('user_name')">
                Ім'я користувача <span v-if="sortKey === 'user_name'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
              </th>
              <th @click="sortBy('total')">
                Сума <span v-if="sortKey === 'total'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
              </th>
              <th @click="sortBy('shipping')">
                Доставка <span v-if="sortKey === 'shipping'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
              </th>
              <th @click="sortBy('payment')">
                Оплата <span v-if="sortKey === 'payment'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
              </th>
              <th>Товари</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in sortedOrders" :key="order.id">
              <td>{{ order.id }}</td>
              <td>{{ order.user_name }}</td>
              <td>{{ order.total }}</td>
              <td>{{ order.shipping }}</td>
              <td>{{ order.payment }}</td>
              <td>
                <ul>
                  <li v-for="item in order.items" :key="item.weapon_id">
                    {{ item.weapon_name }} ({{ item.quantity }} шт.)
                  </li>
                </ul>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Статистика -->
<div v-if="currentSection === 'stats' && userRole === 'admin'" class="stats-container">
  <h2>Статистика</h2>
  <div class="stats-grid">
    <div class="stat-card">
      <h3>Кількість замовлень</h3>
      <p>{{ stats.total_orders }}</p>
    </div>
    <div class="stat-card">
      <h3>Загальний обсяг продажів</h3>
      <p>{{ stats.total_revenue }}</p>
    </div>
    <div class="stat-card">
      <h3>Середня вартість замовлення</h3>
      <p>{{ stats.avg_order_value }}</p>
    </div>
    <div class="stat-card">
      <h3>Найпопулярніший товар</h3>
      <p v-if="stats.most_popular_weapon">
        {{ stats.most_popular_weapon.weapon_name }} ({{ stats.most_popular_weapon.total_quantity }} шт.)
      </p>
      <p v-else>Немає даних</p>
    </div>
    <div class="stat-card">
      <h3>Кількість товарів у наявності</h3>
      <p>{{ stats.total_weapons }}</p>
      </div>
    </div>
   </div>
      
    </main>
  </div>
</template>

<script>
export default {
  data() {
    return {
      currentSection: 'dashboard',
      userRole: 'admin', // або 'user'
      weapon: { name: '', description: '', price: '', quantity: '', image: '', category_id: '' },
      categories: [],
      weapons: [],
      orders: [],
      stats: {},
      editing: false,
      category: { id: null, name: '' },
      editingCategory: false,
      // Параметри сортування для замовлень
      sortKey: 'id',
      sortOrder: 'asc',
      // Параметри сортування для зброї
      sortKeyWeapons: 'name',
      sortOrderWeapons: 'asc',
      // Параметри сортування для категорій
      sortKeyCategories: 'name',
      sortOrderCategories: 'asc',
    };
  },
  computed: {
    // Сортування для замовлень (як у попередньому прикладі)
    sortedOrders() {
      if (!this.orders) return [];
      return this.orders.slice().sort((a, b) => {
        let modifier = this.sortOrder === 'asc' ? 1 : -1;
        let valueA = a[this.sortKey];
        let valueB = b[this.sortKey];
        if (!isNaN(valueA) && !isNaN(valueB)) {
          return (Number(valueA) - Number(valueB)) * modifier;
        }
        valueA = valueA.toString().toLowerCase();
        valueB = valueB.toString().toLowerCase();
        if (valueA < valueB) return -1 * modifier;
        if (valueA > valueB) return 1 * modifier;
        return 0;
      });
    },
    // Сортування для зброї
    sortedWeapons() {
      if (!this.weapons) return [];
      return this.weapons.slice().sort((a, b) => {
        let modifier = this.sortOrderWeapons === 'asc' ? 1 : -1;
        let valueA = a[this.sortKeyWeapons];
        let valueB = b[this.sortKeyWeapons];
        if (!isNaN(valueA) && !isNaN(valueB)) {
          return (Number(valueA) - Number(valueB)) * modifier;
        }
        valueA = valueA.toString().toLowerCase();
        valueB = valueB.toString().toLowerCase();
        if (valueA < valueB) return -1 * modifier;
        if (valueA > valueB) return 1 * modifier;
        return 0;
      });
    },
    // Сортування для категорій
    sortedCategories() {
      if (!this.categories) return [];
      return this.categories.slice().sort((a, b) => {
        let modifier = this.sortOrderCategories === 'asc' ? 1 : -1;
        let valueA = a[this.sortKeyCategories];
        let valueB = b[this.sortKeyCategories];
        if (!isNaN(valueA) && !isNaN(valueB)) {
          return (Number(valueA) - Number(valueB)) * modifier;
        }
        valueA = valueA.toString().toLowerCase();
        valueB = valueB.toString().toLowerCase();
        if (valueA < valueB) return -1 * modifier;
        if (valueA > valueB) return 1 * modifier;
        return 0;
      });
    }
  },
  methods: {
    // Сортування для замовлень
    sortBy(key) {
      if (this.sortKey === key) {
        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortKey = key;
        this.sortOrder = 'asc';
      }
    },
    // Сортування для зброї
    sortWeaponsBy(key) {
      if (this.sortKeyWeapons === key) {
        this.sortOrderWeapons = this.sortOrderWeapons === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortKeyWeapons = key;
        this.sortOrderWeapons = 'asc';
      }
    },
    // Сортування для категорій
    sortCategoriesBy(key) {
      if (this.sortKeyCategories === key) {
        this.sortOrderCategories = this.sortOrderCategories === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortKeyCategories = key;
        this.sortOrderCategories = 'asc';
      }
    },
    async fetchCategories() {
      const response = await fetch('http://localhost/weaponshop/php/adminpanel.php?categories=1', { credentials: 'include' });
      this.categories = await response.json();
    },
    async fetchWeapons() {
      const response = await fetch('http://localhost/weaponshop/php/adminpanel.php', { credentials: 'include' });
      this.weapons = await response.json();
    },
    async fetchOrders() {
      const response = await fetch('http://localhost/weaponshop/php/adminpanel.php?orders=1', { credentials: 'include' });
      this.orders = await response.json();
    },
    async fetchStats() {
      const response = await fetch('http://localhost/weaponshop/php/adminpanel.php?stats=1', { credentials: 'include' });
      this.stats = await response.json();
    },
    async addWeapon() {
      await fetch('http://localhost/weaponshop/php/adminpanel.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify(this.weapon),
      });
      this.weapon = { name: '', description: '', price: '', quantity: '', image: '', category_id: '' };
      this.fetchWeapons();
    },
    editWeapon(item) {
      this.weapon = { ...item };
      this.editing = true;
    },
    async updateWeapon() {
      await fetch('http://localhost/weaponshop/php/adminpanel.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify(this.weapon),
      });
      this.cancelEdit();
      this.fetchWeapons();
    },
    async deleteWeapon(id) {
      await fetch('http://localhost/weaponshop/php/adminpanel.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ id }),
      });
      this.fetchWeapons();
    },
    cancelEdit() {
      this.weapon = { name: '', description: '', price: '', quantity: '', image: '', category_id: '' };
      this.editing = false;
    },
    async addCategory() {
      await fetch('http://localhost/weaponshop/php/adminpanel.php?entity=category', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify(this.category),
      });
      this.category = { id: null, name: '' };
      this.fetchCategories();
    },
    editCategory(cat) {
      this.category = { ...cat };
      this.editingCategory = true;
    },
    async updateCategory() {
      await fetch('http://localhost/weaponshop/php/adminpanel.php?entity=category', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify(this.category),
      });
      this.cancelCategoryEdit();
      this.fetchCategories();
    },
    async deleteCategory(id) {
      const response = await fetch('http://localhost/weaponshop/php/adminpanel.php?entity=category', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ id }),
      });
      const result = await response.json();
      if (result.error) {
        alert(result.error);
      } else {
        this.fetchCategories();
      }
    },
    cancelCategoryEdit() {
      this.category = { id: null, name: '' };
      this.editingCategory = false;
    },
  },
  watch: {
    currentSection(newSection) {
      if (newSection === 'orders') {
        this.fetchOrders();
      }
      if (newSection === 'stats') {
        this.fetchStats();
      }
    }
  },
  mounted() {
    this.fetchCategories();
    this.fetchWeapons();
    if (this.userRole === 'admin') {
      this.fetchOrders();
      this.fetchStats();
    }
  },
};
</script>


<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Roboto', sans-serif;
}

html, body {
  height: 100%;
  overflow: auto; /* Додає загальну прокрутку */
}

/* --- Фіксована бокова панель --- */
.admin-container {
  display: flex;
  min-height: 100vh; /* Щоб контейнер ріс разом із контентом */
}

.sidebar {
  width: 200px;
  background-color: #333;
  color: white;
  padding: 20px;
  position: auto;
  height: auto;
  top: 0;
  left: 0;
  overflow-y: auto;
}

.sidebar h2 {
  margin-bottom: 20px;
  text-align: center;
}

.sidebar ul {
  list-style-type: none;
  padding: 0;
}

.sidebar li {
  padding: 10px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.sidebar li:hover {
  background-color: #555;
}

/* --- Контентна частина --- */
.content {
  flex: 1;
  padding: 20px;
  margin-left: 20px; /* Відступ під sidebar */
  overflow: visible; /* Забирає зайвий скрол у контенті */
}

.content h1 {
  color: #333;
  margin-bottom: 20px;
}

/* --- Форма додавання товару --- */
.form-container {
  background: white;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}

label {
  display: block;
  margin-top: 10px;
  color: #666;
}

input, select, textarea {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
  margin-bottom: 10px;
  border: 1px solid #000;
  border-radius: 4px;
}

button {
  background-color: #4CAF50;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  margin-bottom: 20px;
}

button:hover {
  background-color: #45a049;
}

/* --- Таблиця зі списком товарів --- */
.table-container {
  overflow-x: auto; /* Прокрутка, якщо ширина таблиці більше екрану */
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

th, td {
  border: 1px solid #000;
  padding: 8px;
  text-align: left;
}

th {
  background-color: #4CAF50;
  color: white;
}

tr:nth-child(even) {
  background-color: #f2f2f2;
}

.stats-container {
  background-color: #fff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.stats-container h2 {
  font-size: 28px;
  margin-bottom: 20px;
  text-align: center;
  color: #333;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.stat-card {
  background-color: #f8f8f8;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stat-card h3 {
  font-size: 20px;
  margin-bottom: 10px;
  color: #4CAF50;
}

.stat-card p {
  font-size: 18px;
  margin: 0;
  font-weight: bold;
  color: #333;
}
</style>
