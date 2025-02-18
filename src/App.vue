<template> 
  <div id="app">
    <MainMenu :user="user" @logout="logout" />
    <router-view :cart="cart" @add-to-cart="addToCart" @login-success="checkAuth" />
    <CartPay v-if="!isAdminPage" :cart="cart" @clear-cart="clearCart" />
    <FooterDown v-if="!isAdminPage" />
    <div v-if="cart.length === 0" class="empty-cart-message"></div>
  </div>
</template>

<script>
import MainMenu from './components/MainMenu.vue';
import FooterDown from './components/FooterDown.vue';
import CartPay from './components/CartPay.vue';

export default {
  name: 'App',
  components: {
    MainMenu,
    FooterDown,
    CartPay
  },
  data() {
    return {
      cart: [],
      user: null
    };
  },
  computed: {
    isAdminPage() {
      return this.$route.path.startsWith('/admin');
    }
  },
  methods: {
    addToCart(item) {
      this.cart.push(item);
    },
    clearCart() {
      this.cart = [];
    },
    async checkAuth() {
      try {
        const response = await fetch('http://localhost/weaponshop/php/session.php', {
          credentials: 'include',
        });
        const data = await response.json();

        this.user = data.user ? data.user : null;
      } catch (error) {
        console.error('Помилка перевірки авторизації:', error);
        this.user = null;
      }
    },
    async logout() {
      try {
        await fetch('http://localhost/weaponshop/php/logout.php', {
          method: 'POST',
          credentials: 'include',
        });
        this.user = null;
      } catch (error) {
        console.error('Помилка при виході:', error);
      }
    }
  },
  mounted() {
    this.checkAuth();
  }
};
</script>
