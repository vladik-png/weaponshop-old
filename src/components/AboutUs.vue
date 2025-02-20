<template>
  <div class="about-us-page">
    <div class="header">
      <h1>Про нас</h1>
    </div>
    <p class="intro-text">
      Ми — команда професіоналів, яка прагне зробити ваш досвід покупки зброї безпечним та комфортним. Наш магазин спеціалізується на наданні високоякісної зброї для різноманітних потреб — від захисту до спортивної стрільби.
    </p>

    <section class="company-history">
      <h2>Наша історія</h2>
      <p>
        Наш магазин було засновано з метою забезпечити наших клієнтів найкращою зброєю, яка відповідає всім стандартам безпеки та якості. З кожним роком ми розширюємо наш асортимент, прагнучи задовольнити потреби кожного клієнта.
      </p>
      <p>
        Від самого початку ми орієнтувалися на забезпечення безпеки та надійності кожного товару, що продається в нашому магазині. Наші фахівці працюють лише з перевіреними постачальниками, щоб кожен товар відповідав найвищим стандартам.
      </p>
    </section>

    <section class="our-values">
      <h2>Наші цінності</h2>
      <ul>
        <li>Безпека: Пріоритет номер один — безпечне використання нашої зброї.</li>
        <li>Якість: Ми пропонуємо тільки сертифіковану та надійну продукцію.</li>
        <li>Клієнтський сервіс: Наші консультанти завжди готові допомогти вам у виборі зброї.</li>
        <li>Доступність: Пропонуємо конкурентні ціни на всі товари.</li>
      </ul>
    </section>

    <section class="cta-section">
      <p>
        За більше інформації про наші продукти, відвідайте розділ 
        <strong><router-link to="/weapons">Зброя</router-link></strong> 
        або зв'яжіться з нами через 
        <strong><router-link to="/contact">Контакти</router-link></strong>.
      </p>
    </section>
  </div>
</template>

<script>
export default {
  name: 'AboutUs',
};
</script>

<style scoped>
:root {
  --primary-color: #1f3b73;
  --secondary-color: #2a2e43;
  --accent-color: #e74c3c;
  --bg-color: #ffffff;
  --light-bg: #f7f7f7;
  --text-color: #333333;
}

.about-us-page {
  font-family: 'Roboto', sans-serif;
  padding: 40px 20px;
  background: var(--bg-color);
  color: var(--text-color);
  max-width: 1000px;
  margin: 0 auto;
}

.header {
  background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
  padding: 40px 20px;
  border-radius: 8px;
  color: #fff;
  text-align: center;
  margin-bottom: 30px;
}

h1 {
  font-size: 2.5em;
  margin: 0;
  color: black;
}

.intro-text {
  font-size: 1.1em;
  line-height: 1.6;
  text-align: center;
  margin: 30px auto;
  max-width: 800px;
}

section {
  margin: 40px 0;
  padding: 20px;
  background: var(--light-bg);
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

section h2 {
  font-size: 2em;
  margin-bottom: 20px;
  color: var(--primary-color);
}

section p {
  font-size: 1.1em;
  line-height: 1.6;
  margin-bottom: 20px;
}

.our-values ul {
  list-style: none;
  padding: 0;
}

.our-values li {
  font-size: 1.1em;
  margin: 10px 0;
  padding-left: 25px;
  position: relative;
}

.our-values li::before {
  content: '✓';
  position: absolute;
  left: 0;
  color: var(--accent-color);
}

.cta-section {
  text-align: center;
  margin-top: 40px;
}

.cta-section p {
  font-size: 1.2em;
  line-height: 1.6;
}

router-link {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: bold;
  transition: color 0.3s ease;
}

router-link:hover {
  color: var(--accent-color);
}

</style>
