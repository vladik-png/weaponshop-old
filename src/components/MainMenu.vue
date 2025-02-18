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
      ]
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
