<template>
  <div id="app">
    <MainMenu />
<<<<<<< HEAD
=======
<<<<<<< HEAD
    <router-view></router-view>
=======
>>>>>>> back-end
    <router-view :cart="cart" @add-to-cart="addToCart" /> <!-- Передача cart через router-view -->
    <CartPay :cart="cart" @clear-cart="clearCart" />
    <FooterDown />
    <div v-if="cart.length === 0" class="empty-cart-message">
      Кошик порожній.
    </div>
<<<<<<< HEAD
=======
>>>>>>> front-end
>>>>>>> back-end
  </div>
</template>

<script>
import MainMenu from './components/MainMenu.vue';
<<<<<<< HEAD
import FooterDown from './components/FooterDown.vue';
import CartPay from './components/CartPay.vue';
=======
<<<<<<< HEAD
=======
import FooterDown from './components/FooterDown.vue';
import CartPay from './components/CartPay.vue';
>>>>>>> front-end
>>>>>>> back-end

export default {
  name: 'App',
  components: {
    MainMenu,
<<<<<<< HEAD
    FooterDown,
    CartPay
  },
=======
<<<<<<< HEAD
  },
=======
    FooterDown,
    CartPay
  },
>>>>>>> back-end
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
<<<<<<< HEAD
=======
>>>>>>> front-end
>>>>>>> back-end
};
</script>

<style>
<<<<<<< HEAD
=======
<<<<<<< HEAD
/* Можна додати стилі для загального вигляду */
=======
>>>>>>> back-end
.empty-cart-message {
  text-align: center;
  font-size: 1.5em;
  color: #ff0000;
}
<<<<<<< HEAD
=======
>>>>>>> front-end
>>>>>>> back-end
</style>
