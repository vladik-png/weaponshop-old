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
  import { useRouter } from 'vue-router';
  
  export default {
    data() {
      return {
        email: '',
        password: '',
        message: ''
      };
    },
    setup() {
      const router = useRouter();
      return { router };
    },
    methods: {
      async loginUser() {
        console.log("Метод loginUser викликаний. Email:", this.email, "Пароль:", this.password);
        try {
          const response = await fetch('http://localhost/weaponshop/php/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include', // ✅ Додаємо, щоб кукі передавались
            body: JSON.stringify({
              email: this.email,
              password: this.password
            })
          });
  
          const data = await response.json();
          console.log("Відповідь сервера:", data);
          this.message = data.message;
  
          // ✅ Перевірка перед редіректом
          if (data.user) {
            setTimeout(() => {
              this.router.push('/profile');
            }, 200);
          }
        } catch (error) {
          console.error("Помилка логіну:", error);
          this.message = "Сталася помилка. Будь ласка, спробуйте пізніше.";
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
  