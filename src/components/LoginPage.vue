<template>
    <div class="login-container">
      <h2>Увійти</h2>
      <p>Якщо у вас вже є акаунт - <router-link to="/login">увійдіть</router-link>.</p>
      <p>Якщо немає - <router-link to="/registration">зареєструйтеся</router-link>.</p>
    </div>
  </template>
  
  <script>
  export default {
    name: 'LoginPage'
  };
  </script>
  
  <style scoped>
  .login-container {
    text-align: center;
    max-width: 400px;
    margin: 50px auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    background: #fff;
  }
  h2 {
    margin-bottom: 15px;
  }
  p {
    margin: 10px 0;
  }
  </style>
  