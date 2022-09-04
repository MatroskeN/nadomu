<template>
  <div class="page">
    <h1>
      Заполните информацию
    </h1>
    <div class="subtitle">
      Пожалуйста, укажите ваши данные
      для завершения регистрации
    </div>
    <div class="form">
      <div class="inputLabel">Ваш email</div>
      <input
          class="itemValue"
          type="email"
          id="email"
          v-model="input_email"
          v-bind:class="{invalid: !!error.email}"
          @focus="error.email = ''"
          v-on:keyup.enter="fillUserProfile"
          placeholder="vasiliy.petrov@gmail.com">
      <div class="errorText" v-if="error.email">
        {{ error.email }}
      </div>
      <div class="inputLabel">Ваша фамилия</div>
      <input
          class="itemValue"
          type="text"
          id="last_name"
          v-model="input_last_name"
          v-bind:class="{invalid: !!error.last_name}"
          @focus="error.last_name = ''"
          v-on:keyup.enter="fillUserProfile"
          placeholder="Петров">
      <div class="errorText" v-if="error.last_name">
        {{ error.last_name }}
      </div>
      <div class="inputLabel">Ваше имя</div>
      <input
          class="itemValue"
          type="text"
          id="first_name"
          v-model="input_name"
          v-bind:class="{invalid: !!error.first_name}"
          @focus="error.first_name = ''"
          v-on:keyup.enter="fillUserProfile"
          placeholder="Василий">
      <div class="errorText" v-if="error.first_name">
        {{ error.first_name }}
      </div>
      <div class="inputLabel">Ваше отчество</div>
      <input
          class="itemValue"
          type="text"
          id="patronymic_name"
          v-model="input_patronymic_name"
          v-bind:class="{invalid: !!error.patronymic_name}"
          @focus="error.patronymic_name = ''"
          v-on:keyup.enter="fillUserProfile"
          placeholder="Иванович">
      <div class="errorText" v-if="error.patronymic_name">
        {{ error.patronymic_name }}
      </div>
      <div class="specialist" v-if="is_specialist">
        <div class="inputLabel">Промокод</div>
        <input
            class="itemValue"
            type="text"
            id="promo"
            v-model="input_promo"
            v-bind:class="{invalid: !!error.promo}"
            @focus="error.promo = ''"
            v-on:keyup.enter="fillUserProfile"
            placeholder="">
        <div class="errorText" v-if="error.promo">
          {{ error.promo }}
        </div>
      </div>
      <button v-on:click="fillUserProfile" class="parent-spinner hover-spinner-is-blue">
        <Spinner :isActive="info_is_filled"></Spinner>
        Заполнить
      </button>
    </div>
    <SuccessAlert v-if="data_set" @closeModal="closeSuccessAlert" :title="'Данные сохранены'"
                  :subtitle="'Заполнить остальную информацию можно в вашем профиле'" :linkTitle="'На главную'" :link="'/'" :is-success="true"/>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

import UserService from "@/api/UserService";
import {EXCEPTION_NAME_EXTERNAL, EXCEPTION_NAME_ERROR_FIELDS} from '@/codes';

import SuccessAlert from "@/components/UI/SuccessAlert/SuccessAlert.vue";
import Spinner from "@/components/UI/Spinner/Spinner.vue";

import ToolkitService from "@/common/toolkit";

import {ACTION_INIT_DATA} from '@/store/types/actions';
import {GETTER_USER_DATA} from '@/store/types/getters';


export default {
  name: 'FillInfo',
  data() {
    return {
      is_specialist: false,
      data_set: false,
      input_email: '',
      input_last_name: '',
      input_name: '',
      input_patronymic_name: '',
      input_promo: '',
      info_is_filled: false,
      error: {},
    }
  },
  watch: {
    userdata(userdata, old_val) {
      this.input_name = this.userdata?.userinfo?.first_name;
      this.input_last_name = this.userdata?.userinfo?.last_name;
      this.input_patronymic_name = this.userdata?.userinfo?.patronymic_name;
      this.input_email = this.userdata?.userinfo?.email;
      this.is_specialist = this.userdata?.is_specialist;
    }
  },
  computed: {
    ...mapGetters({
      'userdata': GETTER_USER_DATA,
    })
  },
  methods: {
    ...mapActions({
      'initUserAuth': ACTION_INIT_DATA
    }),
    fillUserProfile: function () {
      let email = this.input_email;
      let last_name = this.input_last_name;
      let name = this.input_name;
      let patronymic_name = this.input_patronymic_name;
      let promo = this.input_promo;
      let component = this;

      this.info_is_filled = true;

      UserService
          .fillUserProfile(email, last_name, name, patronymic_name, promo)
          .then(function (response) {
            if (response.data.status === true) {
              component.info_is_filled = false;
              component.data_set = true;
            }
          })
          .catch(function (error) {
            if (error.name === EXCEPTION_NAME_ERROR_FIELDS) {
              ToolkitService.scrollElementByError(error.values);
              component.error = error.values;
            } else if (error.name === EXCEPTION_NAME_EXTERNAL)
              component.error = error.text;

            component.info_is_filled = false;
          });
    },
    closeSuccessAlert() {
      this.data_set = false;
    }
  },
  components: {
    SuccessAlert,
    Spinner
  }
}
</script>

<style scoped lang="scss">
@import 'assets/scss/default.scss';

.page {
  max-width: 435px;
  margin: 150px auto 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
  font-family: $base-font;
  font-style: normal;

  h1 {
    font-weight: 600;
    font-size: 30px;
    line-height: 37px;
    text-align: center;
    color: #283848;
    margin-bottom: 12px;
  }

  .subtitle {
    font-weight: 400;
    font-size: 14px;
    line-height: 17px;
    text-align: center;
    color: #283848;
    margin-bottom: 27px;
  }

  .form {
    display: flex;
    flex-direction: column;
    align-items: flex-start;

    .inputLabel {
      font-weight: 600;
      font-size: 16px;
      line-height: 20px;
      color: #283848;
      margin-bottom: 16px;
      margin-top: 20px;
    }

    input {
      border-radius: 5px;
      padding: 12px 17px;
      width: -webkit-fill-available;
      width: -moz-available;
      font-weight: 400;
      font-size: 14px;
      line-height: 16px;
    }

    .specialist {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }

    .errorText {
      font-weight: 400;
      font-size: 14px;
      line-height: 14px;
      color: #EF3F3F;
      text-align: left;
      margin-top: 5px;
    }

    button {
      width: 100%;
      margin-top: 30px;
    }

    .error {
      margin-top: 15px;

      span {
        font-weight: 400;
        font-size: 16px;
        line-height: 14px;
        color: #EF3F3F;
        text-align: left;
      }
    }
  }
}


@media(max-width: 500px) {
  .page {
    max-width: 300px;

    h1 {
      font-size: 25px;
      line-height: 31px;
    }
  }
}
</style>
