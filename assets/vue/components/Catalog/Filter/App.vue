<template>
  <div class="filter">
    <div class="title">
      <p>Фильтр</p>
      <div class="arrow" :class="{ active: isActive}" v-on:click="isActive = !isActive">
        <svg width="17" height="10" viewBox="0 0 17 10" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1 1L8.5 9L16 1" stroke="#283848" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </div>
    <div class="filterContent" v-if="isActive">
      <div class="priceBlock">
        <div class="price">
          <p>Цена</p>
          <input type="text" v-model.lazy="range[0]" v-on:change="onChangeMinPrice" :placeholder="minRange">
          <input type="text" v-model.lazy="range[1]" v-on:change="onChangeMaxPrice" :placeholder="maxRange">
        </div>
        <div class="interval">
          <Slider
              v-model="range"
              class="range_slider"
              :lazy="true"
              :min="minRange"
              :max="maxRange"
              :step="step"
              :tooltips="false"
              @change="setPrice"
          />
        </div>
      </div>
      <div class="rate">
        <div class="title">Рейтинг</div>
        <div class="rateItem">
          <input type="radio" name="rate" id="any" v-model="input_data.rating" v-on:change="filterSpecialist"
                 :value="null">
          <label for="any">Любой</label>
        </div>
        <div class="rateItem">
          <input type="radio" name="rate" id="has" v-model="input_data.rating" v-on:change="filterSpecialist"
                 :value="'exist'">
          <label for="has">Есть рейтинг</label>
        </div>
        <div class="rateItem">
          <input type="radio" name="rate" id="five" v-model="input_data.rating" v-on:change="filterSpecialist"
                 :value="5">
          <label for="five">
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.84577 0L8.87808 3.46649L12.8029 4.32814L10.1341 7.33219L10.5275 11.3312L6.84577 9.72132L3.16404 11.3312L3.55741 7.33219L0.8886 4.32814L4.81345 3.46649L6.84577 0Z"
                  fill="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.84577 0L8.87808 3.46649L12.8029 4.32814L10.1341 7.33219L10.5275 11.3312L6.84577 9.72132L3.16404 11.3312L3.55741 7.33219L0.8886 4.32814L4.81345 3.46649L6.84577 0Z"
                  fill="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.84577 0L8.87808 3.46649L12.8029 4.32814L10.1341 7.33219L10.5275 11.3312L6.84577 9.72132L3.16404 11.3312L3.55741 7.33219L0.8886 4.32814L4.81345 3.46649L6.84577 0Z"
                  fill="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.84577 0L8.87808 3.46649L12.8029 4.32814L10.1341 7.33219L10.5275 11.3312L6.84577 9.72132L3.16404 11.3312L3.55741 7.33219L0.8886 4.32814L4.81345 3.46649L6.84577 0Z"
                  fill="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.84577 0L8.87808 3.46649L12.8029 4.32814L10.1341 7.33219L10.5275 11.3312L6.84577 9.72132L3.16404 11.3312L3.55741 7.33219L0.8886 4.32814L4.81345 3.46649L6.84577 0Z"
                  fill="#3F85EF"/>
            </svg>
          </label>
        </div>
        <div class="rateItem">
          <input type="radio" name="rate" id="four" v-model="input_data.rating" v-on:change="filterSpecialist"
                 :value="4">
          <label for="four">
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.84577 0L8.87808 3.46649L12.8029 4.32814L10.1341 7.33219L10.5275 11.3312L6.84577 9.72132L3.16404 11.3312L3.55741 7.33219L0.8886 4.32814L4.81345 3.46649L6.84577 0Z"
                  fill="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.84577 0L8.87808 3.46649L12.8029 4.32814L10.1341 7.33219L10.5275 11.3312L6.84577 9.72132L3.16404 11.3312L3.55741 7.33219L0.8886 4.32814L4.81345 3.46649L6.84577 0Z"
                  fill="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.84577 0L8.87808 3.46649L12.8029 4.32814L10.1341 7.33219L10.5275 11.3312L6.84577 9.72132L3.16404 11.3312L3.55741 7.33219L0.8886 4.32814L4.81345 3.46649L6.84577 0Z"
                  fill="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.84577 0L8.87808 3.46649L12.8029 4.32814L10.1341 7.33219L10.5275 11.3312L6.84577 9.72132L3.16404 11.3312L3.55741 7.33219L0.8886 4.32814L4.81345 3.46649L6.84577 0Z"
                  fill="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.37409 0.988606L7.97507 3.71937L8.08564 3.90798L8.29919 3.95486L11.391 4.63363L9.28865 7.00011L9.14344 7.16355L9.16485 7.38114L9.47473 10.5314L6.57441 9.2632L6.37409 9.17561L6.17377 9.2632L3.27344 10.5314L3.58333 7.38114L3.60473 7.16355L3.45953 7.00011L1.35714 4.63363L4.44899 3.95486L4.66253 3.90798L4.77311 3.71937L6.37409 0.988606Z"
                  stroke="#3F85EF"/>
            </svg>
          </label>
        </div>
        <div class="rateItem">
          <input type="radio" name="rate" id="three" v-model="input_data.rating" v-on:change="filterSpecialist"
                 :value="3">
          <label for="three">
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.84577 0L8.87808 3.46649L12.8029 4.32814L10.1341 7.33219L10.5275 11.3312L6.84577 9.72132L3.16404 11.3312L3.55741 7.33219L0.8886 4.32814L4.81345 3.46649L6.84577 0Z"
                  fill="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.84577 0L8.87808 3.46649L12.8029 4.32814L10.1341 7.33219L10.5275 11.3312L6.84577 9.72132L3.16404 11.3312L3.55741 7.33219L0.8886 4.32814L4.81345 3.46649L6.84577 0Z"
                  fill="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.84577 0L8.87808 3.46649L12.8029 4.32814L10.1341 7.33219L10.5275 11.3312L6.84577 9.72132L3.16404 11.3312L3.55741 7.33219L0.8886 4.32814L4.81345 3.46649L6.84577 0Z"
                  fill="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.37409 0.988606L7.97507 3.71937L8.08564 3.90798L8.29919 3.95486L11.391 4.63363L9.28865 7.00011L9.14344 7.16355L9.16485 7.38114L9.47473 10.5314L6.57441 9.2632L6.37409 9.17561L6.17377 9.2632L3.27344 10.5314L3.58333 7.38114L3.60473 7.16355L3.45953 7.00011L1.35714 4.63363L4.44899 3.95486L4.66253 3.90798L4.77311 3.71937L6.37409 0.988606Z"
                  stroke="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.37409 0.988606L7.97507 3.71937L8.08564 3.90798L8.29919 3.95486L11.391 4.63363L9.28865 7.00011L9.14344 7.16355L9.16485 7.38114L9.47473 10.5314L6.57441 9.2632L6.37409 9.17561L6.17377 9.2632L3.27344 10.5314L3.58333 7.38114L3.60473 7.16355L3.45953 7.00011L1.35714 4.63363L4.44899 3.95486L4.66253 3.90798L4.77311 3.71937L6.37409 0.988606Z"
                  stroke="#3F85EF"/>
            </svg>
          </label>
        </div>
        <div class="rateItem">
          <input type="radio" name="rate" id="two" v-model="input_data.rating" v-on:change="filterSpecialist"
                 :value="2">
          <label for="two">
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.84577 0L8.87808 3.46649L12.8029 4.32814L10.1341 7.33219L10.5275 11.3312L6.84577 9.72132L3.16404 11.3312L3.55741 7.33219L0.8886 4.32814L4.81345 3.46649L6.84577 0Z"
                  fill="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.84577 0L8.87808 3.46649L12.8029 4.32814L10.1341 7.33219L10.5275 11.3312L6.84577 9.72132L3.16404 11.3312L3.55741 7.33219L0.8886 4.32814L4.81345 3.46649L6.84577 0Z"
                  fill="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.37409 0.988606L7.97507 3.71937L8.08564 3.90798L8.29919 3.95486L11.391 4.63363L9.28865 7.00011L9.14344 7.16355L9.16485 7.38114L9.47473 10.5314L6.57441 9.2632L6.37409 9.17561L6.17377 9.2632L3.27344 10.5314L3.58333 7.38114L3.60473 7.16355L3.45953 7.00011L1.35714 4.63363L4.44899 3.95486L4.66253 3.90798L4.77311 3.71937L6.37409 0.988606Z"
                  stroke="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.37409 0.988606L7.97507 3.71937L8.08564 3.90798L8.29919 3.95486L11.391 4.63363L9.28865 7.00011L9.14344 7.16355L9.16485 7.38114L9.47473 10.5314L6.57441 9.2632L6.37409 9.17561L6.17377 9.2632L3.27344 10.5314L3.58333 7.38114L3.60473 7.16355L3.45953 7.00011L1.35714 4.63363L4.44899 3.95486L4.66253 3.90798L4.77311 3.71937L6.37409 0.988606Z"
                  stroke="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.37409 0.988606L7.97507 3.71937L8.08564 3.90798L8.29919 3.95486L11.391 4.63363L9.28865 7.00011L9.14344 7.16355L9.16485 7.38114L9.47473 10.5314L6.57441 9.2632L6.37409 9.17561L6.17377 9.2632L3.27344 10.5314L3.58333 7.38114L3.60473 7.16355L3.45953 7.00011L1.35714 4.63363L4.44899 3.95486L4.66253 3.90798L4.77311 3.71937L6.37409 0.988606Z"
                  stroke="#3F85EF"/>
            </svg>
          </label>
        </div>
        <div class="rateItem">
          <input type="radio" name="rate" id="one" v-model="input_data.rating" v-on:change="filterSpecialist"
                 :value="1">
          <label for="one">
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.84577 0L8.87808 3.46649L12.8029 4.32814L10.1341 7.33219L10.5275 11.3312L6.84577 9.72132L3.16404 11.3312L3.55741 7.33219L0.8886 4.32814L4.81345 3.46649L6.84577 0Z"
                  fill="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.37409 0.988606L7.97507 3.71937L8.08564 3.90798L8.29919 3.95486L11.391 4.63363L9.28865 7.00011L9.14344 7.16355L9.16485 7.38114L9.47473 10.5314L6.57441 9.2632L6.37409 9.17561L6.17377 9.2632L3.27344 10.5314L3.58333 7.38114L3.60473 7.16355L3.45953 7.00011L1.35714 4.63363L4.44899 3.95486L4.66253 3.90798L4.77311 3.71937L6.37409 0.988606Z"
                  stroke="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.37409 0.988606L7.97507 3.71937L8.08564 3.90798L8.29919 3.95486L11.391 4.63363L9.28865 7.00011L9.14344 7.16355L9.16485 7.38114L9.47473 10.5314L6.57441 9.2632L6.37409 9.17561L6.17377 9.2632L3.27344 10.5314L3.58333 7.38114L3.60473 7.16355L3.45953 7.00011L1.35714 4.63363L4.44899 3.95486L4.66253 3.90798L4.77311 3.71937L6.37409 0.988606Z"
                  stroke="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.37409 0.988606L7.97507 3.71937L8.08564 3.90798L8.29919 3.95486L11.391 4.63363L9.28865 7.00011L9.14344 7.16355L9.16485 7.38114L9.47473 10.5314L6.57441 9.2632L6.37409 9.17561L6.17377 9.2632L3.27344 10.5314L3.58333 7.38114L3.60473 7.16355L3.45953 7.00011L1.35714 4.63363L4.44899 3.95486L4.66253 3.90798L4.77311 3.71937L6.37409 0.988606Z"
                  stroke="#3F85EF"/>
            </svg>
            <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M6.37409 0.988606L7.97507 3.71937L8.08564 3.90798L8.29919 3.95486L11.391 4.63363L9.28865 7.00011L9.14344 7.16355L9.16485 7.38114L9.47473 10.5314L6.57441 9.2632L6.37409 9.17561L6.17377 9.2632L3.27344 10.5314L3.58333 7.38114L3.60473 7.16355L3.45953 7.00011L1.35714 4.63363L4.44899 3.95486L4.66253 3.90798L4.77311 3.71937L6.37409 0.988606Z"
                  stroke="#3F85EF"/>
            </svg>
          </label>
        </div>
      </div>
      <div class="timing">
        <div class="title">
          Интересующее время
        </div>
        <div class="time">
          <p class="timeElements">
            <span v-if="timesheet_data.length === 0">В любое время</span>
            <span v-if="timesheet_data.length > 0" v-for="item in converted_timesheet_data">
              {{ item.day }} : {{ item.hour }}
            </span>
          </p>
          <div class="timesheetBtn" v-on:click="$refs.timesheetComponent.openWindow()">Изменить время</div>
        </div>
      </div>
      <div class="sex">
        <div class="title">Пол специалиста</div>
        <div class="sexItem">
          <input type="radio" name="sex" id="anysex" v-model="input_data.gender" v-on:change="filterSpecialist"
                 :value="null">
          <label for="anysex">Любой</label>
        </div>
        <div class="sexItem">
          <input type="radio" name="sex" id="man" v-model="input_data.gender" v-on:change="filterSpecialist"
                 :value="'male'">
          <label for="man">Мужчина</label>
        </div>
        <div class="sexItem">
          <input type="radio" name="sex" id="woman" v-model="input_data.gender" v-on:change="filterSpecialist"
                 :value="'female'">
          <label for="woman">Женщина</label>
        </div>
      </div>
      <div class="experience">
        <div class="title">Стаж специалиста</div>
        <div class="experienceItem">
          <input type="radio" name="experience" id="anyexp" v-model="input_data.experience"
                 v-on:change="filterSpecialist" :value="null">
          <label for="anyexp">Любой</label>
        </div>
        <div class="experienceItem">
          <input type="radio" name="experience" id="3-year" v-model="input_data.experience"
                 v-on:change="filterSpecialist" :value="'from1to3'">
          <label for="3-year">до 3 лет</label>
        </div>
        <div class="experienceItem">
          <input type="radio" name="experience" id="5-year" v-model="input_data.experience"
                 v-on:change="filterSpecialist" :value="'from3to5'">
          <label for="5-year">от 3 до 5 лет</label>
        </div>
        <div class="experienceItem">
          <input type="radio" name="experience" id="10-year" v-model="input_data.experience"
                 v-on:change="filterSpecialist" :value="'from5to10'">
          <label for="10-year">от 5 до 10 лет</label>
        </div>
        <div class="experienceItem">
          <input type="radio" name="experience" id="20-year" v-model="input_data.experience"
                 v-on:change="filterSpecialist" :value="'more10'">
          <label for="20-year">от 10 лет и более</label>
        </div>
      </div>
      <div class="wipe" v-on:click="resetFilter">
        Сбросить фильтр
      </div>
    </div>
    <div class="circle"></div>
    <Timesheet ref="timesheetComponent" @getTimesheet="getTimesheet"/>
  </div>
</template>

<script>
import Slider from '@vueform/slider'
import {
  MUTATION_SET_SEARCH_FILTER_DATA,
  MUTATION_SET_SEARCH_BASE_DATA,
  MUTATION_SET_FILTER_PAGE,
  MUTATION_SET_SEARCH_RESULT
} from "@/components/Catalog/Search/store/types/mutations";
import Timesheet from "@/components/UI/Timesheet/Timesheet.vue";
import {ACTION_SEARCH_REQUEST} from "@/components/Catalog/Search/store/types/actions";
import {GETTER_SEARCH_BASE_DATA} from "@/components/Catalog/Search/store/types/getters";
import {MUTATION_SET_FILTER_WORK_TIME} from "@/components/Catalog/Search/store/types/mutations";
import {mapActions, mapGetters} from "vuex";

//values for price range in filter
const MIN_RANGE = 0;
const MAX_RANGE = 100000;
const STEP = 100;

export default {
  name: 'Filter',
  components: {Timesheet, Slider},
  data() {
    return {
      isActive: true,
      range: [MIN_RANGE, MAX_RANGE],
      page: 1,
      input_data: {
        gender: null,
        experience: null,
        price_min: null,
        price_max: null,
        rating: null
      },
      search_data: {
        service_id: '',
      },
      timesheet_data: '',
      converted_timesheet_data: [],
    }
  },
  methods: {
    ...mapActions({
      'initFilter': ACTION_SEARCH_REQUEST,
    }),
    filterSpecialist: function () {
      this.$store.commit(MUTATION_SET_SEARCH_RESULT, null);
      this.$store.commit(MUTATION_SET_SEARCH_FILTER_DATA, this.input_data);
      this.$store.commit(MUTATION_SET_FILTER_PAGE, 1)
      this.initFilter();
    },
    resetFilter: function () {
      this.timesheet_data = [];
      this.converted_timesheet_data = [];
      this.$refs.timesheetComponent.resetCheckedValues();
      this.range = [this.minRange, this.maxRange]
      this.input_data = {
        gender: null,
        page: 1,
        experience: null,
        input_value_min: '',
        input_value_max: '',
        rating: null,
        worktime: [],
      }

      this.$store.commit(MUTATION_SET_FILTER_WORK_TIME, [])
      this.$store.commit(MUTATION_SET_SEARCH_FILTER_DATA, this.input_data);
      this.$store.commit(MUTATION_SET_FILTER_PAGE, 1)

      this.filterSpecialist();
    },
    setPrice: function (val) {
      this.input_data.price_min = val[0];
      this.input_data.price_max = val[1];

      this.filterSpecialist();
    },
    onChangeMinPrice: function(event){
      let price = event.target.value;

      if (this.range[1] < price)
        this.range[1] = price;

      this.setIntvalRangeTime();
      this.setPrice(this.range);
    },
    onChangeMaxPrice: function(event){
      let price = event.target.value;

      if (this.range[0] > price)
        this.range[0] = price;

      this.setIntvalRangeTime();
      this.setPrice(this.range);
    },
    setIntvalRangeTime: function() {
      this.range[0] = parseInt(this.range[0]);
      this.range[1] = parseInt(this.range[1]);
    },
    getTimesheet: function (timeData) {
      this.timesheet_data = timeData.time;
      this.convertTimesheet();
    },
    convertTimesheet: function () {
      this.converted_timesheet_data = [];
      this.timesheet_data.forEach((item, index) => {
        switch (item.day) {
          case 1:
            this.converted_timesheet_data.push({day: 'Пн', hour: item.hour + ':00'})
            break
          case 2:
            this.converted_timesheet_data.push({day: 'Вт', hour: item.hour + ':00'})
            break
          case 3:
            this.converted_timesheet_data.push({day: 'Ср', hour: item.hour + ':00'})
            break
          case 4:
            this.converted_timesheet_data.push({day: 'Чт', hour: item.hour + ':00'})
            break
          case 5:
            this.converted_timesheet_data.push({day: 'Пт', hour: item.hour + ':00'});
            break
          case 6:
            this.converted_timesheet_data.push({day: 'Сб', hour: item.hour + ':00'})
            break
          case 7:
            this.converted_timesheet_data.push({day: 'Вс', hour: item.hour + ':00'})
            break
        }
      })
    }
  },
  computed: {
    minRange: function () {
      return MIN_RANGE;
    },
    maxRange: function () {
      return MAX_RANGE;
    },
    step: function () {
      return STEP;
    },
    ...mapGetters({
      'getSearchBaseData': GETTER_SEARCH_BASE_DATA
    })
  }
}
</script>
<style src="@vueform/slider/themes/default.css"></style>
<style scoped lang="scss">
@import 'assets/scss/default.scss';

.filter {
  background: #F0F1F8;
  border-radius: 15px;
  width: 290px;
  padding: 27px 23px;
  box-sizing: border-box;
  margin-right: 25px;
  position: relative;

  input[type="radio"] {
    border-color: #87B1CA;
    color: #87B1CA;
    width: 13px;
    height: 13px;
  }

  .title {
    font-weight: 600;
    font-size: 20px;
    line-height: 27px;
    color: #151E29;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;

    .arrow {
      display: none;
    }
  }

  .price {
    display: flex;
    align-items: center;

    input {
      border: none;
      padding: 9px 17px;
      width: auto;
      max-width: 90px;
      box-sizing: border-box;
      margin-left: 10px;
    }
  }

  .interval {
    margin: 30px 0;
    background: #87B1CA;
    width: 100%;
    height: 1px;
  }

  .range_slider {
    --slider-height: 2px;
    --slider-handle-width: 10px;
    --slider-handle-height: 10px;
    --slider-connect-bg: #3F85EF;
    --slider-handle-bg: #3F85EF;
  }

  .rate {
    margin-bottom: 30px;

    .title {
      margin-bottom: 15px;
    }

    .rateItem {
      display: flex;
      align-items: center;
      margin-bottom: 15px;

      label {
        margin-left: 15px;
        font-weight: 400;
        font-size: 14px;
        line-height: 16px;
        color: #283848;
      }
    }
  }

  .timing {
    .title {
      margin-bottom: 15px;
    }

    .time {
      display: flex;
      align-items: center;
      justify-content: flex-start;

      .timeElements {
        max-width: 120px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      span {
        font-weight: 400;
        font-size: 14px;
        line-height: 16px;
        color: #283848;
        margin-right: 5px;
      }

      .timesheetBtn {
        margin-left: 5px;
        cursor: pointer;
        font-weight: 500;
        font-size: 12px;
        line-height: 20px;
        text-decoration-line: underline;
        color: #3F85EF;
      }
    }
  }

  .sex {
    margin-top: 30px;

    .title {
      margin-bottom: 15px;
    }

    .sexItem {
      margin-bottom: 15px;

      label {
        margin-left: 15px;
        font-weight: 400;
        font-size: 14px;
        line-height: 16px;
        color: #283848;
      }
    }
  }

  .experience {
    margin-top: 30px;

    .title {
      margin-bottom: 15px;
    }

    .experienceItem {
      margin-bottom: 15px;

      label {
        margin-left: 15px;
        font-weight: 400;
        font-size: 14px;
        line-height: 16px;
        color: #283848;
      }
    }
  }

  .wipe {
    margin-top: 30px;
    font-weight: 600;
    font-size: 14px;
    line-height: 17px;
    text-decoration-line: underline;
    color: #87B1CA;
    cursor: pointer;
  }

  .circle {
    width: 36px;
    height: 36px;
    border-radius: 25px;
    background: #3F85EF;
    position: absolute;
    bottom: -18px;
    right: 49px;
  }
}

@media (max-width: 1250px) {
  .filter {
    max-width: 100%;
    width: 100%;
    margin-bottom: 57px;
    margin-right: unset;
    display: flex;
    flex-flow: row wrap;

    .title {
      width: 100%;

      .arrow {
        display: flex;
        transition: all 0.4s;
        height: fit-content;
      }

      .active {
        transform: rotateZ(180deg);
      }
    }

    .filterContent {
      display: flex;
      flex-flow: row wrap;

      .priceBlock {
        order: 0;
        width: 50%;

        .interval {
          width: 215px;
        }
      }

      .timing {
        order: 1;
        width: 50%;

        .time {
          justify-content: unset;

          span {
            margin-right: 55px;
          }
        }
      }

      .rate {
        order: 2;
        width: 33%;
        margin: 0;
      }

      .sex {
        order: 3;
        width: 33%;
        margin: 0;
      }

      .experience {
        order: 4;
        width: 33%;
        margin: 0;
      }

      .wipe {
        order: 5;
      }
    }
  }
}

@media(max-width: 690px) {
  .filter {
    flex-flow: column;
    align-items: stretch;

    .filterContent {
      flex-flow: column;
      align-items: stretch;

      .rate {
        order: 1;
        margin-bottom: 36px;
        width: 100%;
      }

      .timing {
        order: 2;
        margin-bottom: 36px;
        width: 100%;

        .time {
          justify-content: space-between;

          span {
            margin: 0;
          }
        }
      }

      .sex {
        margin-bottom: 36px;
        width: 100%;
      }

      .experience {
        width: 100%;
      }
    }
  }
}
</style>
