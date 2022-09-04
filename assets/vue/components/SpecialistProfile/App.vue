<template>
  <div class="page">
    <nav aria-label="Breadcrumb" class="breadcrumb">
      <ul>
        <li>
          <a href="#">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M17.5165 7.82913C17.5161 7.82872 17.5157 7.82831 17.5152 7.8279L10.1719 0.485596C9.85892 0.172485 9.44277 0 9.00011 0C8.55746 0 8.14131 0.172348 7.82816 0.485458L0.488685 7.82405C0.486213 7.82652 0.483741 7.82913 0.481269 7.8316C-0.161497 8.47801 -0.160398 9.52679 0.484428 10.1716C0.779029 10.4663 1.16812 10.637 1.58413 10.6548C1.60103 10.6565 1.61806 10.6573 1.63523 10.6573H1.9279V16.0608C1.9279 17.13 2.79797 18 3.8676 18H6.74054C7.03171 18 7.26794 17.7639 7.26794 17.4727V13.2363C7.26794 12.7484 7.66486 12.3515 8.15284 12.3515H9.84738C10.3354 12.3515 10.7323 12.7484 10.7323 13.2363V17.4727C10.7323 17.7639 10.9684 18 11.2597 18H14.1326C15.2023 18 16.0723 17.13 16.0723 16.0608V10.6573H16.3437C16.7862 10.6573 17.2024 10.4849 17.5157 10.1718C18.1612 9.52597 18.1614 8.4754 17.5165 7.82913Z"
                  fill="#3F85EF"/>
            </svg>
          </a>
        </li>
        <li>
          <a href="#">
            Профиль специалиста
          </a>
        </li>
      </ul>
    </nav>
    <h1>Профиль специалиста</h1>
    <div class="note">
      На модерации
    </div>
    <Warning title="Ваш профиль не участвует в системе"
             subtitle="Для активации вашего профиля необходимо пройти модерацию!"/>
    <Warning title="Сообщение от модератора"
             subtitle="Пожалуйста, загрузите медицинский сертификат и загрузите документы подтверждающие ваше медицинское образование"/>
    <div class="wrapper">
      <div class="title">
        Информация специалиста
      </div>
      <div class="subtitle">
        Данная информация будет отображаться на странице вашего профиля
      </div>
      <div class="item" id="gender">
        <div class="label">
          Пол
        </div>
        <div class="content">
          <input type="radio" name="sex" id="man" value="male" v-on:keyup.enter="updateSpecialistProfile" v-model="input_gender" checked>
          <label for="man">Мужской</label>
          <input type="radio" name="sex" id="woman" value="female" v-on:keyup.enter="updateSpecialistProfile" v-model="input_gender">
          <label for="woman">Женский</label>
        </div>
        <div class="errorText" v-if="error.gender">
          {{ error.gender }}
        </div>
      </div>
      <div class="item cities" id="cities_id">
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
        <div class="label">
          Местоположение
        </div>
        <div class="content" v-on:click="select_location.open = true">
          <p>{{ select_location.text }}</p>
          <svg width="11" height="8" viewBox="0 0 11 8" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 1.5L5.5 6.5L10 1.5" stroke="#283848" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="errorText" v-if="error.cities_id">
          {{ error.cities_id }}
        </div>
        <div class="errorText" v-if="error.stations_id">
          {{ error.stations_id }}
        </div>
      </div>
      <div class="item" id="region_id">
        <div class="label">
          Населенный пункт
        </div>
        <div class="content">
          <p>Московская обл.</p>
        </div>
        <div class="errorText" v-if="error.region_id">
          {{ error.region_id }}
        </div>
      </div>
      <div class="item" id="callback_phone">
        <div class="label">
          Телефон для связи
        </div>
        <div class="content">
          <vue-mask class="form-control inputPhone"
                    mask="+7 (000) 000-00-00"
                    id="phone"
                    v-model="callback_phone"
                    v-bind:class="{invalid: error.phone}"
                    @focus="error.callback_phone = ''"
                    v-on:keyup.enter="updateSpecialistProfile"
                    type="text"
                    :raw="false"
                    :options="phoneOptions">
          </vue-mask>
          <div class="errorText" v-if="error.callback_phone">
            {{ error.callback_phone }}
          </div>
        </div>
      </div>
      <div class="item service" id="services">
        <div class="label">
          Выберите оказываемые услуги и стоимость
        </div>
        <div class="serviceItem" v-for="(service, index) in services" :key="service.id">
          <input type="checkbox" v-bind:id="'service-'+service.id" class="serviceCheck" v-on:keyup.enter="updateSpecialistProfile" v-model="service.checked">
          <label v-bind:for="'service-'+service.id">
            <span class="pic">
              <img :src="'/images/service-icons/'+service.icon+'.svg'" alt="icon">
            </span>
            <span class="serviceName">{{ service.name }}</span>
            <input type="text" class="servicePrice" name="price" v-on:keyup.enter="updateSpecialistProfile" v-bind:id="'servicePrice-'+service.id"
                   v-model="service.price">
            <span>руб.</span>
          </label>
        </div>
        <a href="#" class="support">
          Нет нужной услуги? Напишите в поддержку!
        </a>
        <div class="errorText" v-if="error.services">
          {{ error.services }} <br>
          {{ error.services[0].price }}
        </div>
      </div>
      <div class="item timesheet" id="worktime">
        <div class="label">
          Время работы
        </div>
        <div class="timeArea">
          <div class="days">
            <div class="empty"></div>
            <div class="day" v-for="day in days">
              {{ day.day }}
            </div>
          </div>
          <div class="list">
            <div class="item" v-for="hour in hours" :key="hour.id">
              <div class="time">{{ hour.hour }}</div>
              <div v-for="n in days.length" class="checkbox-parent">
                <input class="timeCheck" type="checkbox" v-model="checkedValues" v-on:keyup.enter="updateSpecialistProfile" :value="{day: n, hour: hour.id}"
                       v-bind:data-hour="'hour-'+hour.id" v-bind:data-day="'day-'+n">
              </div>
            </div>
          </div>
        </div>
        <div class="errorText" v-if="error.worktime">
          {{ error.worktime }}
        </div>
        <div class="allow">
          <input type="checkbox" id="allow" v-model="input_time_range">
          <label for="allow">
            Разрешить искать +/- 1 час
          </label>
        </div>
        <div class="errorText" v-if="error.time_range">
          {{ error.time_range }}
        </div>
      </div>
      <div class="item loadImage">
        <div class="label">
          <p>Документы для публичного доступа</p>
          <div class="load">
            <label>
              <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_291_131)">
                  <path d="M16 16.5L12 12.5L8 16.5" stroke="#87B1CA" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"/>
                  <path d="M12 12.5V21.5" stroke="#87B1CA" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"/>
                  <path
                      d="M20.3895 18.8914C21.3648 18.3597 22.1353 17.5183 22.5793 16.5001C23.0234 15.4818 23.1157 14.3447 22.8417 13.2681C22.5677 12.1916 21.943 11.237 21.0661 10.5549C20.1893 9.87283 19.1103 9.50218 17.9995 9.50145H16.7395C16.4368 8.33069 15.8726 7.24378 15.0894 6.32244C14.3062 5.4011 13.3243 4.6693 12.2176 4.18206C11.1108 3.69481 9.90802 3.46481 8.69959 3.50933C7.49116 3.55385 6.30854 3.87175 5.24065 4.43911C4.17276 5.00648 3.24738 5.80855 2.53409 6.78503C1.8208 7.76151 1.33816 8.88699 1.12245 10.0768C0.906742 11.2667 0.963577 12.49 1.28869 13.6547C1.61379 14.8194 2.19871 15.8953 2.99947 16.8014"
                      stroke="#87B1CA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M16 16.5L12 12.5L8 16.5" stroke="#87B1CA" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"/>
                </g>
                <defs>
                  <clipPath id="clip0_291_131">
                    <rect width="24" height="24" fill="white" transform="translate(0 0.5)"/>
                  </clipPath>
                </defs>
              </svg>
              <p>Загрузить</p>
            </label>
            <input type="file" name="publicDoc" id="public_docs" multiple v-on:change="loadFiles">
          </div>
        </div>
        <div class="preview public_docs">
          <div class="previewPic" v-for="(url, index) in public_docs_url">
            <div class="deletePreview" v-on:click="removePhoto(index, 'public_docs')">
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 1L1 13" stroke="#283848" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M1 1L13 13" stroke="#283848" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <img :src="url.filepath" alt="url">
          </div>
        </div>
      </div>
      <div class="item loadImage">
        <div class="label">
          <p>Фотографии</p>
          <div class="load">
            <label>
              <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_291_131)">
                  <path d="M16 16.5L12 12.5L8 16.5" stroke="#87B1CA" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"/>
                  <path d="M12 12.5V21.5" stroke="#87B1CA" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"/>
                  <path
                      d="M20.3895 18.8914C21.3648 18.3597 22.1353 17.5183 22.5793 16.5001C23.0234 15.4818 23.1157 14.3447 22.8417 13.2681C22.5677 12.1916 21.943 11.237 21.0661 10.5549C20.1893 9.87283 19.1103 9.50218 17.9995 9.50145H16.7395C16.4368 8.33069 15.8726 7.24378 15.0894 6.32244C14.3062 5.4011 13.3243 4.6693 12.2176 4.18206C11.1108 3.69481 9.90802 3.46481 8.69959 3.50933C7.49116 3.55385 6.30854 3.87175 5.24065 4.43911C4.17276 5.00648 3.24738 5.80855 2.53409 6.78503C1.8208 7.76151 1.33816 8.88699 1.12245 10.0768C0.906742 11.2667 0.963577 12.49 1.28869 13.6547C1.61379 14.8194 2.19871 15.8953 2.99947 16.8014"
                      stroke="#87B1CA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M16 16.5L12 12.5L8 16.5" stroke="#87B1CA" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"/>
                </g>
                <defs>
                  <clipPath id="clip0_291_131">
                    <rect width="24" height="24" fill="white" transform="translate(0 0.5)"/>
                  </clipPath>
                </defs>
              </svg>
              <p>Загрузить</p>
            </label>
            <input type="file" name="photoDoc" id="public_photo" multiple v-on:change="loadFiles">
          </div>
        </div>
        <div class="preview public_photo">
          <div class="previewPic" v-for="(url, index) in public_photo_url">
            <div class="deletePreview" v-on:click="removePhoto(index, 'public_photo')">
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 1L1 13" stroke="#283848" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M1 1L13 13" stroke="#283848" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <img :src="url.filepath" alt="url">
          </div>
        </div>
      </div>
      <div class="item loadImage">
        <div class="label">
          <p>Документы для модерации</p>
          <div class="info">
            <p>На сайте не отображаются</p>
            <p>
              <span>
                Загрузите обязательные документы: диплом, селфи с паспортом
                и закрытым номером, документы
                о мед. образовании.
              </span>
            </p>
          </div>
          <div class="load">
            <label>
              <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_291_131)">
                  <path d="M16 16.5L12 12.5L8 16.5" stroke="#87B1CA" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"/>
                  <path d="M12 12.5V21.5" stroke="#87B1CA" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"/>
                  <path
                      d="M20.3895 18.8914C21.3648 18.3597 22.1353 17.5183 22.5793 16.5001C23.0234 15.4818 23.1157 14.3447 22.8417 13.2681C22.5677 12.1916 21.943 11.237 21.0661 10.5549C20.1893 9.87283 19.1103 9.50218 17.9995 9.50145H16.7395C16.4368 8.33069 15.8726 7.24378 15.0894 6.32244C14.3062 5.4011 13.3243 4.6693 12.2176 4.18206C11.1108 3.69481 9.90802 3.46481 8.69959 3.50933C7.49116 3.55385 6.30854 3.87175 5.24065 4.43911C4.17276 5.00648 3.24738 5.80855 2.53409 6.78503C1.8208 7.76151 1.33816 8.88699 1.12245 10.0768C0.906742 11.2667 0.963577 12.49 1.28869 13.6547C1.61379 14.8194 2.19871 15.8953 2.99947 16.8014"
                      stroke="#87B1CA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M16 16.5L12 12.5L8 16.5" stroke="#87B1CA" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"/>
                </g>
                <defs>
                  <clipPath id="clip0_291_131">
                    <rect width="24" height="24" fill="white" transform="translate(0 0.5)"/>
                  </clipPath>
                </defs>
              </svg>
              <p>Загрузить</p>
            </label>
            <input type="file" name="privateDoc" id="private_docs" multiple v-on:change="loadFiles">
          </div>
        </div>
        <div class="preview private_docs">
          <div class="previewPic" v-for="(url, index) in private_docs_url">
            <div class="deletePreview" v-on:click="removePhoto(index, 'private_docs')">
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 1L1 13" stroke="#283848" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M1 1L13 13" stroke="#283848" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <img :src="url.filepath" alt="url">
          </div>
        </div>
      </div>
      <div class="item experience" id="experience">
        <div class="label">Стаж работы в годах</div>
        <input type="text" placeholder="10" v-model="input_experience" v-on:keyup.enter="updateSpecialistProfile" @focus="error.experience = ''">
        <div class="errorText" v-if="error.experience">
          {{ error.experience }}
        </div>
      </div>
      <div class="item education" id="education">
        <div class="label">
          Образование
        </div>
        <div class="educationItem" v-for="(school, index) in input_education">
          <div class="column name">
            <p>Название</p>
            <input type="text" class="input_school" v-model="school.university" v-on:keyup.enter="updateSpecialistProfile" @focus="error.education = ''"
                   placeholder="РУДН, юридический факультет, специальность....">
            <div class="errorText" v-if="error.education">
              {{ error.education[0].university }}
            </div>
          </div>
          <div class="column from">
            <p>от</p>
            <input type="text" class="input_from" v-model="school.from" v-on:keyup.enter="updateSpecialistProfile" placeholder="2011 г."
                   @focus="error.education = ''">
            <div class="errorText" v-if="error.education">
              {{ error.education[0].from }}
            </div>
          </div>
          <div class="column to">
            <p>до</p>
            <input type="text" class="input_to" v-model="school.to" placeholder="2015 г." v-on:keyup.enter="updateSpecialistProfile" @focus="error.education = ''">
            <div class="errorText" v-if="error.education">
              {{ error.education[0].to }}
            </div>
          </div>
          <div class="deleteEducation" v-on:click="input_education.splice(index,1)">
            <svg
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
              <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M17 5V4C17 2.89543 16.1046 2 15 2H9C7.89543 2 7 2.89543 7 4V5H4C3.44772 5 3 5.44772 3 6C3 6.55228 3.44772 7 4 7H5V18C5 19.6569 6.34315 21 8 21H16C17.6569 21 19 19.6569 19 18V7H20C20.5523 7 21 6.55228 21 6C21 5.44772 20.5523 5 20 5H17ZM15 4H9V5H15V4ZM17 7H7V18C7 18.5523 7.44772 19 8 19H16C16.5523 19 17 18.5523 17 18V7Z"
                  fill="#87B1CA"
              />
              <path d="M9 9H11V17H9V9Z" fill="#87B1CA"/>
              <path d="M13 9H15V17H13V9Z" fill="#87B1CA"/>
            </svg>
          </div>
        </div>
        <button v-on:click="input_education.push({university: '', from: null, to: null})">
          Добавить место учебы
        </button>
      </div>
      <div class="item about" id="about">
        <div class="label">
          Обо мне
        </div>
        <textarea v-model="input_about" v-on:keyup.enter="updateSpecialistProfile">
        </textarea>
        <div class="errorText" v-if="error.about">
          {{ error.about }}
        </div>
        <div class="errorText" v-if="error['#']">
          {{ error['#'] }}
        </div>
        <button v-on:click="updateSpecialistProfile" class="parent-spinner hover-spinner-is-blue">
          <Spinner :isActive="info_is_updated"></Spinner>
          Отправить на модерацию
        </button>
      </div>
    </div>
    <SuccessAlert v-if="profile_is_updated" @closeModal="closeSuccessAlert" :title="'Данные сохранены'"
                  :subtitle="'Обновленная информация будет отображаться в вашем профиле'" :linkTitle="'Вернуться'" :link="this_page" :is-success="true"/>
  </div>
</template>

<script>
import {mapGetters} from "vuex";

import vueMask from "vue-jquery-mask";
import Spinner from "@/components/UI/Spinner/Spinner.vue";
import SuccessAlert from "@/components/UI/SuccessAlert/SuccessAlert.vue";
import Warning from "@/components/UI/Warning/Warning.vue";
import SelectLocation from "@/components/UI/SelectLocation/SelectLocation.vue";

import SpecialistService from "@/api/SpecialistService";
import FileService from "@/api/FileService";
import ToolkitService from "@/common/toolkit";
import {EXCEPTION_NAME_EXTERNAL, EXCEPTION_NAME_ERROR_FIELDS} from '@/codes';

import {GETTER_SYSTEM_DATA} from '@/store/types/getters';
import {GETTER_USER_DATA} from '@/store/types/getters';


export default {
  name: 'SpecialistProfile',
  data() {
    return {
      this_page: window.location.href,
      profile_is_updated: false,
      info_is_updated: false,
      region_id: 1,
      checkedValues: [],
      checkedServices: [],
      stations_id: [],
      cities_id: [],
      callback_phone: '',
      services: [],
      days: [
        {
          day: 'Пн'
        },
        {
          day: 'Вт'
        },
        {
          day: 'Ср'
        },
        {
          day: 'Чт'
        },
        {
          day: 'Пт'
        },
        {
          day: 'Сб'
        },
        {
          day: 'Вс'
        }
      ],
      hours: [
        {
          id: 1,
          hour: '00:00'
        },
        {
          id: 2,
          hour: '01:00'
        },
        {
          id: 3,
          hour: '02:00'
        },
        {
          id: 4,
          hour: '03:00'
        },
        {
          id: 5,
          hour: '04:00'
        },
        {
          id: 6,
          hour: '05:00'
        },
        {
          id: 7,
          hour: '06:00'
        },
        {
          id: 8,
          hour: '07:00'
        },
        {
          id: 9,
          hour: '08:00'
        },
        {
          id: 10,
          hour: '09:00'
        },
        {
          id: 11,
          hour: '10:00'
        },
        {
          id: 12,
          hour: '11:00'
        },
        {
          id: 13,
          hour: '12:00'
        },
        {
          id: 14,
          hour: '13:00'
        },
        {
          id: 15,
          hour: '14:00'
        },
        {
          id: 16,
          hour: '15:00'
        },
        {
          id: 17,
          hour: '16:00'
        },
        {
          id: 18,
          hour: '17:00'
        },
        {
          id: 19,
          hour: '18:00'
        },
        {
          id: 20,
          hour: '19:00'
        },
        {
          id: 21,
          hour: '20:00'
        },
        {
          id: 22,
          hour: '21:00'
        },
        {
          id: 23,
          hour: '22:00'
        },
        {
          id: 24,
          hour: '23:00'
        },
      ],
      data_worktime: [],
      input_gender: '',
      input_services: [],
      input_worktime: [],
      input_time_range: false,
      docs: {
        public_photo: [],
        public_docs: [],
        private_docs: []
      },
      input_public_photo: [],
      input_public_docs: [],
      input_private_docs: [],
      public_photo_url: [],
      public_docs_url: [],
      private_docs_url: [],
      input_experience: '',
      input_education: [
        {
          "university": '',
          "from": null,
          "to": null
        }
      ],
      input_about: 'Напишите что-нибудь',
      phoneOptions: {
        placeholder: '+7 (900) 000-00-00',
        clearIfNotMatch: true
      },
      select_location: {
        stations: [],
        cities: [],
        text: '',
        open: false,
      },
      stations: [],
      cities: [],
      error: {}
    }
  },
  mounted() {
  },
  computed: {
    ...mapGetters({
      'service_info': GETTER_SYSTEM_DATA,
      'userdata': GETTER_USER_DATA,
    })
  },
  methods: {
    // data collecting methods
    checkServices() {
      let component = this;

      this.services.forEach((service) => {
        if (service.checked === true) {
          component.input_services.push({service_id: service.id, price: service.price})
        }
      })
    },
    collectWorkHours() {
      let component = this;

      this.checkedValues.forEach((value) => {
        let day = value.day;
        let hour = value.hour;
        component.input_worktime.push({day: day, hour: hour});
      })
    },
    //file processing methods
    async loadFiles(event) {
      let component = this;
      const files = event.target.files;
      let doctype = event.target.id;
      let fileData;

      for (let i = 0; i < files.length; i++) {

        await this.getBase64(files[i]).then(function (response) {
          fileData = response;
        })
        let url = URL.createObjectURL(files[i]);
        if (doctype === 'public_photo') {
          this.docs.public_photo.push({filename: files[i].name, filetype: 'public_photo', filedata: fileData})
          component.public_photo_url.push({"id": i, "filepath": url, "filetype": 'public_photo'})
        } else if (doctype === 'public_docs') {
          this.docs.public_docs.push({filename: files[i].name, filetype: 'public_docs', filedata: fileData})
          component.public_docs_url.push({"id": i, "filepath": url, "filetype": 'public_docs'});
        } else if (doctype === 'private_docs') {
          this.docs.private_docs.push({filename: files[i].name, filetype: 'private_docs', filedata: fileData})
          component.private_docs_url.push({"id": i, "filepath": url, "filetype": 'private_docs'});
        }
      }
    },
    removePhoto(index, doctype) {
      if (doctype === 'private_docs') {
        this.docs.private_docs.splice(index, 1);
        this.private_docs_url.splice(index, 1);
      } else if (doctype === 'public_docs') {
        this.docs.public_docs.splice(index, 1);
        this.public_docs_url.splice(index, 1);
      } else if (doctype === 'public_photo') {
        this.docs.public_photo.splice(index, 1);
        this.public_photo_url.splice(index, 1);
      }
    },
    getBase64(inputFile) {
      const temporaryFileReader = new FileReader();

      return new Promise((resolve, reject) => {
        temporaryFileReader.onerror = () => {
          temporaryFileReader.abort();
          reject(new DOMException("Problem parsing input file."));
        };

        temporaryFileReader.onload = () => {
          resolve(temporaryFileReader.result);
        };
        temporaryFileReader.readAsDataURL(inputFile);
      });
    },

    //update info method
    updateSpecialistProfile: async function () {
      let component = this;

      this.info_is_updated = true;
      let file_promises = [];

      if (this.docs.public_photo) {
        this.docs.public_photo.forEach((doc) => {
          file_promises.push(new Promise(async function (resolve, reject) {

            FileService
                .uploadFile(doc.filename, doc.filetype, doc.filedata)
                .then(function (response) {
                  if (response.data.status === true) {
                    component.input_public_photo.push(response.data.id.toString());

                    resolve();
                  }
                })
                .catch(function (error) {
                  if (error.name === EXCEPTION_NAME_ERROR_FIELDS)
                    component.error = error.values;
                  else if (error.name === EXCEPTION_NAME_EXTERNAL)
                    component.error = error.text;

                  component.info_is_updated = false;
                  resolve();
                })
          }))
        })
      }

      if (this.docs.public_docs) {
        this.docs.public_docs.forEach((doc) => {
          file_promises.push(new Promise(async function (resolve, reject) {

            FileService
                .uploadFile(doc.filename, doc.filetype, doc.filedata)
                .then(function (response) {
                  if (response.data.status === true) {
                    component.input_public_docs.push(response.data.id.toString());

                    component.info_is_updated = false;
                    resolve();
                  }
                })
                .catch(function (error) {
                  if (error.name === EXCEPTION_NAME_ERROR_FIELDS)
                    component.error = error.values;
                  else if (error.name === EXCEPTION_NAME_EXTERNAL)
                    component.error = error.text;

                  component.info_is_updated = false;
                  resolve();
                })
          }))
        })
      }

      if (this.docs.private_docs) {
        this.docs.private_docs.forEach((doc) => {
          file_promises.push(new Promise(async function (resolve, reject) {

            FileService
                .uploadFile(doc.filename, doc.filetype, doc.filedata)
                .then(function (response) {
                  if (response.data.status === true) {
                  }
                  component.input_private_docs.push(response.data.id.toString());

                  resolve();
                })
                .catch(function (error) {
                  if (error.name === EXCEPTION_NAME_ERROR_FIELDS)
                    component.error = error.values;
                  else if (error.name === EXCEPTION_NAME_EXTERNAL)
                    component.error = error.text;

                  resolve();
                })
          }))
        })
      }

      Promise.all(file_promises)
          .then(function (values) {

            component.checkServices();
            component.collectWorkHours();

            let update_profile = {
              gender: component.input_gender,
              region_id: component.region_id,
              stations_id: component.stations_id,
              cities_id: component.cities_id,
              callback_phone: component.callback_phone,
              services: component.input_services,
              worktime: component.input_worktime,
              time_range: component.input_time_range,
              public_photo: component.input_public_photo,
              public_docs: component.input_public_docs,
              private_docs: component.input_private_docs,
              experience: component.input_experience,
              education: component.input_education,
              about: component.input_about
            };

            SpecialistService
                .updateSpecialistProfile(update_profile)
                .then(function (response) {
                  if (response.data.status === true) {
                    component.info_is_updated = false;
                    component.profile_is_updated = true;
                    component.clearArrays();
                  }
                })
                .catch(function (error) {
                  if (error.name === EXCEPTION_NAME_ERROR_FIELDS) {
                    ToolkitService.scrollElementByError(error.values);
                    component.error = error.values;
                  } else if (error.name === EXCEPTION_NAME_EXTERNAL)
                    component.error = error.text;

                  component.info_is_updated = false;
                  update_profile.services = ''
                  component.clearArrays();
                })
          })
    },

    //utility methods
    closeSuccessAlert() {
      this.profile_is_updated = false;
    },
    clearArrays() {
      this.docs.public_photo = [];
      this.docs.public_docs = [];
      this.docs.private_docs = [];
      this.input_services = [];
      this.input_worktime = [];
    },

    //select methods
    setLocations(text, station_ids, cities_ids) {
      this.stations_id = station_ids;
      this.cities_id = cities_ids;
      this.select_location.text = text;
    },
    closeSelectLocation() {
      this.select_location.open = false;
    }
  },
  watch: {
    service_info(service_info, old_val) {
      this.services = this.service_info?.services;
    },
    userdata(userdata, old_val) {
      this.callback_phone = this.userdata?.userinfo?.phone;
      this.input_gender = this.userdata?.userinfo?.gender;
      this.input_experience = this.userdata?.specialistinfo?.experience;
      this.input_education = this.userdata?.specialistinfo?.education;
      this.input_about = this.userdata?.specialistinfo?.about;
      this.checkedValues = this.userdata?.specialistinfo?.worktime;

      let component = this;
      this.service_info?.services.forEach((element) => {
        this.userdata?.specialistinfo?.services.forEach((item) => {
          if (element.id === item.service.id) {
            component.services[item.service.id].checked = true;
            component.services[item.service.id].price = item.price;
          }
        })
      })
    },
  },
  components: {
    SelectLocation,
    SuccessAlert,
    Spinner,
    Warning,
    vueMask
  }
}
</script>

<style scoped lang="scss">
@import 'assets/scss/default.scss';

.page {
  max-width: 1235px;
  width: 100%;
  margin: 0 auto;
  font-family: $base-font;
  font-style: normal;

  h1 {
    margin-top: 50px;
    font-style: normal;
    font-weight: 500;
    font-size: 35px;
    line-height: 50px;
    color: #3F85EF;
  }

  .note {
    margin-top: 8px;
    font-style: normal;
    font-weight: 600;
    font-size: 25px;
    line-height: 31px;
    color: #87B1CA;
    margin-bottom: 26px;
  }

  .wrapper {
    margin-top: 30px;
    width: -webkit-fill-available;
    width: -moz-available;
    background: #FFFFFF;
    box-shadow: 0 5px 35px rgba(21, 30, 41, 0.1);
    border-radius: 15px;
    padding: 50px 80px;

    .title {
      font-weight: 600;
      font-size: 25px;
      line-height: 31px;
      color: #283848;
      margin-bottom: 14px;
    }

    .subtitle {
      font-weight: 400;
      font-size: 14px;
      line-height: 16px;
      color: #283848;
      margin-bottom: 35px;
    }

    .item {
      display: flex;
      align-items: center;
      padding: 33px 0;
      border-bottom: 2px solid #F0F1F8;

      .label {
        font-weight: 600;
        font-size: 18px;
        line-height: 22px;
        color: #283848;
        width: 278px;
      }

      .content {
        label {
          font-weight: 400;
          font-size: 16px;
          line-height: 19px;
          color: #283848;
          margin: 0 17px 0 9px;
        }

        .inputPhone {
          width: 253px;
        }
      }
    }

    .cities{
      .content{
        cursor: pointer;
        background: #FFFFFF;
        border: 1px solid #87B1CA;
        border-radius: 5px;
        display: flex;
        align-items: center;
        box-sizing: border-box;
        padding: 12px 17px;
        p{
          font-weight: 400;
          font-size: 14px;
          line-height: 16px;
          color: #283848;
          margin-right: 10px;
        }
      }
    }

    .service {
      flex-direction: column;
      align-items: flex-start;

      .label {
        width: unset;
        margin-bottom: 14px;
      }

      .serviceItem {
        display: flex;
        align-items: center;

        input[type="checkbox"] {
          border: 2px solid #87B1CA;
          opacity: 0.6;
          width: 22px;
          height: 22px;
          border-radius: 5px;
        }

        label {
          opacity: 0.6;
          display: flex;
          align-items: center;
          margin-left: 10px;

          span {
            font-weight: 400;
            font-size: 16px;
            line-height: 19px;
            color: #283848;
          }

          .pic {
            margin-right: 10px;
          }
        }

        input[type="checkbox"]:checked {
          opacity: 1;
        }

        input[type="checkbox"]:checked + label {
          opacity: 1;
        }

        input[type="text"] {
          width: 40px;
          border: none;
          margin: 0 10px;
          padding: 3px 16px;
          background: #E6F7F6;
          border-radius: 5px;
          font-size: 16px;
          line-height: 19px;
        }
      }

      .support {
        font-weight: 600;
        font-size: 16px;
        line-height: 20px;
        text-decoration-line: underline;
        color: #3F85EF;
        margin-top: 35px;
      }
    }

    .timesheet {
      flex-direction: column;
      align-items: flex-start;

      .label {
        margin-bottom: 14px;
      }

      .timeArea {
        background: #EDF4FE;
        border-radius: 10px;
        width: 548px;
        position: relative;
        padding-top: 40px;
        padding-right: 18px;

        .days {
          display: flex;
          align-items: center;
          position: absolute;
          right: 0;
          top: 10px;

          .empty {
            width: 100%;
          }

          .day {
            margin-right: 44px;
            width: 50px;
            font-style: normal;
            font-weight: 600;
            font-size: 14px;
            line-height: 17px;
          }
        }

        .list {
          height: 445px;
          overflow-y: auto;
          overflow-x: hidden;

          .item {
            padding: 5px 20px;
            display: flex;
            align-items: center;

            .time {
              min-width: 93px;
              font-style: normal;
              font-weight: 600;
              font-size: 14px;
              line-height: 17px;
            }

            .checkbox-parent {
              display: inline-block;
              position: relative;
              margin-right: 32px;
              cursor: pointer;
              font-size: 22px;
              -webkit-user-select: none;
              user-select: none;

              input {
                width: 22px;
                height: 22px;
                border: 2px solid #87b1ca;
                border-radius: 5px;
              }
            }
          }

          &::-webkit-scrollbar {
            width: 3px;
          }

          &::-webkit-scrollbar-track {
            background: #E0E0E0;
            width: 1px;
          }

          &::-webkit-scrollbar-thumb {
            background: #87B1CA;
            border-radius: 123px;
            width: 3px;
          }
        }
      }

      .allow {
        margin-top: 17px;
        display: flex;
        align-items: center;

        input {
          width: 23px;
          height: 23px;
          margin-right: 12px;
        }

        label {
          font-weight: 400;
          font-size: 14px;
          line-height: 16px;
          color: #283848;
        }
      }
    }

    .loadImage {
      overflow-x: auto;

      &::-webkit-scrollbar {
        width: 3px;
        height: 3px;
      }

      &::-webkit-scrollbar-track {
        background: #E0E0E0;
        width: 1px;
      }

      &::-webkit-scrollbar-thumb {
        background: #87B1CA;
        border-radius: 123px;
        width: 3px;
      }

      .label {
        position: relative;
        z-index: 0;

        .info {
          p {
            font-weight: 400;
            font-size: 16px;
            line-height: 19px;
            color: #283848;
            max-width: 243px;

            &:first-of-type {
              margin: 12px 0;
            }

            span {
              font-weight: 400;
              font-size: 14px;
              line-height: 20px;
              color: #283848;
            }
          }
        }

        .load {
          position: relative;
          cursor: pointer;
          display: flex;
          align-items: flex-start;
          margin-top: 17px;
          width: fit-content;

          label {
            position: relative;
            z-index: 1;
            cursor: pointer;
            display: flex;
            align-items: center;
            background: $white;
            border: 1px solid #87B1CA;
            border-radius: 39px;
            padding: 13px 23px;
            margin-top: 14px;

            p {
              margin: 0 0 0 11px;
              font-weight: 400;
              font-size: 14px;
              line-height: 16px;
              color: #283848;
            }
          }
        }
      }

      .preview {
        display: flex;
        margin-left: 20px;

        .previewPic {
          width: 200px;
          height: 200px;
          position: relative;
          margin-right: 20px;

          .deletePreview {
            position: absolute;
            z-index: 1;
            top: 15px;
            right: 15px;
            cursor: pointer;
          }

          img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            position: relative;
            z-index: 0;
          }
        }
      }

      input {
        width: 100%;
        height: 100%;
        position: absolute;
        z-index: 1;
        opacity: 0;
        top: 0;
        left: 0;
        padding: 0;
      }
    }

    .experience {
      flex-direction: column;
      align-items: flex-start;

      input {
        margin-top: 14px;
        width: 440px;
      }
    }

    .education {
      flex-direction: column;
      align-items: flex-start;

      .educationItem {
        display: flex;
        align-items: center;
        margin-top: 31px;
        flex-flow: row wrap;

        .column {
          display: flex;
          flex-direction: column;
          margin-right: 24px;
          position: relative;

          p {
            font-weight: 600;
            font-size: 16px;
            line-height: 20px;
            color: #283848;
            margin-bottom: 16px;
          }

          input {
            width: 60px;
          }

          .errorText {
            position: absolute;
            bottom: -20px;
            left: 0;
            max-width: 100%;
          }
        }

        .from, .to {
          .errorText {
            bottom: -30px;
            font-size: 10px;
          }
        }

        .name {
          input {
            width: 390px;
          }
        }

        .deleteEducation {
          margin-top: 40px;
          cursor: pointer;
        }
      }

      button {
        margin-top: 30px;
        background: $white;
        color: $base-blue;

        &:hover {
          background: $base-blue;
          color: $white;
        }
      }
    }

    .about {
      flex-direction: column;
      align-items: flex-start;

      textarea {
        padding: 17px 22px;
        margin-top: 14px;
        width: -webkit-fill-available;
        width: -moz-available;
        height: 150px;
        border: 1px solid #87B1CA;
        border-radius: 10px;
        font-family: $base-font;
        font-style: normal;
        font-weight: 400;
        font-size: 14px;
        line-height: 16px;
        outline: none;
      }

      button {
        margin-top: 33px;
      }
    }
  }
}

@media(max-width: 1024px) {
  .page {
    .wrapper {
      .loadImage {
        overflow-x: scroll;
      }
    }
  }
}

@media(max-width: 767px) {
  .page {
    max-width: 100%;
    padding: 24px;
    box-sizing: border-box;

    h1 {
      font-size: 28px;
      line-height: 35px;
    }

    .note {
      font-size: 20px;
      line-height: 28px;
    }

    .wrapper {
      padding: 35px 24px;

      .title {
        font-size: 22px;
        line-height: 24px;
      }

      .subtitle {
        margin-bottom: 29px;
      }

      .item {
        justify-content: space-between;

        .label {
          font-size: 16px;
        }

        .content {
          display: flex;

          .inputPhone {
            width: -webkit-fill-available;
            width: -moz-available;
          }
        }
      }
      .cities{
        .label{
          width: auto;
        }
      }
      .service {
        .serviceItem {
          label {
            .serviceName {
              width: 100px;
            }

            span {
              font-size: 12px;
              line-height: 15px;
            }
          }
        }
      }

      .timesheet {
        .timeArea {
          zoom: 0.5;
        }
      }

      .experience {
        input {
          width: -webkit-fill-available;
          width: -moz-available;
        }
      }

      .education {
        .educationItem {
          .column {
            input {
              width: 40px;
            }

            p {
              margin-bottom: 8px;
              margin-top: 10px;
            }
          }

          .name {
            input {
              width: -webkit-fill-available;
              width: -moz-available;
            }
          }
        }
      }
    }
  }
}

</style>