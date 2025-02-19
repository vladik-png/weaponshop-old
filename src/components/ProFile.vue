<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const username = ref('');
const email = ref('');
const ordersCount = ref(0);
const address = ref('');
const newAddress = ref('');
const message = ref('');
const role = ref(''); // зберігаємо роль користувача
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
      role.value = data.user.role || ''; // зберігаємо роль (наприклад, "admin")
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
    router.push('/'); // Перенаправлення на головну сторінку
  } catch (error) {
    console.error('Помилка при виході:', error);
  }
};

const saveAddress = async () => {
  if (!newAddress.value) return;
  address.value = newAddress.value;
  isEditing.value = false;
};

const goBack = () => {
  router.go(-1);
};
</script>

<template>
  <div class="profile-card">
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
    <h2>Профіль користувача</h2>
    <p v-if="message" class="error">{{ message }}</p>
    <template v-if="username && email">
      <p>
        <strong>Ім'я користувача:</strong> {{ username }}
        <!-- Відображення значка адміністратора, якщо роль "admin" -->
        <span v-if="role === 'admin'" class="admin-badge">Адміністратор</span>
      </p>
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
  position: relative;
  max-width: 500px;
  margin: 60px auto;
  padding: 30px;
  background: #fff;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  border-radius: 15px;
  text-align: center;
  transition: transform 0.3s ease;
}

.profile-card:hover {
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
  stroke: #3498db;
  transition: stroke 0.3s;
}

.back-arrow:hover svg {
  stroke: #217dbb;
}

h2 {
  margin-bottom: 20px;
  font-size: 2rem;
  color: #333;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

p {
  font-size: 1.1rem;
  margin: 10px 0;
  color: #555;
}

/* Стиль значка адміністратора */
.admin-badge {
  background-color: #ff0000;
  color: #fff;
  font-size: 0.8rem;
  padding: 3px 6px;
  border-radius: 3px;
  margin-left: 8px;
  vertical-align: middle;
}

.address-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 15px;
}

.edit-address {
  margin-top: 15px;
}

input {
  padding: 10px;
  width: 100%;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 1rem;
  margin-bottom: 10px;
  transition: border-color 0.3s;
}

input:focus {
  border-color: #3498db;
  outline: none;
}

button {
  padding: 10px 15px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
  font-size: 1rem;
  transition: background-color 0.3s;
}

.edit-btn {
  background-color: #3498db;
  color: #fff;
}

.edit-btn:hover {
  background-color: #217dbb;
}

.save-btn {
  background-color: #2ecc71;
  color: #fff;
}

.save-btn:hover {
  background-color: #27ae60;
}

.logout-btn {
  background-color: #e74c3c;
  color: #fff;
  margin-top: 25px;
  width: 100%;
}

.logout-btn:hover {
  background-color: #c0392b;
}

.error {
  color: #e74c3c;
  font-weight: bold;
  margin-bottom: 15px;
}

</style>
