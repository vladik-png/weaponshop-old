<template>
    <div class="login">
      <h2>Логін</h2>
      <form @submit.prevent="loginUser">
        <div>
          <label>Email</label>
          <input v-model="email" type="email" required />
        </div>
        <div>
          <label>Пароль</label>
          <input v-model="password" type="password" required />
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
            credentials: 'include',
            body: JSON.stringify({
              email: this.email,
              password: this.password
            })
          });
  
          const data = await response.json();
          this.message = data.message;
  
          if (data.user) {
            this.$emit('login-success', data.user); // Передаємо дані користувача у батьківський компонент
            this.$router.push('/');
          }
        } catch (error) {
          console.error("Помилка логіну:", error);
        }
      }
    }
  };
  </script>
  
  <style scoped>
  .login {
    max-width: 400px;
    margin: auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  input {
    padding: 0.5rem;
    font-size: 1rem;
    width: 100%;
  }
  button {
    padding: 0.5rem;
    font-size: 1rem;
    cursor: pointer;
  }
  </style>
  