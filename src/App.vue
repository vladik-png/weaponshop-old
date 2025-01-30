<template>
  <div id="app">
    <MainMenu />
    <router-view></router-view>
    <FooterDown />
  </div>
</template>

<script>
import MainMenu from './components/MainMenu.vue';
import FooterDown from './components/FooterDown.vue';

export default {
  name: 'App',
  components: {
    MainMenu,
    FooterDown
  }
};

</script>

<style>
/* Можна додати стилі для загального вигляду */
</style>
