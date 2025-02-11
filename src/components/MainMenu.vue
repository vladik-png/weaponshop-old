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
  </nav>
</template>

<script>
export default {
  name: "MainMenu",
  data() {
    return {
      menuItems: [
<<<<<<< HEAD
        { id: 1, name: "Головна", link: "/" },
=======
        { id: 1, name: "Головна ", link: "/" },
>>>>>>> front-end
        { id: 2, name: "Зброя", link: "/weapons" },
        { id: 3, name: "Про нас", link: "/about" },
        { id: 4, name: "Контакти", link: "/contact" },
      ],
    };
  },
};
</script>

<style scoped>
.main-menu {
  background-color: #2c3e50;
  padding: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.menu-list {
  list-style: none;
  display: flex;
  justify-content: space-around;
  margin: 0;
  padding: 0;
}

.menu-item {
  font-size: 1.2rem;
}

.menu-link {
  text-decoration: none;
  color: white; /* Колір за замовчуванням */
  transition: color 0.3s;
}

.menu-link:hover {
  color: #f39c12; /* Колір при наведенні */
}

/* Активний маршрут */
.menu-link.router-link-active {
  color: #e74c3c; /* Колір для активного посилання */
}

/* Зміна кольору при натисканні */
.menu-link:active {
  color: #2980b9;
}
</style>
