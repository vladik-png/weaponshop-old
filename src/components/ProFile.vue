<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const username = ref('');
const email = ref('');
const message = ref('');
const router = useRouter();

const fetchUserData = async () => {
  try {
    const response = await fetch('http://localhost/weaponshop/php/session.php', {
      credentials: 'include',
    });
    const data = await response.json();

    console.log("Дані користувача:", data);

    if (data.user) {
      username.value = data.user.username;
      email.value = data.user.email;
    } else {
      message.value = data.message || "Помилка отримання даних.";
      setTimeout(() => router.push('/login'), 2000); // ✅ Редірект через 2 секунди
    }
  } catch (error) {
    console.error('Помилка отримання даних:', error);
    message.value = "Сталася помилка. Спробуйте пізніше.";
  }
};

onMounted(fetchUserData);

const logout = async () => {
  try {
    await fetch('http://localhost/weaponshop/php/logout.php', {
      method: 'POST',
      credentials: 'include',
    });
    router.push('/login');
  } catch (error) {
    console.error('Помилка при виході:', error);
  }
};
</script>

<template>
  <div class="profile">
    <h2>Профіль користувача</h2>
    <p v-if="message">{{ message }}</p>
    <template v-if="username && email">
      <p><strong>Email:</strong> {{ email }}</p>
      <p><strong>Ім'я користувача:</strong> {{ username }}</p>
      <p>Ласкаво просимо до вашого профілю!</p>
      <button @click="logout">Вийти</button>
    </template>
  </div>
</template>

<style scoped>
.profile {
  max-width: 400px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f0f0f0;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  border-radius: 5px;
}
h2 {
  text-align: center;
  margin-bottom: 20px;
}
button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 3px;
  cursor: pointer;
  width: 100%;
}
button:hover {
  background-color: #0056b3;
}
</style>
