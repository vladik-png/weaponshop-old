<template>
    <div class="search-container">
      <input
        type="text"
        v-model="searchTerm"
        @input="onSearchInput"
        placeholder="Пошук..."
      />
      <ul v-if="suggestions.length" class="suggestion-list">
        <li 
          v-for="(item, index) in suggestions" 
          :key="index"
          @click="onSelect(item)"
        >
          <!-- Відображення залежить від типу -->
          <span v-if="item.type === 'weapon'">
            🔫 {{ item.name }}
          </span>
          <span v-else-if="item.type === 'category'">
            📂 {{ item.name }}
          </span>
        </li>
      </ul>
    </div>
  </template>
  
  <script>
  export default {
    name: 'GlobalSearch',
    data() {
      return {
        searchTerm: '',
        suggestions: []
      };
    },
    methods: {
      onSearchInput() {
        // Якщо поле порожнє - не відправляємо запит
        if (!this.searchTerm) {
          this.suggestions = [];
          return;
        }
  
        // Використовуємо fetch (або axios) для GET-запиту
        fetch(`http://localhost/weaponshop/php/global_search.php?term=${encodeURIComponent(this.searchTerm)}`)
          .then(response => response.json())
          .then(data => {
            this.suggestions = data;
          })
          .catch(err => console.error('Помилка пошуку:', err));
      },
      onSelect(item) {
        // Дія при кліку на елемент списку
        // Наприклад, можна перенаправити користувача:
        if (item.type === 'weapon') {
          // Припустимо, ви маєте маршрут /weapon/:id
          this.$router.push(`/weapon/${item.id}`);
        } else if (item.type === 'category') {
          // Маршрут /category/:id
          this.$router.push(`/category/${item.id}`);
        }
  
        // Також можна очистити рядок пошуку
        this.searchTerm = '';
        this.suggestions = [];
      }
    }
  };
  </script>
  
  <style scoped>
  .search-container {
    position: relative;
    display: inline-block;
  }
  .suggestion-list {
    position: absolute;
    background: #fff;
    border: 1px solid #ccc;
    list-style: none;
    margin: 0;
    padding: 0;
    width: 200px;
  }
  .suggestion-list li {
    padding: 5px 10px;
    cursor: pointer;
  }
  .suggestion-list li:hover {
    background-color: #eee;
  }
  </style>
  