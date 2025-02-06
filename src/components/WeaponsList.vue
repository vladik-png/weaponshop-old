<template>
  <div class="weapons-list">
    <h1>Список зброї</h1>
    <div v-if="loading">Завантаження...</div>
    <div v-if="error" class="error">{{ error }}</div>
    <div class="weapon-item" v-for="weapon in weapons" :key="weapon.id">
      <img :src="weapon.image" :alt="weapon.name" class="weapon-image" />
      <div class="weapon-details">
        <h2>{{ weapon.name }}</h2>
        <p>{{ weapon.descruption }}</p>
        <p><strong>Ціна: </strong>{{ weapon.price }} грн</p>
        <p><strong>Наявність: </strong>{{ weapon.ammo }}</p>
        <button 
          @click="addToCart(weapon)" 
          :disabled="weapon.ammo <= 0"
          class="add-to-cart-btn"
        >
          Додати до кошика
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'WeaponsList',
  props: {
    cart: {
      type: Array,
      required: true,
      default: () => []
    }
  },
  data() {
    return {
      weapons: [],
      loading: false,
      error: null
    };
  },
  created() {
    this.fetchWeapons();
  },
  methods: {
    async fetchWeapons() {
      this.loading = true;
      try {
        const response = await fetch('http://127.0.0.1/weaponshop/php/weapon.php');
        if (!response.ok) throw new Error('Помилка завантаження зброї');
        this.weapons = await response.json();
      } catch (err) {
        this.error = err.message;
      } finally {
        this.loading = false;
      }
    },
    addToCart(weapon) {
      const existingItem = this.cart.find(item => item.id === weapon.id);
      if (existingItem) {
        existingItem.quantity++;
      } else {
        this.$emit('add-to-cart', { ...weapon, quantity: 1 });
      }
      weapon.ammo--;
    }
  },
  getImagePath(img) {
    return require(`@/assets/${img}`);
  }
};
</script>

<style scoped>
.weapons-list {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.weapon-item {
  display: flex;
  align-items: center;
  margin: 20px;
  border: 1px solid #ccc;
  padding: 10px;
  border-radius: 5px;
  width: 80%;
}

.weapon-image {
  width: 200px;
  height: auto;
  margin-right: 20px;
}

.weapon-details {
  flex-grow: 1;
}

.weapon-details h2 {
  font-size: 1.5em;
  margin-bottom: 10px;
}

.weapon-details p {
  margin: 5px 0;
}

.add-to-cart-btn {
  padding: 8px 16px;
  margin-top: 10px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.add-to-cart-btn:hover {
  background-color: #45a049;
}

.add-to-cart-btn:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.error {
  color: red;
  font-weight: bold;
  margin-top: 20px;
}
</style>
