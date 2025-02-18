<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const username = ref('');
const email = ref('');
const ordersCount = ref(0);
const address = ref('');
const newAddress = ref('');
const message = ref('');
const router = useRouter();
const isEditing = ref(false);

const fetchUserData = async () => {
  try {
    const response = await fetch('http://localhost/weaponshop/php/session.php', {
      credentials: 'include',
    });
    const data = await response.json();

    if (data.user) {
      username.value = data.user.username;
      email.value = data.user.email;
      ordersCount.value = data.user.ordersCount || 0;
      address.value = data.user.address || 'Адреса не вказана';
    } else {
      message.value = data.message || "Помилка отримання даних.";
      setTimeout(() => router.push('/login'), 2000);
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

const saveAddress = async () => {
  if (!newAddress.value) return;
  address.value = newAddress.value;
  isEditing.value = false;
};
</script>

<template>
  <div class="profile-card">
    <h2>Профіль користувача</h2>
    <p v-if="message" class="error">{{ message }}</p>
    <template v-if="username && email">
      <p><strong>Ім'я користувача:</strong> {{ username }}</p>
      <p><strong>Email:</strong> {{ email }}</p>
      <p><strong>Кількість замовлень:</strong> {{ ordersCount }}</p>
      
      <div class="address-section">
        <p><strong>Адреса доставки:</strong> {{ address }}</p>
        <button @click="isEditing = true" class="edit-btn">Редагувати</button>
      </div>
      
      <div v-if="isEditing" class="edit-address">
        <input v-model="newAddress" type="text" placeholder="Введіть нову адресу" />
        <button @click="saveAddress" class="save-btn">Зберегти</button>
      </div>
      
      <button @click="logout" class="logout-btn">Вийти</button>
    </template>
  </div>
</template>

<style scoped>
.profile-card {
  max-width: 500px;
  margin: 40px auto;
  padding: 20px;
  background: white;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-radius: 12px;
  text-align: center;
}

h2 {
  margin-bottom: 15px;
  font-size: 1.8rem;
  color: #333;
}

p {
  font-size: 1.1rem;
  margin: 8px 0;
}

.address-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.edit-address {
  margin-top: 10px;
}

input {
  padding: 8px;
  width: 100%;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-bottom: 10px;
}

button {
  padding: 10px 15px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
}

.edit-btn {
  background-color: #3498db;
  color: white;
}

.save-btn {
  background-color: #2ecc71;
  color: white;
}

.logout-btn {
  background-color: #e74c3c;
  color: white;
  margin-top: 20px;
  width: 100%;
}
</style>
