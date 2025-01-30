<template>
  <div class="weapons-list">
    <h1>Список зброї</h1>
    <div class="weapon-item" v-for="weapon in weapons" :key="weapon.id">
      <img :src="weapon.image" :alt="weapon.name" class="weapon-image" />
      <div class="weapon-details">
        <h2>{{ weapon.name }}</h2>
        <p>{{ weapon.description }}</p>
        <p><strong>Ціна: </strong>{{ weapon.price }} грн</p>
        <p><strong>Кількість: </strong>{{ weapon.quantity }}</p>
        <button 
          @click="addToCart(weapon)" 
          :disabled="weapon.quantity <= 0"
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
      default: () => [] // Встановлення дефолтного значення
    }
  },
  data() {
    return {
      weapons: [
        {
          id: 1,
          name: 'Автомат АК-47',
          description: 'Машинна гвинтівка, автоматичний режим стрільби.',
          price: 15000,
          quantity: 10,
          image: require('@/assets/ak-47.jpg'),
        },
        {
          id: 2,
          name: 'Пістолет Glock 17',
          description: 'Полуавтоматичний пістолет для самозахисту.',
          price: 7000,
          quantity: 5,
          image: require('@/assets/glock17.png'),
        },
        {
          id: 3,
          name: 'Снайперська гвинтівка Dragunov',
          description: 'Снайперська гвинтівка з високою точністю.',
          price: 22000,
          quantity: 3,
          image: require('@/assets/dragunov.jpg'),
        },
      ]
    };
  },
  methods: {
    addToCart(weapon) {
      const existingItem = this.cart.find(item => item.id === weapon.id);
      if (existingItem) {
        existingItem.quantity++; // Збільшуємо кількість, якщо товар вже в кошику
      } else {
        this.$emit('add-to-cart', { ...weapon, quantity: 1 }); // Додаємо товар до кошика, якщо його ще немає
      }
      weapon.quantity--; // Зменшуємо кількість на складі
    },
    isInCart(weapon) {
      return this.cart.some(item => item.id === weapon.id);
    }
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
</style>
