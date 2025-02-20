<template>
  <div class="search-container">
    <input
      type="text"
      v-model="searchTerm"
      @input="onSearchInput"
      placeholder="Пошук..."
      class="search-input"
    />
    <ul v-if="suggestions.length" class="suggestion-list">
      <li
        v-for="(item, index) in suggestions"
        :key="index"
        @click="onSelect(item)"
      >
        <div v-if="item.type === 'weapon'" class="suggestion-item">
          <img
            v-if="item.image"
            :src="item.image"
            alt="weapon image"
            class="suggestion-image"
          />
          <span> {{ item.name }}</span>
        </div>
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
      if (!this.searchTerm.trim()) {
        this.suggestions = [];
        return;
      }
      fetch(`http://localhost/weaponshop/php/global_search.php?term=${encodeURIComponent(this.searchTerm)}`)
        .then(response => response.json())
        .then(data => {
          this.suggestions = data;
        })
        .catch(err => console.error('Помилка пошуку:', err));
    },
    onSelect(item) {
      // Якщо це товар, перенаправляємо на сторінку "/weapon" з параметром "search"
      if (item.type === 'weapon') {
        this.$router.push({ path: '/weapon', query: { search: item.name } });
      }
      // Очищення пошукового рядка та підказок
      this.searchTerm = '';
      this.suggestions = [];
    }
  }
};
</script>
  
