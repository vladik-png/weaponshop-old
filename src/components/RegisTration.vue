<template>
    <div>
      <h2>Реєстрація</h2>
      <form @submit.prevent="registerUser">
        <div>
          <label>Ім'я користувача</label>
          <input v-model="username" />
        </div>
        <div>
          <label>Email</label>
          <input v-model="email" type="email" />
        </div>
        <div>
          <label>Пароль</label>
          <input v-model="password" type="password" />
        </div>
        <button type="submit">Зареєструватися</button>
      </form>
      <p v-if="message">{{ message }}</p>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        username: '',
        email: '',
        password: '',
        message: ''
      };
    },
    methods: {
      async registerUser() {
        try {
          const response = await fetch('http://localhost/weaponshop/php/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              username: this.username,
              email: this.email,
              password: this.password
            })
          });
          const data = await response.json();
          this.message = data.message;
        } catch (error) {
          console.error(error);
        }
      }
    }
  };
  </script>
  