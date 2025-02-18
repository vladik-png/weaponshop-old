<template>
    <div class="admin-container">
      <!-- Sidebar -->
      <aside class="sidebar">
        <h2>Адмін-панель</h2>
        <ul>
          <li @click="currentSection = 'dashboard'">Головна</li>
          <li @click="currentSection = 'weapons'">Зброя</li>
          <li @click="currentSection = 'categories'">Категорії</li>
        </ul>
      </aside>
  
      <!-- Основний контент -->
      <main class="content">
        <div v-if="currentSection === 'dashboard'">
          <h1>Вітаємо в адмін-панелі</h1>
          <p>Оберіть розділ у меню зліва.</p>
        </div>
  
        <div v-if="currentSection === 'weapons'">
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
  
          <h2>Список зброї</h2>
          <table>
            <thead>
              <tr>
                <th>Назва</th>
                <th>Опис</th>
                <th>Ціна</th>
                <th>Кількість</th>
                <th>Категорія</th>
                <th>Дії</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in weapons" :key="item.id">
                <td>{{ item.name }}</td>
                <td>{{ item.description }}</td>
                <td>{{ item.price }}</td>
                <td>{{ item.quantity }}</td>
                <td>{{ item.category_name }}</td>
                <td>
                  <button @click="editWeapon(item)">Редагувати</button>
                  <button @click="deleteWeapon(item.id)">Видалити</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        currentSection: 'dashboard',
        weapon: { name: '', description: '', price: '', quantity: '', image: '', category_id: '' },
        categories: [],
        weapons: [],
        editing: false,
      };
    },
    methods: {
      async fetchCategories() {
        const response = await fetch('http://localhost/weaponshop/php/adminpanel.php?categories=1', { credentials: 'include' });
        this.categories = await response.json();
      },
      async fetchWeapons() {
        const response = await fetch('http://localhost/weaponshop/php/adminpanel.php', { credentials: 'include' });
        this.weapons = await response.json();
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
    },
    mounted() {
      this.fetchCategories();
      this.fetchWeapons();
    },
  };
  </script>
<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
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

input, select {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
  margin-bottom: 10px;
  border: 1px solid #000000;
  border-radius: 4px;
}

button {
  background-color: #4CAF50;
  color: white;
  padding: 10px 15px;
  border: solid;
  border-radius: 8px;
  cursor: pointer;
   margin-bottom: 20px
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
  border: 1px solid #000000;
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

</style>  