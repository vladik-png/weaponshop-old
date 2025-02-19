<template>
  <div class="contact-page">
    <h1>Контакти</h1>
    <p class="contact-description">
      Ми завжди готові допомогти вам! Якщо у вас є питання або потреба в консультації, заповніть форму нижче або зв'яжіться з нами за вказаними контактами.
    </p>
    
    <p class="contact-info">
      📍 Адреса: вул. Тарабалки Степана, 20, м.Коломия, Івано-Франківська обл., 78203<br>
      ☎ Телефон: (03433) 4 - 77 - 26<br>
      ✉ Email: support@weaponshop.com<br>
      🕒 Години роботи: Пн-Пт: 10:00 - 18:00, Сб-Нд: вихідний
    </p>

    <div class="contact-form">
      <h2>Залиште своє повідомлення</h2>
      <form @submit.prevent="submitForm">
        <div class="form-group">
          <label for="name">Ваше ім'я</label>
          <input type="text" id="name" v-model="form.name" required />
        </div>

        <div class="form-group">
          <label for="email">Ваш email</label>
          <input type="email" id="email" v-model="form.email" required />
        </div>

        <div class="form-group">
          <label for="message">Повідомлення</label>
          <textarea id="message" v-model="form.message" required></textarea>
        </div>

        <button type="submit" class="submit-btn">Відправити</button>
      </form>
    </div>

    <!-- Спливаюче повідомлення -->
    <div v-if="showModal" class="modal">
      <div class="modal-content">
        <p>Ваше повідомлення відправлено!</p>
        <button @click="closeModal">Закрити</button>
      </div>
    </div>
  </div>


    <div class="map-container">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1303.7588579640371!2d25.041431651290047!3d48.53273961853463!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4736d29057abf5b3%3A0xbe99f0c2dee6921f!2z0JrQvtC70L7QvNC40LnRgdGM0LrQuNC5INC_0L7Qu9GW0YLQtdGF0L3RltGH0L3QuNC5INGE0LDRhdC-0LLQuNC5INC60L7Qu9C10LTQtiDQndCw0YbRltC-0L3QsNC70YzQvdC-0LPQviDRg9C90ZbQstC10YDRgdC40YLQtdGC0YMgwqvQm9GM0LLRltCy0YHRjNC60LAg0L_QvtC70ZbRgtC10YXQvdGW0LrQsMK7!5e1!3m2!1suk!2sua!4v1739955090276!5m2!1suk!2sua"
        width="600"
        height="450"
        style="border:0;"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
      </iframe> 
      
    </div>
</template>

<script>
import emailjs from 'emailjs-com';

export default {
  name: 'ContactPage',
  data() {
    return {
      form: {
        name: '',
        email: '',
        message: '',
      },
      showModal: false,
    };
  },
  methods: {
    submitForm() {
      this.sendEmail();
      console.log('Форма відправлена:', this.form);
      this.showModal = true;
      this.resetForm();
    },
    resetForm() {
      this.form.name = '';
      this.form.email = '';
      this.form.message = '';
    },
    closeModal() {
      this.showModal = false;
    },
    sendEmail() {
      const emailData = {
        from_name: this.form.name,
        from_email: this.form.email,
        message: this.form.message,
      };
      emailjs.send('service_3hl50gn', 'template_uzyroa8', emailData, 'AUPCw9H6RMRp-xYbl')
        .then(response => {
          console.log('Email sent successfully', response);
        })
        .catch(error => {
          console.error('Error sending email', error);
        });
    },
  },
};
</script>

<style scoped>
.contact-page {
  font-family: 'Arial', sans-serif;
  padding: 20px;
  text-align: center;
  background: linear-gradient(135deg, #eceff1, #f5f7fa);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

h1 {
  font-size: 2.5em;
  color: #2e3b4e;
  margin-bottom: 20px;
}

.contact-description {
  font-size: 1.2em;
  color: #555;
  line-height: 1.6;
  max-width: 800px;
  margin: 0 auto 30px auto;
}

.contact-form {
  background-color: white;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  max-width: 500px;
  width: 100%;
}

.contact-form h2 {
  font-size: 1.8em;
  color: #2e3b4e;
  margin-bottom: 15px;
}

.form-group {
  margin-bottom: 15px;
  text-align: left;
}

label {
  font-size: 1.1em;
  color: #333;
  display: block;
  margin-bottom: 5px;
}

input,
textarea {
  width: 100%;
  padding: 10px;
  font-size: 1em;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.submit-btn {
  padding: 12px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 1.1em;
  cursor: pointer;
  width: 100%;
}

.submit-btn:hover {
  background-color: #0056b3;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
  max-width: 400px;
  width: 100%;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.modal-content button {
  padding: 10px 20px;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin-top: 10px;
}

.modal-content button:hover {
  background-color: #218838;
}

.map-container {
  max-width: 600px;
  margin: 20px auto;
  border-radius: 8px;
  overflow: hidden;
}
</style>
