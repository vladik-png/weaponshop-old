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

      <li class="menu-item" v-if="isAdmin">
        <router-link to="/admin" active-class="active-link" class="menu-link">
          Адмін панель
        </router-link>
      </li>
    </ul>

    <div class="search-container" ref="searchContainer" @click="closeSearch">
      <input
        type="text"
        v-model="searchTerm"
        @input="onSearchInput"
        placeholder="Пошук..."
        class="search-input"
        @click.stop
      />
      <ul v-if="suggestions.length" class="suggestion-list">
        <li v-for="(item, index) in suggestions" :key="index" @click="onSelect(item)" class="suggestion-item">
          <img v-if="item.image" :src="item.image" alt="Фото" class="suggestion-img" />
          <img v-else src="/path/to/placeholder.jpg" alt="Фото" class="suggestion-img" />
          <span> {{ item.name }}</span>
        </li>
      </ul>
    </div>

    <router-link v-if="!user" to="/signin" class="login-btn">Увійти</router-link>
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
    isAdmin() {
      return this.user && (this.user.role === 'admin' || this.user.isAdmin === true);
    }
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
      this.$router.push("/");
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
        this.$router.push({ path: '/weapons', query: { search: item.name } });
      }
      this.searchTerm = '';
      this.suggestions = [];
    },
    closeSearch(event) {
      if (!this.$refs.searchContainer.contains(event.target)) {
        this.suggestions = [];
        this.searchTerm = '';
      }
    }
  },
  mounted() {
    document.addEventListener('click', this.closeSearch);
  },
  beforeUnmount() { // Замінили beforeDestroy на beforeUnmount
    document.removeEventListener('click', this.closeSearch);
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
  position: relative;
}

.menu-list {
  list-style: none;
  display: flex;
  margin: 0;
  padding: 0;
  align-items: center; /* Вирівнює елементи меню по вертикалі */
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

.search-container {
  position: relative;
  margin-left: 20px;
  flex-grow: 1; /* Збільшує область для пошуку, щоб вона займала все доступне місце */
  display: flex;
  justify-content: flex-end; /* Вирівнює пошуковий блок праворуч */
}

.search-input {
  padding: 5px 10px;
  font-size: 16px;
  border-radius: 5px;
  border: 1px solid #ccc;
  width: 250px; /* Фіксована ширина для пошукового поля */
  box-sizing: border-box;
}

.suggestion-list {
  position: absolute;
  top: 35px;
  width: 250px;
  background: #fff;
  border: 1px solid #ccc;
  list-style: none;
  margin: 0;
  padding: 0;
  max-height: 400px;
  overflow-y: auto;
  z-index: 10;
  border-radius: 5px;
}

.suggestion-item {
  padding: 10px 15px;
  cursor: pointer;
  font-size: 14px;
}

.suggestion-item:hover {
  background-color: #eee;
}

.suggestion-img {
  width: 50px;
  height: 50px;
  object-fit: cover;
  margin-right: 15px;
  border-radius: 4px;
}

.login-btn {
  text-decoration: none;
  color: white;
  font-size: 1.2rem;
  padding: 8px 12px;
  background-color: #e74c3c;
  border-radius: 5px;
  transition: background 0.3s;
  margin-left: 20px; /* Відступ між кнопками */
}

.login-btn:hover {
  background-color: #c0392b;
}

.user-menu {
  position: relative;
  cursor: pointer;
  color: white;
  font-size: 1.2rem;
  display: flex;
  align-items: center; /* Вирівнює елементи меню користувача по вертикалі */
}

.username {
  margin-right: 15px; /* Відступ між іменем користувача і кнопкою меню */
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
