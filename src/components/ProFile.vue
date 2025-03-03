<template>
  <div class="profile-card">
    <!-- Кнопка "Назад" -->
    <button class="back-arrow" @click="goBack">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
    </button>

    <h2>Профіль користувача</h2>
    <p>
      Ім'я користувача: {{ username }}
      <span v-if="role === 'admin'" class="admin-badge">Адміністратор</span>
    </p>
    <p>Email: {{ email }}</p>
    
    <!-- Відображення адреси -->
    <div class="address-section">
      <div v-if="address.country && address.city && address.branch">
        <p><strong>Країна:</strong> {{ address.country }}</p>
        <p><strong>Місто/село:</strong> {{ address.city }}</p>
        <p><strong>Відділення Нової пошти:</strong> {{ address.branch }}</p>
        <p><strong>Номер телефону:</strong> {{ address.phone_number || 'Не вказано' }}</p>
      </div>
      <div v-else>
        <p>Адреса не вказана</p>
      </div>
      <button class="edit-btn" :class="{ active: isEditing }" @click="toggleEdit">
        {{ isEditing ? 'Закрити' : 'Редагувати' }}
      </button>
    </div>

    <!-- Редагування адреси -->
    <Transition name="smooth-fade">
      <div v-if="isEditing" class="edit-address">
        <h4>Введіть нову адресу</h4>
        <input type="text" v-model="newCountry" placeholder="Країна" />
        <input type="text" v-model="newCity" placeholder="Місто/село" />
        <input type="text" v-model="newBranch" placeholder="Відділення Нової пошти" />
        <input type="text" v-model="newPhoneNumber" placeholder="Номер телефону" />
        <button class="save-btn" @click="saveAddress">Зберегти</button>
      </div>
    </Transition>

    <!-- Відображення замовлень -->
    <div class="orders-section">
      <h3>Мої замовлення</h3>
      <ul v-if="orders.length">
        <li v-for="order in orders" :key="order.id">
          <!-- Натискання на замовлення відкриває/закриває деталі -->
          <div class="order-summary" @click="toggleOrderDetails(order)" style="cursor: pointer;">
            Замовлення #{{ order.id }} – Сума: {{ order.total }} – Статус: {{ order.status || 'Невідомо' }}
          </div>
          <!-- Деталі замовлення -->
          <div v-if="selectedOrder && selectedOrder.id === order.id" class="order-details">
            <h4>Товари в замовленні:</h4>
            <ul>
              <li v-for="item in order.items" :key="item.id">
                {{ item.name }} – Кількість: {{ item.quantity }} – Ціна: {{ item.price }}
              </li>
            </ul>
          </div>
        </li>
      </ul>
      <p v-else>Замовлень поки немає.</p>
    </div>

    <!-- Повідомлення про помилки чи успіх -->
    <p v-if="message" class="error">{{ message }}</p>

    <!-- Кнопка виходу -->
    <button class="logout-btn" @click="logout">Вийти</button>
  </div>
</template>

<script setup>
import { ref, onMounted, defineEmits } from 'vue';
import { useRouter } from 'vue-router';

const username = ref('');
const email = ref('');
const orders = ref([]);
const address = ref({ country: '', city: '', branch: '', phone_number: '' });
const newCountry = ref('');
const newCity = ref('');
const newBranch = ref('');
const newPhoneNumber = ref('');
const message = ref('');
const role = ref('');
const userId = ref('');
const router = useRouter();
const isEditing = ref(false);
const selectedOrder = ref(null);

const emit = defineEmits(["logout"]);

const fetchUserData = async () => {
  try {
    const response = await fetch('http://localhost/weaponshop/php/session.php', {
      credentials: 'include',
    });
    const data = await response.json();

    if (data.user) {
      username.value = data.user.username;
      email.value = data.user.email;
      role.value = data.user.role || '';
      userId.value = data.user.id;
      fetchAddress();
      fetchOrders();
    } else {
      message.value = data.message || "Помилка отримання даних.";
      setTimeout(() => router.push('/login'), 2000);
    }
  } catch (error) {
    console.error('Помилка отримання даних:', error);
    message.value = "Сталася помилка. Спробуйте пізніше.";
  }
};

const fetchAddress = async () => {
  try {
    const response = await fetch('http://localhost/weaponshop/php/address.php', {
      method: 'GET',
      credentials: 'include',
    });
    const data = await response.json();
    if (data.success && data.address) {
      address.value = data.address;
    } else {
      address.value = { country: '', city: '', branch: '', phone_number: '' };
    }
  } catch (error) {
    console.error('Помилка отримання адреси:', error);
    address.value = { country: '', city: '', branch: '', phone_number: '' };
  }
};

const fetchOrders = async () => {
  try {
    const response = await fetch('http://localhost/weaponshop/php/order.php', {
      method: 'GET',
      credentials: 'include'
    });
    const data = await response.json();
    if (data.status === "success" && data.orders) {
      orders.value = data.orders;
    } else {
      orders.value = [];
      console.error("Помилка отримання замовлень:", data.message);
    }
  } catch (error) {
    console.error('Помилка запиту замовлень:', error);
    orders.value = [];
  }
};

const toggleOrderDetails = (order) => {
  if (selectedOrder.value && selectedOrder.value.id === order.id) {
    selectedOrder.value = null;
  } else {
    selectedOrder.value = order;
  }
};

const saveAddress = async () => {
  if (!newCountry.value || !newCity.value || !newBranch.value || !newPhoneNumber.value) {
    message.value = "Будь ласка, заповніть всі поля.";
    return;
  }
  try {
    const response = await fetch('http://localhost/weaponshop/php/address.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'include',
      body: JSON.stringify({
        country: newCountry.value,
        city: newCity.value,
        branch: newBranch.value,
        phone_number: newPhoneNumber.value,
      }),
    });
    const data = await response.json();
    if (data.success) {
      address.value = data.address;
      isEditing.value = false;
      newCountry.value = '';
      newCity.value = '';
      newBranch.value = '';
      newPhoneNumber.value = '';
      message.value = "Адресу та номер телефону успішно оновлено.";
    } else {
      message.value = data.message || "Не вдалося оновити адресу.";
    }
  } catch (error) {
    console.error('Помилка збереження адреси:', error);
    message.value = "Сталася помилка. Спробуйте пізніше.";
  }
};

const logout = async () => {
  try {
    await fetch('http://localhost/weaponshop/php/logout.php', {
      method: 'POST',
      credentials: 'include'
    });

    // Очищення даних користувача
    username.value = '';
    email.value = '';
    role.value = '';
    userId.value = '';

    // Емітуємо подію logout, щоб головне меню оновилося
    emit("logout");

    // Переадресація на головну сторінку
    router.push('/');
  } catch (error) {
    console.error('Помилка при виході:', error);
  }
};

const goBack = () => {
  router.go(-1);
};

const toggleEdit = () => {
  isEditing.value = !isEditing.value;
};

onMounted(fetchUserData);
</script>


<style scoped>
/* Стилі залишаються без змін */
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
  font-family: 'Roboto', sans-serif;
}
p {
  font-size: 1.1rem;
  margin: 10px 0;
  color: #555;
}
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
  text-align: left;
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
  color: #4CAF50;
  font-weight: bold;
  margin-bottom: 15px;
}
.order-summary {
  padding: 10px;
  background: #f1f1f1;
  margin: 5px 0;
  border-radius: 5px;
}
.order-details {
  padding: 10px;
  background: #fafafa;
  border: 1px solid #ddd;
  border-radius: 5px;
  margin-bottom: 10px;
}
.smooth-fade-enter-active, .smooth-fade-leave-active {
  transition: opacity 0.4s ease, transform 0.4s ease;
}
.smooth-fade-enter-from, .smooth-fade-leave-to {
  opacity: 0;
  transform: translateY(-15px);
}
</style>
