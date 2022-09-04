<template>
  <section>
    <h1>Поиск медсестер на дом в Москве</h1>
    <div class="subtitle">
      Ручной подбор медицинских специалистов по выбранным Вами критериям
    </div>
    <div class="specialist">
      <SelectLocation
          :isOpen="select_location.open"
          :isSingle="false"
          :showLatest="true"
          :maxTextLength="30"
          :defaultText="'Выберите станцию метро'"
          :initStations="stations"
          :initCities="cities"
          @select-location-close="closeSelectLocation"
          @select-location-select="setLocations"
      />
      <div class="content" v-on:click="select_location.open = true">
        <p>{{ select_location.text }}</p>
        <svg width="11" height="8" viewBox="0 0 11 8" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1 1.5L5.5 6.5L10 1.5" stroke="#283848" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round"/>
        </svg>
      </div>
      <select v-model="search_data.service_id">
        <option value="">Специализация</option>
        <option v-for="item in services" :value="item.id">
          {{ item.name }}
        </option>
      </select>
      <button class="choose parent-spinner hover-spinner-is-blue" v-on:click="selectSpecialist">
        <Spinner :isActive="is_filter_result_loading"></Spinner>
        Подобрать специалиста
      </button>
    </div>
    <div class="note">
      Для массовой отправки заявок специалистам воспользуйтесь <b>инструментом подбора</b>
      <br>
      <div class="loading_error">
        {{ filter_loading_message }}
      </div>
    </div>
  </section>
</template>

<script>
import SelectLocation from "@/components/UI/SelectLocation/SelectLocation.vue";
import {
  MUTATION_SET_SEARCH_BASE_DATA,
  MUTATION_SET_SEARCH_RESULT,
  MUTATION_SET_FILTER_PAGE
} from "@/components/Catalog/Search/store/types/mutations";
import {ACTION_SEARCH_REQUEST} from "./store/types/actions";
import {mapActions, mapGetters} from "vuex";
import {GETTER_IS_FILTER_RESULT_LOADING} from "@/components/Catalog/Search/store/types/getters";
import {GETTER_SYSTEM_DATA} from "@/store/types/getters";
import Spinner from "@/components/UI/Spinner/Spinner.vue";

export default {
  name: "Search",
  data() {
    return {
      filter_loading_message: '',
      select_location: {
        stations: [],
        cities: [],
        text: '',
        open: false,
      },
      stations: [],
      cities: [],
      search_data: {
        service_id: '',
      },
      services: [],
      page: 1
    }
  },
  methods: {
    ...mapActions({
      'initFilter': ACTION_SEARCH_REQUEST,
    }),
    //select methods
    setLocations(text, station_ids, cities_ids) {
      /*this.search_data.stations_id = station_ids;
      this.search_data.cities_id = cities_ids;*/

      let station_id = (station_ids.length ? station_ids[0] : null);
      let city_id = (cities_ids.length ? cities_ids[0] : null);

      this.search_data.stations_id = station_id;
      this.search_data.cities_id = city_id;

      this.select_location.text = text;
    },
    closeSelectLocation() {
      this.select_location.open = false;
    },
    selectSpecialist() {
      this.filter_loading_message = '';
      this.filter_loading_status = true;

      this.$store.commit(MUTATION_SET_SEARCH_RESULT, null);
      this.$store.commit(MUTATION_SET_SEARCH_BASE_DATA, this.search_data);
      this.$store.commit(MUTATION_SET_FILTER_PAGE, 1)
      this.initFilter();
    }
  },
  computed: {
    ...mapGetters({
      'is_filter_result_loading': GETTER_IS_FILTER_RESULT_LOADING,
      'system_data': GETTER_SYSTEM_DATA
    })
  },
  watch: {
    system_data(system_data, old_val) {
      this.services = system_data.services;
    }
  },
  components: {
    Spinner,
    SelectLocation
  }
}
</script>

<style scoped lang="scss">
@import 'assets/scss/default.scss';

section {
  font-family: $base-font;
  font-style: normal;
}

h1 {
  font-weight: 500;
  font-size: 48px;
  line-height: 50px;
  text-align: center;
  color: #3F85EF;
}

.subtitle {
  margin: 26px 0 45px;
  font-weight: 300;
  font-size: 18px;
  line-height: 27px;
  text-align: center;
  color: #283848;
}

.specialist {
  margin: 0 auto;
  background: #FFFFFF;
  box-shadow: 0px 0px 35px rgba(21, 30, 41, 0.1);
  border-radius: 15px;

  .content {
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    font-size: 16px;
    color: #283848;
    width: 276px;
    height: 30px;
    margin-right: 50px;
    border-bottom: 1px solid #C4C4C4;
    padding-bottom: 5px;
  }

  select {
    font-family: $base-font;
    font-style: normal;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    font-size: 16px;
    color: #283848;
    width: 276px;
    height: 30px;
    margin-right: 50px;
    border-bottom: 1px solid #C4C4C4;
    padding: 0 0 5px 0;
    border-radius: unset;
  }
}

.note {
  font-weight: 300;
  font-size: 16px;
  line-height: 24px;
  text-align: center;
  color: #283848;
  margin: 45px auto 0;
  max-width: 497px;

  .loading {
    font-weight: 600;
  }

  .loading_error {
    color: #d00d0d;
  }
}

@media(max-width: 960px) {
  .specialist {
    flex-direction: column;
    height: fit-content;
    padding: 30px 40px;
    box-sizing: border-box;

    .content, select {
      margin-right: unset;
      margin-bottom: 30px;
    }
  }
}
</style>
