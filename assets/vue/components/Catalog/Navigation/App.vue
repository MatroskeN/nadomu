<template>
  <Teleport to=".contentHeader">
    <div class="pagination" v-if="total_pages > 1">
      <div class="current" v-if="page<10">
        <p>0{{page}}</p>
      </div>
      <div class="current" v-if="page>=10">
        <p>{{page}}</p>
      </div>
      <div class="total">/ {{total_pages}}</div>
    </div>
  </Teleport>
  <div class="navigation" v-if="total_pages > 1">
    <div class="prev-arrow arrow" v-on:click="minusPage" v-if="parseInt(page) - 1 > 0">
      <svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M7.5 13L1.5 7L7.5 1" stroke="#59B4FB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <div class="next-arrow arrow" v-on:click="plusPage" v-if="parseInt(page) + 1 <= total_pages">
      <svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M1.5 13L7.5 7L1.5 1" stroke="#59B4FB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
  </div>
</template>

<script>
import {MUTATION_SET_FILTER_PAGE} from "@/components/Catalog/Search/store/types/mutations";
import {ACTION_SEARCH_REQUEST} from "@/components/Catalog/Search/store/types/actions";
import {GETTER_RESULT_TOTAL_COUNT} from '@/components/Catalog/Search/store/types/getters';
import {GETTER_SEARCH_BASE_DATA} from "@/components/Catalog/Search/store/types/getters";
import {mapGetters} from "vuex";

const PAGE_LIMIT = 10;

export default {
  name: 'Navigation',
  data () {
    return{
      page: '1'
    }
  },
  methods: {
    setPage: function (){
      this.$store.commit(MUTATION_SET_FILTER_PAGE, this.page)
      this.$store.dispatch(ACTION_SEARCH_REQUEST)
    },
    minusPage: function (){
      if (this.page > 1){
        this.page--;
        this.setPage()
      }
    },
    plusPage: function (){
      if (this.page < this.total_pages){
        this.page++;
        this.setPage()
      }
    },
  },
  computed: {
    ...mapGetters({
      'resultTotal': GETTER_RESULT_TOTAL_COUNT,
      'searchBaseData': GETTER_SEARCH_BASE_DATA
    }),
    total_pages() {
      let count = this.resultTotal ?? window.catalog_count_specialists;

      return Math.ceil(count / PAGE_LIMIT)
    }
  },
  watch: {
    searchBaseData : {
      handler(searchBaseData, old_val){
        this.page = searchBaseData.page;
      },
      deep: true
    }
  },
  mounted() {

  }
}
</script>

<style scoped lang="scss">
.navigation{
  display: flex;
  align-items: center;
  justify-content: flex-end;
  .arrow{
    width: 45px;
    height: 45px;
    border: 2px solid #59B4FB;
    border-radius: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    &:first-of-type{
      margin-right: 15px;
    }
  }
}
</style>