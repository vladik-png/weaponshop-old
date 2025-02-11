<template>
  <div id="app">
    <MainMenu />
<<<<<<< HEAD
    <router-view></router-view>
=======
    <router-view :cart="cart" @add-to-cart="addToCart" /> <!-- Передача cart через router-view -->
    <CartPay :cart="cart" @clear-cart="clearCart" />
    <FooterDown />
    <div v-if="cart.length === 0" class="empty-cart-message">
      Кошик порожній.
    </div>
>>>>>>> front-end
  </div>
</template>

<script>
import MainMenu from './components/MainMenu.vue';
<<<<<<< HEAD
=======
import FooterDown from './components/FooterDown.vue';
import CartPay from './components/CartPay.vue';
>>>>>>> front-end

export default {
  name: 'App',
  components: {
    MainMenu,
<<<<<<< HEAD
  },
=======
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
>>>>>>> front-end
};
</script>

<style>
<<<<<<< HEAD
/* Можна додати стилі для загального вигляду */
=======
.empty-cart-message {
  text-align: center;
  font-size: 1.5em;
  color: #ff0000;
}
>>>>>>> front-end
</style>
