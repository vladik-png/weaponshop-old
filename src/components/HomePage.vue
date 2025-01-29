<template>
  <div class="home-page">
    <h1>Ласкаво просимо до нашого магазину зброї!</h1>
    <p class="intro-text">
      У нашому магазині ви знайдете широкий асортимент зброї для будь-якої мети. Від автоматичних гвинтівок до пістолетів — у нас є все для захисту, полювання та спорту. Оцініть високий рівень якості та безпеки наших товарів, що відповідають міжнародним стандартам.
    </p>

    <div class="feature-section">
      <h2>Особливості нашого магазину</h2>
      <ul class="feature-list">
        <li>Великий вибір зброї від найкращих виробників.</li>
        <li>Конкурентоспроможні ціни.</li>
        <li>Швидка доставка по всій країні.</li>
        <li>Гарантія якості на всю продукцію.</li>
        <li>Консультації експертів перед покупкою.</li>
      </ul>
    </div>

    <div class="cta-section">
      <h3>Готові зробити покупку?</h3>
      <p>Перейдіть до нашого розділу <strong><router-link to="/weapons">Зброя</router-link></strong> і знайдіть те, що вам потрібно!</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HomePage',
};
</script>

<style scoped>
.home-page {
  font-family: 'Arial', sans-serif;
  text-align: center;
  padding: 20px;
}

h1 {
  font-size: 2.5em;
  color: #2e3b4e;
  margin-bottom: 20px;
}

.intro-text {
  font-size: 1.2em;
  color: #555;
  line-height: 1.6;
  max-width: 800px;
  margin: 0 auto 30px auto;
}

.feature-section {
  background-color: #f4f4f4;
  padding: 20px;
  margin: 30px 0;
  border-radius: 8px;
}

.feature-section h2 {
  font-size: 2em;
  color: #2e3b4e;
  margin-bottom: 15px;
}

.feature-list {
  list-style-type: none;
  padding: 0;
}

.feature-list li {
  font-size: 1.1em;
  color: #333;
  margin: 10px 0;
}

.cta-section {
  margin-top: 40px;
}

.cta-section h3 {
  font-size: 1.8em;
  color: #333;
}

.cta-section p {
  font-size: 1.1em;
  color: #555;
}

router-link {
  color: #007bff;
  text-decoration: none;
  font-weight: bold;
}

router-link:hover {
  text-decoration: underline;
}
</style>
