<template>
  <div class="weapons-list">
    <h1>Список зброї</h1>
    <div class="cart-indicator">
      Товарів у кошику: {{ cart.length }}
    </div>
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
      ],
      cart: [],
    };
  },
  methods: {
    addToCart(weapon) {
      if (weapon.quantity > 0) {
        this.cart.push({...weapon});
        weapon.quantity--;
      }
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

.cart-indicator {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 10px 20px;
  background-color: #2196F3;
  color: white;
  border-radius: 5px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
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