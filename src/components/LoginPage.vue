<template>
  <div class="login-container">
    <h2>Увійти</h2>
    <p>
      Якщо у вас вже є акаунт -
      <router-link to="/login" class="link">увійдіть</router-link>.
    </p>
    <p>
      Якщо немає -
      <router-link to="/registration" class="link">зареєструйтеся</router-link>.
    </p>
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
  margin: 80px auto;
  padding: 30px;
  border: none;
  border-radius: 15px;
  background: #ffffff;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.login-container:hover {
  transform: translateY(-5px);
}

h2 {
  margin-bottom: 20px;
  color: #333;
  font-family: 'Roboto', sans-serif;
}

p {
  margin: 15px 0;
  font-size: 16px;
  color: #666;
  line-height: 1.5;
}

.link {
  color: #3498db;
  text-decoration: none;
  font-weight: bold;
  transition: color 0.3s ease;
}

.link:hover {
  color: #217dbb;
}

</style>
