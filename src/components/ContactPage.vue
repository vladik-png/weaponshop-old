<template>
  <div class="contact-page">
    <h1>Контакти</h1>
    <p class="contact-description">
      Ми завжди готові допомогти вам! Якщо у вас є питання або потреба в консультації, заповніть форму нижче або зв'яжіться з нами за вказаними контактами.
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
      showModal: false, // Для спливаючого вікна
    };
  },
  methods: {
    submitForm() {
      // Відправка електронної пошти
      this.sendEmail(); // Викликаємо функцію відправки
      console.log('Форма відправлена:', this.form);

      // Показати спливаюче вікно
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

      // Використовуємо EmailJS для відправки електронної пошти
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
/* Стилі для контактної сторінки та форми */
.contact-page {
  font-family: 'Arial', sans-serif;
  padding: 20px;
  text-align: center;
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
  background-color: #f4f4f4;
  padding: 20px;
  margin: 30px 0;
  border-radius: 8px;
}

.contact-form h2 {
  font-size: 2em;
  color: #2e3b4e;
  margin-bottom: 15px;
}

.contact-form .form-group {
  margin-bottom: 15px;
  text-align: left;
}

.contact-form label {
  font-size: 1.1em;
  color: #333;
}

.contact-form input,
.contact-form textarea {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
  font-size: 1em;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.contact-form button {
  padding: 10px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 1.1em;
  cursor: pointer;
}

.contact-form button:hover {
  background-color: #0056b3;
}

/* Стилі для спливаючого вікна */
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
}

.modal-content button {
  padding: 10px 20px;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.modal-content button:hover {
  background-color: #218838;
}
</style>
