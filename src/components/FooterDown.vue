<template>
    <footer class="footer">
      <div class="footer-container">
        <div class="footer-left">
          <img src="@/assets/logo.png" alt="Логотип" class="logo">
          <span class="tagline">ЗА КРОК ДО ПРИГОД</span>
        </div>
        <div class="footer-center">
          <p>Всі права захищені © 2025</p>
        </div>
        <div class="footer-right">
          <img src="@/assets/visa.svg" alt="Visa" class="icon">
          <img src="@/assets/mastercard.svg" alt="Mastercard" class="icon">
          <img src="@/assets/NovaPoshta.png" alt="Нова Пошта" class="icon">
        </div>
      </div>
    </footer>
  </template>
  
  <style scoped>
  .footer {
    background-color: #f5f7f7;
    padding: 15px 0;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
  }
  
  .footer-container {
    width: 90%;
    max-width: 1200px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .footer-left {
    display: flex;
    align-items: center;
  }
  
  .logo {
    height: 40px;
    margin-right: 10px;
  }
  
  .tagline {
    color: #f36b21;
    font-weight: bold;
  }
  
  .footer-center {
    text-align: center;
    color: #666;
    font-size: 14px;
  }
  
  .company-name {
    color: #f36b21;
    font-weight: bold;
  }
  
  .footer-right {
    display: flex;
    gap: 10px;
  }
  
  .icon {
    height: 25px;
  }
  </style>
  