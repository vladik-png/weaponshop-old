<template>
    <div>
      <h2>Вхід</h2>
      <form @submit.prevent="loginUser">
        <div>
          <label>Email</label>
          <input v-model="email" type="email" />
        </div>
        <div>
          <label>Пароль</label>
          <input v-model="password" type="password" />
        </div>
        <button type="submit">Увійти</button>
      </form>
      <p v-if="message">{{ message }}</p>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        email: '',
        password: '',
        message: ''
      };
    },
    methods: {
      async loginUser() {
        try {
          const response = await fetch('http://localhost/weaponshop/php/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              email: this.email,
              password: this.password
            })
          });
          const data = await response.json();
          this.message = data.message;
  
          if (data.success) {
            // При бажанні можна зберегти user info у Vuex або localStorage
            // localStorage.setItem('user', JSON.stringify(data.user));
          }
        } catch (error) {
          console.error(error);
        }
      }
    }
  };
  </script>
  