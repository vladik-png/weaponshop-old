import { createRouter, createWebHistory } from 'vue-router';
import HomePage from '../components/HomePage.vue';
import WeaponsList from '../components/WeaponsList.vue';
import AboutUs from '../components/AboutUs.vue';
import ContactPage from '../components/ContactPage.vue';
import CartPay from '../components/CartPay.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomePage,
  },
  {
    path: '/weapons',
    name: 'Weapons',
    component: WeaponsList,
  },
  {
    path: '/about',
    name: 'About',
    component: AboutUs,
  },
  {
    path: '/contact',
    name: 'Contact',
    component: ContactPage,
  },
  {
    path: '/cart',
    name: 'Cart',
    component: CartPay,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
