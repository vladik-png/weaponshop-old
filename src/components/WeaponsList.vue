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
          image: require('@/assets/ak-47.jpg'), // Шлях до зображення
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
    };
  },
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
</style>
