<template>
  <div class="login-container">
    <button class="back-arrow" @click="goBack">
      <svg
        width="24"
        height="24"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <polyline points="15 18 9 12 15 6"></polyline>
      </svg>
    </button>
    <h2>Логін</h2>
    <form @submit.prevent="loginUser">
      <div class="form-group">
        <label for="email">Email</label>
        <input
          id="email"
          v-model="email"
          type="email"
          placeholder="Введіть email"
          required
        />
      </div>
      <div class="form-group">
        <label for="password">Пароль</label>
        <input
          id="password"
          v-model="password"
          type="password"
          placeholder="Введіть пароль"
          required
        />
      </div>
      <button type="submit" class="submit-btn">Увійти</button>
    </form>
    <p v-if="message" class="message">{{ message }}</p>
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
          this.$router.push('/profile');
        }
      } catch (error) {
        console.error("Помилка логіну:", error);
      }
    },
    goBack() {
      this.$router.go(-1);
    }
  }
};
</script>

<style scoped>
.login-container {
  position: relative;
  max-width: 400px;
  margin: 80px auto;
  padding: 30px;
  background: #fff;
  border-radius: 15px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
  text-align: center;
}

.login-container:hover {
  transform: translateY(-3px);
}

.back-arrow {
  position: absolute;
  top: 15px;
  left: 15px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
  outline: none;
}

.back-arrow svg {
  width: 24px;
  height: 24px;
  stroke: #4CAF50;
  transition: stroke 0.3s;
}

.back-arrow:hover svg {
  stroke: #45a049;
}

h2 {
  margin-bottom: 25px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #333;
}

.form-group {
  margin-bottom: 20px;
  text-align: left;
}

label {
  display: block;
  margin-bottom: 5px;
  font-size: 14px;
  color: #555;
  font-weight: 600;
}

input {
  width: 100%;
  padding: 12px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 5px;
  transition: border-color 0.3s;
}

input:focus {
  border-color: #4CAF50;
  outline: none;
}

.submit-btn {
  width: 100%;
  padding: 12px;
  font-size: 16px;
  background-color: #4CAF50;
  color: #fff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.submit-btn:hover {
  background-color: #45a049;
}

.message {
  margin-top: 20px;
  font-size: 16px;
  color: #333;
}

</style>
