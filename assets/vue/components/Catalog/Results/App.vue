<template>
  <div class="tempo">
    <div class="tempoItem" v-if="is_empty_results">
      Нет подходящих результатов
    </div>
    <div class="tempoItem" v-if="is_filter_result_loading">
      Подождите, идет загрузка...
      <br>
    </div>
  </div>
  <div v-for="item in items">
    <SearchCard :data="item"></SearchCard>
  </div>
</template>

<script>

import SearchCard from "@/components/Catalog/Card/App.vue";
import {mapGetters} from "vuex";
import {GETTER_SEARCH_RESULTS} from "../Search/store/types/getters";
import {GETTER_IS_FILTER_RESULT_LOADING} from "@/components/Catalog/Search/store/types/getters";

export default {
  name: 'SearchResults',
  components: {SearchCard},
  computed: {
    ...mapGetters({
      'items': GETTER_SEARCH_RESULTS,
      'is_filter_result_loading': GETTER_IS_FILTER_RESULT_LOADING
    }),
    is_empty_results() {
      return (this.items === null || !this.items?.length) && !this.is_filter_result_loading;
    }
  }
}
</script>

<style scoped lang="scss">
  .tempo{
    display: flex;
    flex-direction: column;
    align-items: center;
    .tempoItem{
      text-align: center;
      margin-bottom: 10px;
      font-weight: 600;
    }
  }
</style>
