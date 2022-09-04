<template>
  <div v-bind:class="{active : isOpen}" class="select-location">
    <div class="head">
      <div class="title-block">
        <p class="title">Выбор станции метро или города</p>
        <p class="subtitle">Выберите одну или несколько станций метро</p>
      </div>
      <div class="close" v-on:click="closeWindow()">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M22.5 2L2 22.5" stroke="#283848" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M2 2L22.5 22.5" stroke="#283848" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </div>

    <div class="search">
      <div>
        <input type="text" @keyup="updateAvailableLetters" v-model="search"
               placeholder="Введите станцию метро или город">
        <span class="clear-input" v-if="search" v-on:click="clearSearch()">
          <svg width="9" height="9" viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0.641159 0.473686L8.69379 8.52632" stroke="black" stroke-width="0.473684"
                  stroke-linecap="round"/>
            <line x1="0.473679" y1="8.51335" x2="8.51334" y2="0.473687" stroke="black" stroke-width="0.473684"
                  stroke-linecap="round"/>
          </svg>
        </span>
      </div>
      Введите название или выберите из списка вручную
    </div>

    <div class="alphabet-list" v-if="systemData && (selected_cities.length + selected_stations.length) !== 0">
      <h4>Вы выбрали</h4>
      <div class="list-letters selected-values">
        <span class="line" v-on:click="toggleItemCity(city)" v-for="city in selected_cities"
              :set="data = getItem(city, systemData.cities)">
           <span class="metro-icon " style="background: rgba(63, 133, 239, 0.3);"></span> г.&nbsp;{{ data.name }}
              <svg width="9" height="9" viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M0.641159 0.473686L8.69379 8.52632" stroke="black" stroke-width="0.473684"
                    stroke-linecap="round"/>
              <line x1="0.473679" y1="8.51335" x2="8.51334" y2="0.473687" stroke="black" stroke-width="0.473684"
                    stroke-linecap="round"/>
              </svg>
        </span>
        <span class="line" v-on:click="toggleItemStation(station)" v-for="station in selected_stations"
              :set="data = getItem(station, systemData.stations)">
          <span class="metro-icon" :class="{'is-white': data.color === 'FFFFFF'}"
                :style="{background: '#' + data.color}"></span> м.&nbsp;{{ data.name }}
              <svg width="9" height="9" viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M0.641159 0.473686L8.69379 8.52632" stroke="black" stroke-width="0.473684"
                    stroke-linecap="round"/>
              <line x1="0.473679" y1="8.51335" x2="8.51334" y2="0.473687" stroke="black" stroke-width="0.473684"
                    stroke-linecap="round"/>
              </svg>
        </span>

      </div>
    </div>

    <div class="alphabet-list"
         v-if="systemData && showLatest === true && (selected_cities.length + selected_stations.length) === 0 && getLatest().length > 0">
      <h4>Ранее Вы выбирали</h4>
      <div class="list-letters selected-values">

        <span class="line" v-if="systemData.cities && systemData.stations" v-for="item in getLatest()">
          <div v-if="item.type === 'city'" v-on:click="toggleItemCity(item.id)" class="latest-search">
              <span class="metro-icon " style="background: rgba(63, 133, 239, 0.3);"></span>
              г. {{ getItem(item.id, systemData.cities).name }}
            </div>

            <div v-if="item.type === 'station'" :set="metro = getItem(item.id, systemData.stations)"
                 v-on:click="toggleItemStation(item.id)" class="latest-search">
               <span class="metro-icon " :class="{'is-white': metro.color === 'FFFFFF'}"
                     :style="{background: '#' + metro.color}"></span> {{ metro.name }}
            </div>
        </span>
      </div>
    </div>

    <div class="alphabet-list" v-if="(getStations.length + getCities.length) !== 0">
      <h4>Алфавитный указатель</h4>
      <div class="list-letters">
        <span v-for="i_letter in letters" v-bind:class="{active : i_letter === letter}"
              v-on:click="setLetter(i_letter)">{{ i_letter }}</span>
      </div>
    </div>

    <div class="content">
      <div>
        <h4 v-if="!search && !letter">Выбор станции метро</h4>
        <h4 v-if="search && !letter">Станции метро содержащие "{{ getRealSearch }}"</h4>
        <h4 v-if="!search && letter">Станции метро начинающиеся на "{{ letter }}"</h4>
        <h4 v-if="search && letter">Станции метро на "{{ letter }}" и содержащие "{{ getRealSearch }}"</h4>

        <div class="alphabet-metro" v-if="getStations">
          <div class="row" :class="{'empty-results': getStations.length === 0, 'one-column': getStations.length === 1}">

            <div v-for="(item, key) in getStations">
              <div class="letter" :class="{'is-first': key === 0}">{{ item.letter }}</div>

              <div :class="{'selected': (selected_stations.includes(metro.id))}" class="metroStationClick metro-item"
                   v-for="metro in item.items" v-on:click="toggleItemStation(metro.id)">
                <span class="metro-icon " :class="{'is-white': metro.color === 'FFFFFF'}"
                      :style="{background: '#' + metro.color}"></span> <span class="name">{{ metro.name }}</span>
              </div>
            </div>
            <div v-if="getStations.length === 0">
              Станции метро по выбранным критериям не найдены
            </div>
          </div>
        </div>

        <h4 v-if="!search && !letter">Выбор города</h4>
        <h4 v-if="search && !letter">Города содержащие "{{ getRealSearch }}"</h4>
        <h4 v-if="!search && letter">Города начинающиеся на "{{ letter }}"</h4>
        <h4 v-if="search && letter">Города на "{{ letter }}" и содержащие "{{ getRealSearch }}"</h4>

        <div class="alphabet-metro" v-if="getCities">
          <div class="row" :class="{'empty-results': getCities.length === 0, 'one-column': getCities.length === 1}">

            <div v-for="(item, key)  in getCities">
              <div class="letter" :class="{'is-first': key === 0}">{{ item.letter }}</div>

              <div :class="{'selected': (selected_cities.includes(city.id))}" class="metro-item"
                   v-for="city in item.items" v-on:click="toggleItemCity(city.id)">
                <span class="metro-icon " style="background: rgba(63, 133, 239, 0.3);"></span> <span
                  class="name">{{ city.name }}</span>
              </div>
            </div>
            <div v-if="getCities.length === 0">
              Города по выбранным критериям не найдены
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="bottom">
      <button class="white-button" v-on:click="closeWindow()">Закрыть</button>
      <button class="" v-on:click="selectItems()">Выбрать</button>
    </div>
  </div>
</template>


<script>
import {mapGetters} from "vuex";
import {GETTER_ALPHABET_STATIONS, GETTER_ALPHABET_CITIES, GETTER_SYSTEM_DATA} from "@/store/types/getters";
import ToolkitService from "../../../common/toolkit";

export default {
  name: 'SelectLocation',
  props: {
    isOpen: Boolean,
    isSingle: Boolean,
    showLatest: Boolean,
    maxTextLength: Number,
    defaultText: String,
    initStations: Array,
    initCities: Array
  },
  data() {
    return {
      selected_cities: [],
      selected_stations: [],
      letter: null,
      letters: [],
      search: '',
      stations: [],
      cities: []
    }
  },
  computed: {
    ...mapGetters({
      'alphabetStations': GETTER_ALPHABET_STATIONS,
      'alphabetCities': GETTER_ALPHABET_CITIES,
      'systemData': GETTER_SYSTEM_DATA
    }),
    getStations() {
      return this.filterItems(this.alphabetStations);
    },
    getCities() {
      return this.filterItems(this.alphabetCities);
    },
    //получаем правильное написание текста, даже если ошиблись в раскладке
    getRealSearch() {
      return ToolkitService.wrongKeyboardLanguage(this.search);
    }
  },
  mounted() {
    this.setHtmlOverflow();
    this.setInitItems();
    this.selectItems();
  },
  watch: {
    isOpen() {
      this.setHtmlOverflow();
    },
    alphabetStations() {
      //как загружаются данные, обновляем
      this.updateAvailableLetters();
      this.selectItems();
    },
    initStations() {
      this.selectItems();
    }
  },
  methods: {
    setHtmlOverflow() {
      let html_style = this.isOpen === true ? 'hidden' : 'unset';

      document.querySelector('html').style.overflow = html_style;
    },
    //список букв
    updateAvailableLetters() {
      let stations = JSON.parse(JSON.stringify(this.getStations));
      let cities = JSON.parse(JSON.stringify(this.getCities));
      let result = [];

      //фильтруем актуальные результаты, только при отсутствии выбранной буквы, иначе по всем
      if (this.letter) {
        stations = JSON.parse(JSON.stringify(this.alphabetStations));
        cities = JSON.parse(JSON.stringify(this.alphabetCities));
      }

      let source = stations.concat(cities);

      source.forEach(function (v) {
        result.push(v.letter);
      });

      result = result.filter((v, i, a) => a.indexOf(v) === i);

      this.letters = result.sort();
    },
    setLetter(letter) {
      this.letter = (letter === this.letter ? null : letter);
    },
    //фильтрация элементов в источнике (по буквам, поиску)
    filterItems(source) {
      let search = this.search.toLowerCase().trim();
      let letter = this.letter;
      let result = [];

      search = ToolkitService.wrongKeyboardLanguage(search);

      if (source) {
        source.forEach(function (v) {
          let set = [];

          //перебираем в поиске нужных элементов
          v.items.forEach(function (item) {
            let string = item.name;

            if (typeof item.line != 'undefined')
              string = string + ' ' + item.line;

            string = string.toLowerCase();
            let first_letter = string.charAt(0).toUpperCase();

            if (string.indexOf(search) !== -1 && ((letter && first_letter === letter) || letter === null))
              set.push(item);
          });

          //если что-то нашли, то добавляем, иначе пропускаем букву
          if (set.length > 0) {
            let ins = JSON.parse(JSON.stringify(v));
            ins.items = set;

            result.push(ins);
          }
        });

        return result;
      }

      return [];
    },
    //выбираем станцию
    toggleItemStation(item_id) {
      if (this.isSingle) {
        this.clearSelected();

        this.selected_stations = [item_id];

        this.selectItems();
      } else {
        if (this.selected_stations.includes(item_id))
          this.selected_stations = ToolkitService.dropElementFromArrayByValue(this.selected_stations, item_id);
        else {
          this.selected_stations.push(item_id)
        }
      }
    },
    //выбираем город
    toggleItemCity(item_id) {
      if (this.isSingle) {
        this.clearSelected();

        this.selected_cities = [item_id];

        this.selectItems();
      } else {
        if (this.selected_cities.includes(item_id))
          this.selected_cities = ToolkitService.dropElementFromArrayByValue(this.selected_cities, item_id);
        else {
          this.selected_cities.push(item_id)
        }
      }
    },
    //информация об объекте по идентификатору
    getItem(search_id, source) {
      let result = source.filter(obj => {
        return obj.id === search_id
      });

      return result ? result[0] : null;
    },
    //очищаем выбор
    clearSelected() {
      this.selected_cities = [];
      this.selected_stations = [];
    },
    //очищаем поиск
    clearSearch() {
      this.search = '';
      this.updateAvailableLetters();
    },
    //получаем крайние выбираемые локации
    getLatest() {
      return JSON.parse(localStorage.getItem('last_search_location')) || [];
    },
    //добавляем локацию с список используемых
    addLatest(id, type) {
      let items = this.getLatest();

      items.push({
        id: id,
        type: type
      });

      //удаляем дубли
      items = items.filter(function (value, index, self) {
        let values = self.map(function (o) {
          return o.type + ':' + o.id;
        });

        return values.indexOf(value.type + ':' + value.id) === index;
      });

      if (items.length > 3)
        items.shift();

      localStorage.setItem('last_search_location', JSON.stringify(items));
    },
    //закрываем окно, отдаем родителю, что нужно свернуть
    closeWindow() {
      this.$emit("select-location-close");
    },
    //событие на выбор наименований
    selectItems() {
      let component = this;

      this.selected_stations.forEach(function (v, k) {
        component.addLatest(v, 'station');
      });

      this.selected_cities.forEach(function (v, k) {
        component.addLatest(v, 'city');
      });

      this.$emit("select-location-select", this.getText(), this.selected_stations, this.selected_cities);
      this.$emit("select-location-close");
    },
    //информация в виде превью строки
    getText() {
      let result = [];
      let component = this;

      if (component.systemData) {
        component.selected_cities.forEach(function (v, k) {
          let data = component.getItem(v, component.systemData.cities);
          result.push('г. ' + data.name);
        });

        component.selected_stations.forEach(function (v, k) {
          let data = component.getItem(v, component.systemData.stations);
          result.push('м. ' + data.name);
        });
      }

      let output = result.join(', ');

      if (output.length > this.maxTextLength)
        output = output.substring(0, this.maxTextLength - 3) + '...';

      if (output === '')
        output = this.defaultText;

      return output;
    },
    //выставление инициируемыех значений
    setInitItems() {
      this.selected_cities = this.initCities;
      this.selected_stations = this.initStations;
    }
  }
}
</script>

<style scoped lang="scss">
@import 'assets/scss/var.scss';
@import 'assets/scss/components/input.scss';


.select-location {
  position: fixed;
  font-family: $base-font;
  background: $white;
  z-index: 100;
  width: -webkit-fill-available;
  width: -moz-available;
  height: calc(100% - 100px);
  top: 0;
  padding: 50px;
  display: none;
  overflow: unset;
  border-left: 1px solid #c2c2c2;

  &::-webkit-scrollbar {
    width: 5px;
  }

  &::-webkit-scrollbar-track {
    background-color: #f4f4f4;
  }

  &::-webkit-scrollbar-thumb {
    background: $base-blue;
    border-radius: 5px;
  }

  h4 {
    margin-top: 0px;
    margin-bottom: 20px;
  }

  &.active {
    display: block;
    overflow-y: scroll;
    overflow-x: hidden;
  }

  .clear-input {
    position: absolute;
    margin-left: -25px;
    margin-top: 14px;
  }

  span.metro-icon {
    width: 8px;
    height: 8px;
    display: inline-block;
    border-radius: 8px;
    margin-right: 5px;

    &.is-white {
      border: 1px solid #d9d9d9;
    }
  }

  .head {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;

    .title-block {
      .title {
        font-weight: 700;
        font-size: 24px;
        margin-bottom: 5px;
      }

      .subtitle {
        font-size: 18px;
      }
    }

    .close {
      cursor: pointer;
      margin: auto 0;
    }
  }

  .content {
    margin-bottom: 20px;
    overflow-y: auto;
    &::-webkit-scrollbar {
      width: 5px;
    }

    &::-webkit-scrollbar-track {
      background-color: #f4f4f4;
    }

    &::-webkit-scrollbar-thumb {
      background: $base-blue;
      border-radius: 5px;
    }
  }

  .search {
    font-size: 14px;
    margin-bottom: 35px;

    input {
      width: 435px;
      margin-bottom: 10px;
    }
  }

  .alphabet-list {
    margin-bottom: 35px;

    .list-letters {
      &.selected-values {
        span.line {
          margin-right: 20px;
          white-space: nowrap;
        }
      }

      span {
        margin-right: 8px;
        cursor: pointer;
        font-weight: 400;
        font-size: 16px;
        color: #3F85EF;

        &.active {
          font-weight: 700;
        }
      }
    }

    .latest-search {
      display: inline-block;
    }
  }

  .bottom {
    display: flex;
    justify-content: flex-end;
    position: fixed;
    box-sizing: border-box;
    bottom: 0;
    padding-right: 100px;
    height: 100px;
    width: 100%;
    background: white;
    border-top: 1px solid #f5f5f5;

    button {
      font-family: $base-font;
      margin-left: 20px;
      margin-top: 25px;
      height: 50px;

      &.white-button {
        background: $white;
        color: $base-blue;

        &:hover {
          background: $base-blue;
          border-color: $base-blue;
          color: $white;
        }
      }
    }
  }

  .alphabet-metro {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 30px;

    .metro-item {
      margin-bottom: 7px;
      line-height: 1.1;
      cursor: pointer;

      &.selected .name {
        font-weight: 500;
        text-decoration: underline;
      }
    }

    @media (max-width: $width_tablet) {
      overflow-y: auto;
      max-height: 300px;
    }

    .letter-block {
      white-space: nowrap;
      display: inline-block;
    }

    .row {
      column-count: 6;
      width: 100%;

      @media (max-width: $width_big_PC) {
        column-count: 4;
      }

      @media (max-width: $width_PC) {
        column-count: 3;
      }

      @media (max-width: $width_tablet) {
        column-count: 2;
      }

      @media (max-width: $width_mobile) {
        column-count: 1
      }

      &.empty-results, &.one-column {
        column-count: 1;
      }
    }

    .letter {
      font-style: normal;
      font-weight: 600;
      font-size: 16px;
      line-height: 20px;
      color: $base-blue;
      margin-bottom: 10px;
      margin-top: 20px;

      &.is-first {
        margin-top: 0;
      }
    }
  }

  @media (max-width: $width_PC) {
    padding: 20px;
    //width: calc(100% - 40px);
    height: calc(100% - 40px);

    .head .close {
      margin: unset;
    }

    .head .title-block .title {
      font-size: 20px;
    }
    .head .title-block .subtitle {
      font-size: 14px;
    }

    .search input {
      width: -webkit-fill-available;
      width: -moz-available;
    }

    .alphabet-list .list-letters {
      width: -webkit-fill-available;
      width: -moz-available;
      overflow-wrap: break-word;
    }

    .alphabet-metro {
      &::-webkit-scrollbar {
        width: 3px;
      }

      &::-webkit-scrollbar-track {
        background-color: #f4f4f4;
      }

      &::-webkit-scrollbar-thumb {
        background: $base-blue;
        border-radius: 2px;
      }
    }

    .content {
      margin-bottom: 30px;
    }

    .bottom {
      right: 0;
      justify-content: space-between;
      bottom: 0;
      button {
        margin-right: 20px;
      }
    }
  }
  @media (max-width: $width_mobile) {
    .search,.alphabet-list{
      margin-bottom: 10px;
    }
    h4{
      margin-bottom: 10px;
    }
  }

}

</style>
