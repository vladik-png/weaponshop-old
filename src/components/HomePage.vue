<template>
  <div class="home-page">
    <header class="hero-section">
      <h1>Ласкаво просимо до нашого магазину зброї!</h1>
      <p>
        Широкий асортимент високоякісної зброї для захисту, полювання та спорту. Ми гарантуємо найкращі ціни та професійний сервіс.
      </p>
      <router-link to="/weapons" class="cta-button">Переглянути каталог</router-link>
    </header>

    <section class="features">
      <h2>Наші переваги</h2>
      <div class="feature-list">
        <div class="feature-item">
          <span>🔫</span>
          <h3>Широкий вибір</h3>
          <p>Великий асортимент зброї від провідних виробників.</p>
        </div>
        <div class="feature-item">
          <span>💰</span>
          <h3>Доступні ціни</h3>
          <p>Конкурентні ціни та гнучкі умови оплати.</p>
        </div>
        <div class="feature-item">
          <span>🚚</span>
          <h3>Швидка доставка</h3>
          <p>Доставляємо замовлення по всій країні в найкоротші терміни.</p>
        </div>
        <div class="feature-item">
          <span>✅</span>
          <h3>Гарантія якості</h3>
          <p>Уся продукція сертифікована та перевірена на відповідність стандартам.</p>
        </div>
      </div>
    </section>

    <section class="cta-section">
      <h3>Готові зробити покупку?</h3>
      <p>Перейдіть до нашого розділу <strong><router-link to="/weapons">Зброя</router-link></strong> і знайдіть найкращі пропозиції.</p>
    </section>
  </div>
</template>

<script>
export default {
  name: 'HomePage',
};
</script>

<style scoped>
:root {
  --primary-color: #1f3b73;
  --secondary-color: #2a2e43;
  --accent-color: #ff9800;
  --bg-color: #ffffff;
  --light-bg: #f7f7f7;
  --text-color: #333;
}

.home-page {
  font-family: 'Roboto', sans-serif;
  text-align: center;
  padding: 40px 20px;
  background: var(--bg-color);
  color: var(--text-color);
  max-width: 1000px;
  margin: 0 auto;
}

.hero-section {
  background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
  padding: 50px 20px;
  border-radius: 10px;
  color: #000000;
  text-align: center;
  margin-bottom: 40px;
  transition: transform 0.3s ease;
}

.hero-section:hover {
  transform: translateY(-5px);
}

.hero-section h1 {
  font-size: 2.5em;
  margin-bottom: 10px;
}

.hero-section p {
  font-size: 1.2em;
  margin-bottom: 20px;
}

.cta-button {
  background: var(--accent-color);
  color: #ff9800;
  padding: 12px 20px;
  font-size: 1.2em;
  border-radius: 5px;
  text-decoration: none;
  transition: background 0.3s ease, transform 0.3s ease;
  display: inline-block;
  border: 2px solid #ff9800;
}

.cta-button:hover {
  background: #ffffff;
  transform: scale(1.05);
}

.features h2 {
  font-size: 2em;
  margin-bottom: 20px;
}

.feature-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  text-align: center;
}

.feature-item {
  background: var(--light-bg);
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.feature-item:hover {
  transform: translateY(-5px);
}

.feature-item span {
  font-size: 2em;
}

.feature-item h3 {
  font-size: 1.4em;
  margin: 10px 0;
}

.cta-section {
  margin-top: 40px;
  padding: 20px;
  background: var(--light-bg);
  border-radius: 8px;
  transition: transform 0.3s ease;
}

.cta-section:hover {
  transform: translateY(-5px);
}

.cta-section h3 {
  font-size: 1.8em;
  margin-bottom: 10px;
}
</style>
