<template>
  <nav class="main-menu">
    <ul class="menu-list">
      <li class="menu-item" v-for="item in menuItems" :key="item.id">
        <router-link 
          :to="item.link" 
          active-class="active-link"
          class="menu-link"
        >{{ item.name }}</router-link>
      </li>
    </ul>

    <!-- Пошук -->
    <div class="search-container">
      <input
        type="text"
        v-model="searchTerm"
        @input="onSearchInput"
        placeholder="Пошук..."
        class="search-input"
      />
      <!-- Випадаючий список пропозицій -->
      <ul v-if="suggestions.length" class="suggestion-list">
        <li
          v-for="(item, index) in suggestions"
          :key="index"
          @click="onSelect(item)"
          class="suggestion-item"
        >
          <!-- Відображення фото (якщо воно існує) -->
          <img
            v-if="item.image"
            :src="item.image"
            alt="Фото"
            class="suggestion-img"
          />
          <!-- Якщо фото немає, можна вивести placeholder -->
          <img
            v-else
            src="/path/to/placeholder.jpg"
            alt="Фото"
            class="suggestion-img"
          />
          <!-- Відображення назви -->
          <span v-if="item.type === 'weapon'">
            🔫 {{ item.name }}
          </span>
          <span v-else-if="item.type === 'category'">
            📂 {{ item.name }}
          </span>
        </li>
      </ul>
    </div>

    <!-- Якщо користувач не авторизований -->
    <router-link v-if="!user" to="/signin" class="login-btn">Увійти</router-link>

    <!-- Якщо користувач авторизований -->
    <div v-else class="user-menu">
      <span @click="toggleMenu" class="username">{{ user.username }}</span>
      <div v-if="menuOpen" class="dropdown-menu">
        <router-link to="/profile" class="dropdown-item">Профіль</router-link>
        <button @click="logout" class="dropdown-item">Вийти</button>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: "MainMenu",
  props: {
    user: Object
  },
  data() {
    return {
      menuOpen: false,
      menuItems: [
        { id: 1, name: "Головна", link: "/" },
        { id: 2, name: "Зброя", link: "/weapons" },
        { id: 3, name: "Про нас", link: "/about" },
        { id: 4, name: "Контакти", link: "/contact" }
      ],
      // Дані для пошуку
      searchTerm: '',
      suggestions: []
    };
  },
  methods: {
    toggleMenu() {
      this.menuOpen = !this.menuOpen;
    },
    async logout() {
      await fetch('http://localhost/weaponshop/php/logout.php', {
        method: 'POST',
        credentials: 'include'
      });
      this.$emit("logout");
      this.menuOpen = false;
    },
    onSearchInput() {
      // Якщо поле пошуку порожнє, скидаємо пропозиції
      if (!this.searchTerm) {
        this.suggestions = [];
        return;
      }
      // Робимо запит до global_search.php
      fetch(`http://localhost/weaponshop/php/global_search.php?term=${encodeURIComponent(this.searchTerm)}`)
        .then(response => response.json())
        .then(data => {
          this.suggestions = data;
        })
        .catch(err => console.error('Помилка пошуку:', err));
    },
    onSelect(item) {
      // Переходимо на відповідну сторінку в залежності від типу
      if (item.type === 'weapon') {
        // Наприклад, маршрут для зброї: /weapon/:id
        this.$router.push(`/weapon/${item.id}`);
      } else if (item.type === 'category') {
        // Для категорій: /category/:id
        this.$router.push(`/category/${item.id}`);
      }
      // Очищаємо поле пошуку та список підказок
      this.searchTerm = '';
      this.suggestions = [];
    }
  }
};
</script>

<style scoped>
.main-menu {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #2c3e50;
  padding: 1rem;
}

.menu-list {
  list-style: none;
  display: flex;
  margin: 0;
  padding: 0;
}

.search-container {
  position: relative;
  margin-left: 20px; /* щоб трохи відступити від меню */
}

.search-input {
  padding: 5px 8px;
  /* інші стилі за бажанням */
}

.suggestion-list {
  position: absolute;
  top: 30px; /* відступ від поля вводу */
  left: 0;
  width: 200px;
  background: #fff;
  border: 1px solid #ccc;
  list-style: none;
  margin: 0;
  padding: 0;
  z-index: 10; /* щоб перекривало інші елементи */
}

.suggestion-item {
  padding: 5px 10px;
  cursor: pointer;
}

.suggestion-item:hover {
  background-color: #eee;
}

.suggestion-img {
  width: 40px;
  height: 40px;
  object-fit: cover;
  margin-right: 10px;
  border-radius: 4px;
}

.menu-item {
  font-size: 1.2rem;
  margin-right: 20px;
}

.menu-link {
  text-decoration: none;
  color: white;
  transition: color 0.3s;
}

.menu-link:hover {
  color: #f39c12;
}

.login-btn {
  text-decoration: none;
  color: white;
  font-size: 1.2rem;
  padding: 8px 12px;
  background-color: #e74c3c;
  border-radius: 5px;
}

.user-menu {
  position: relative;
  cursor: pointer;
  color: white;
  font-size: 1.2rem;
}

.dropdown-menu {
  position: absolute;
  top: 30px;
  right: 0;
  background: white;
  border-radius: 5px;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
  padding: 10px;
}

.dropdown-item {
  display: block;
  text-decoration: none;
  color: black;
  padding: 5px 10px;
  cursor: pointer;
}

.dropdown-item:hover {
  background: #f1f1f1;
}
</style>
