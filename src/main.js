import { createApp } from 'vue';
import App from './App.vue'; // Головний компонент додатку
import router from './router';

createApp(App)
  .use(router)
  .mount('#app');
