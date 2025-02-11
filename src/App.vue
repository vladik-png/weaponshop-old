<template>
  <div id="app">
    <MainMenu />
    <router-view :cart="cart" @add-to-cart="addToCart" /> <!-- Передача cart через router-view -->
    <CartPay :cart="cart" @clear-cart="clearCart" />
    <FooterDown />
    <div v-if="cart.length === 0" class="empty-cart-message">
    </div>
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
    };
  },
  methods: {
    addToCart(item) {
      this.cart.push(item);
    },
    clearCart() {
      this.cart = [];
    }
  }
};
</script>

<style>
.empty-cart-message {
  text-align: center;
  font-size: 1.5em;
  color: #ff0000;
}
</style>
