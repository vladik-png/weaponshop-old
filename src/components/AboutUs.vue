<template>
  <div class="about-us-page">
    <h1>Про нас</h1>
    <p class="intro-text">
      Ми — команда професіоналів, яка прагне зробити ваш досвід покупки зброї безпечним та комфортним. Наш магазин спеціалізується на наданні високоякісної зброї для різноманітних потреб — від захисту до спортивної стрільби.
    </p>

    <div class="company-history">
      <h2>Наша історія</h2>
      <p>
        Наш магазин було засновано з метою забезпечити наших клієнтів найкращою зброєю, яка відповідає всім стандартам безпеки та якості. З кожним роком ми розширюємо наш асортимент, прагнучи задовольнити потреби кожного клієнта.
      </p>
      <p>
        Від самого початку ми орієнтувалися на забезпечення безпеки та надійності кожного товару, що продається в нашому магазині. Наші фахівці працюють лише з перевіреними постачальниками, щоб кожен товар відповідав найвищим стандартам.
      </p>
    </div>

    <div class="our-values">
      <h2>Наші цінності</h2>
      <ul class="values-list">
        <li>Безпека: Пріоритет номер один — безпечне використання нашої зброї.</li>
        <li>Якість: Ми пропонуємо тільки сертифіковану та надійну продукцію.</li>
        <li>Клієнтський сервіс: Наші консультанти завжди готові допомогти вам у виборі зброї.</li>
        <li>Доступність: Пропонуємо конкурентні ціни на всі товари.</li>
      </ul>
    </div>

    <div class="cta-section">
      <p>За більше інформації про наші продукти, відвідайте розділ <strong><router-link to="/weapons">Зброя</router-link></strong> або зв'яжіться з нами через <strong><router-link to="/contact">Контакти</router-link></strong>.</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AboutUs',
};
</script>

<style scoped>
.about-us-page {
  font-family: 'Arial', sans-serif;
  padding: 20px;
  text-align: center;
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

.company-history {
  background-color: #f9f9f9;
  padding: 20px;
  margin: 30px 0;
  border-radius: 8px;
}

.company-history h2 {
  font-size: 2em;
  color: #2e3b4e;
  margin-bottom: 15px;
}

.company-history p {
  font-size: 1.1em;
  color: #333;
  line-height: 1.6;
  margin-bottom: 20px;
}

.our-values {
  background-color: #f4f4f4;
  padding: 20px;
  margin: 30px 0;
  border-radius: 8px;
}

.our-values h2 {
  font-size: 2em;
  color: #2e3b4e;
  margin-bottom: 15px;
}

.values-list {
  list-style-type: none;
  padding: 0;
}

.values-list li {
  font-size: 1.2em;
  color: #333;
  margin: 10px 0;
}

.cta-section {
  margin-top: 40px;
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
