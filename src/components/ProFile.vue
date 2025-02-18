<template>
    <div>
      <h2>Профіль користувача</h2>
      <div v-if="user">
        <p>ID: {{ user.id }}</p>
        <p>Ім'я: {{ user.username }}</p>
        <p>Email: {{ user.email }}</p>
        <p>Зареєстровано: {{ user.created_at }}</p>
      </div>
      <p v-else>Будь ласка, увійдіть.</p>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        user: null
      };
    },
    async created() {
      try {
        const response = await fetch('http://localhost/weaponshop/profile.php');
        const data = await response.json();
        if (data.success) {
          this.user = data.user;
        }
      } catch (error) {
        console.error(error);
      }
    }
  };
  </script>
  