<template>
  <div>
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
    <span v-on:click="select_location.open = true">{{ select_location.text }}</span>

    <select class="form-control" v-model="service">
      <option value="">Выберите услугу</option>
      <option v-for="option in systemServices" v-bind:value="option.id" >{{ option.name }}</option>
    </select>
  </div>
</template>

<script>
import SelectLocation from "@/components/UI/SelectLocation/SelectLocation.vue";
import {GETTER_SYSTEM_SERVICES} from "@/store/types/getters";
import {mapGetters} from "vuex";


export default {
  name: 'HeaderCatalogSearch',
  data() {
    return {
      select_location: {
        stations: [],
        cities: [],
        text: '',
        open: false,
      },
      stations: [30], //станции по умолчанию (которые уже проставлены). сделано для примера, после ознакомления убрать!
      cities: [1],
      service: '',  //значение по умолчанию для услуги (пустой селект "Выберите услугу")
      error: {}
    }
  },
  components: {
    SelectLocation
  },
  computed: {
    ...mapGetters({
      'systemServices': GETTER_SYSTEM_SERVICES
    }),
  },
  methods: {
    setLocations(text, station_ids, cities_ids) {
      this.select_location.stations = station_ids;
      this.select_location.cities = cities_ids;
      this.select_location.text = text;
    },
    closeSelectLocation() {
      this.select_location.open = false;
    }
  }
}
</script>

<style scoped lang="scss">

</style>
