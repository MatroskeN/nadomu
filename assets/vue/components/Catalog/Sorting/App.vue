<template>
  <div class="select">
    <select v-model="sort_by" v-on:change="setSortData()">
      <option value="price_max">
        По снижению цены
      </option>
      <option value="price_min">
        По возрастанию цены
      </option>
      <option value="rate_max">
        По снижению рейтинга
      </option>
      <option value="rate_min">
        По возрастанию рейтинга
      </option>
    </select>
  </div>
</template>

<script>
import {MUTATION_SET_SEARCH_SORT_DATA, MUTATION_SET_FILTER_PAGE} from "@/components/Catalog/Search/store/types/mutations";
import {ACTION_SEARCH_REQUEST} from "@/components/Catalog/Search/store/types/actions";
import {GETTER_SEARCH_BASE_DATA} from "@/components/Catalog/Search/store/types/getters";
import {mapActions, mapGetters} from "vuex";

export default {
  name: 'Sorting',
  data (){
    return{
      sort_by: 'price_min',
      search_data: {
        stations_id: [],
        cities_id: [],
        service_id: '',
      },
    }
  },
  methods: {
    ...mapActions({
      'initFilter': ACTION_SEARCH_REQUEST,
    }),
    setSortData: function (){
      this.$store.commit(MUTATION_SET_SEARCH_SORT_DATA, this.sort_by);
      this.$store.commit(MUTATION_SET_FILTER_PAGE, 1)
      this.initFilter();
    }
  },
  computed: {
    ...mapGetters({
      'getSearchBaseData': GETTER_SEARCH_BASE_DATA
    })
  }
}
</script>

<style scoped lang="scss">
@import '../../../../scss/default.scss';

  .select{
    background: $base-blue;
    border-radius: 5px;
    width: fit-content;
    height: 35px;
    position: relative;
    select{
      width: 100%;
      height: 100%;
      padding: 0 24px;
      appearance: none;
      -moz-appearance: none;
      -webkit-appearance: none;
      background: none;
      font-family: $base-font;
      font-style: normal;
      font-weight: 400;
      font-size: 14px;
      line-height: 16px;
      color: $white;
      option{
        color: #0a0a0a;
      }
    }
    select::-ms-expand {
      display: none;
    }
    &::after{
      content: url("data:image/svg+xml,%3Csvg width='13' height='8' viewBox='0 0 13 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0.247543 1.15845C0.578316 0.827423 1.0809 0.827423 1.41803 1.15835L6.25266 5.99531L11.0873 1.15835C11.4181 0.827423 11.9207 0.827423 12.2578 1.15835C12.5886 1.48929 12.5886 1.99206 12.2578 2.3294L6.83783 7.75195C6.67242 7.91744 6.50706 8.00016 6.25256 8.00016C5.99807 8.00016 5.83271 7.91744 5.75003 7.83467L0.247444 2.32945C-0.08323 1.99856 -0.08323 1.49579 0.247543 1.15845Z' fill='white'/%3E%3C/svg%3E");
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      right: 7px;
    }
  }

@media (max-width: 650px){
  .select{
    select{
      padding: 0 25px 0 20px;
      width: auto;
      font-size: 12px;
    }
  }
}
</style>