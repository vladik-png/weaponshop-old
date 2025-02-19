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

      <!-- Кнопка "Адмін панель" відображається лише для адміністратора -->
      <li class="menu-item" v-if="isAdmin">
        <router-link 
          to="/admin" 
          active-class="active-link"
          class="menu-link"
        >
          Адмін панель
        </router-link>
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
          <img
            v-if="item.image"
            :src="item.image"
            alt="Фото"
            class="suggestion-img"
          />
          <img
            v-else
            src="/path/to/placeholder.jpg"
            alt="Фото"
            class="suggestion-img"
          />
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
    <div v-else ref="userMenu" class="user-menu">
      <span @click="toggleMenu" class="username">{{ user.username }}</span>
      <div v-if="menuOpen" class="dropdown-menu">
        <router-link to="/profile" class="dropdown-item">Профіль</router-link>
        <button @click="logout" class="logout-btn">Вийти</button>
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
      searchTerm: '',
      suggestions: []
    };
  },
  computed: {
    // Визначаємо, чи є користувач адміністратором
    isAdmin() {
      return this.user && (this.user.role === 'admin' || this.user.isAdmin === true);
    }
  },
  mounted() {
    document.addEventListener("click", this.handleOutsideClick);
  },
  unmounted() {
    document.removeEventListener("click", this.handleOutsideClick);
  },
  watch: {
    // При зміні маршруту закриваємо меню
    $route() {
      this.menuOpen = false;
    }
  },
  methods: {
    toggleMenu() {
      this.menuOpen = !this.menuOpen;
    },
    handleOutsideClick(event) {
      if (this.menuOpen && this.$refs.userMenu && !this.$refs.userMenu.contains(event.target)) {
        this.menuOpen = false;
      }
    },
    async logout() {
      await fetch('http://localhost/weaponshop/php/logout.php', {
        method: 'POST',
        credentials: 'include'
      });
      this.$emit("logout");
      this.menuOpen = false;
      this.$router.push("/"); // Редірект після виходу
    },
    onSearchInput() {
      if (!this.searchTerm) {
        this.suggestions = [];
        return;
      }
      fetch(`http://localhost/weaponshop/php/global_search.php?term=${encodeURIComponent(this.searchTerm)}`)
        .then(response => response.json())
        .then(data => {
          this.suggestions = data;
        })
        .catch(err => console.error('Помилка пошуку:', err));
    },
    onSelect(item) {
      if (item.type === 'weapon') {
        this.$router.push(`/weapon/${item.id}`);
      } else if (item.type === 'category') {
        this.$router.push(`/category/${item.id}`);
      }
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
  background-color: #333;
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
  margin-left: 20px;
}

.search-input {
  padding: 5px 8px;
}

.suggestion-list {
  position: absolute;
  top: 30px;
  left: 0;
  width: auto;
  background: #fff;
  border: 1px solid #ccc;
  list-style: none;
  margin: 0;
  padding: 0;
  z-index: 10;
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
  transition: background 0.3s;
}

.login-btn:hover {
  background-color: #c0392b;
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
  min-width: 150px; 
}

.dropdown-item {
  display: block;
  text-decoration: none;
  color: black;
  padding: 8px 10px;
  cursor: pointer;
}

.dropdown-item:hover {
  background: #f1f1f1;
}

.logout-btn {
  width: 100%;
  background-color: #e74c3c;
  color: white;
  border: none;
  padding: 8px 10px;
  cursor: pointer;
  font-size: 1rem;
  border-radius: 5px;
  transition: background 0.3s;
}

.logout-btn:hover {
  background-color: #c0392b;
}
</style>
