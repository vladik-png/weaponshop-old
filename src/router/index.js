import { createRouter, createWebHistory } from 'vue-router';
import HomePage from '../components/HomePage.vue';
import WeaponsList from '../components/WeaponsList.vue';
import AboutUs from '../components/AboutUs.vue';
import ContactPage from '../components/ContactPage.vue';
import CartPay from '../components/CartPay.vue';
import RegisTration from '@/components/RegisTration.vue';
import LoGin from '@/components/LoGin.vue';
import LoginPage from '@/components/LoginPage.vue';
import ProFile from "@/components/ProFile.vue";
import AdminPanel from "@/components/AdminPanel.vue";
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
  {
    path: '/registration',
    name: 'Registration',
    component: RegisTration,
  },
  {
    path: '/login',
    name: 'Login',
    component: LoGin,
  },
  { path: '/signin',
    name: 'SignIn',
    component: LoginPage,
  },
  { path: '/profile',
    name: 'Profile',
    component: ProFile,
  },
  { path: '/admin',
    name: 'Admin',
    component: AdminPanel,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;