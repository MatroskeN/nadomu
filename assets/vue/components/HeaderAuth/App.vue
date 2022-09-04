<template>
  <div class="signIn">
    <div v-if="userdata" class="logged" v-on:mouseover="is_menu_opened = true" v-on:mouseleave="is_menu_opened = false">
      <div class="userIcon">
        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="20" cy="20" r="20" fill="#59B4FB"/>
          <path
              d="M28 29V27C28 25.9391 27.5786 24.9217 26.8284 24.1716C26.0783 23.4214 25.0609 23 24 23H16C14.9391 23 13.9217 23.4214 13.1716 24.1716C12.4214 24.9217 12 25.9391 12 27V29"
              stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path
              d="M20 19C22.2091 19 24 17.2091 24 15C24 12.7909 22.2091 11 20 11C17.7909 11 16 12.7909 16 15C16 17.2091 17.7909 19 20 19Z"
              stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <svg class="arrow" v-bind:class="{activeArrow : is_menu_opened}" width="11" height="7" viewBox="0 0 11 7" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1 1L5.5 6L10 1" stroke="#283848" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="userMenu" v-if="is_menu_opened">
        <div class="item title">
          {{ username }}
        </div>
        <hr>
        <div class="item role">
          <p v-if="userdata.is_specialist">
            Медицинский специалист
          </p>
          <p v-if="!userdata.is_specialist">
            Пользователь сервиса
          </p>
        </div>
        <hr>
        <div class="item link" v-on:click="redirectButton('/profile/requests')">
          Мои обращения
        </div>
        <div class="item link">
          Избранное
        </div>
        <div class="item link" v-on:click="redirectButton('/profile/user')">
          Профиль
        </div>
        <div class="item link">
          Связаться с поддержкой
        </div>
        <hr>
        <div class="item logout" v-on:click="logout()">
          Выйти
        </div>
      </div>
    </div>
    <div v-if="!userdata">
      <button class="enter" v-on:click="openAuthorizationModal">
        Войти
      </button>

      <Modal :isOpen="isModalPhoneOpen" @closeModal="closeAuthorizationModal">
        <template v-if="!is_code_window" v-slot:body>
          <div class="title">
            Добро пожаловать
          </div>
          <div class="subtitle">
            Введите номер телефона, чтобы войти <br> или зарегистроваться
          </div>
          <div class="form">
            <div class="label">Ваш телефон</div>
            <div class="access">
              <button v-on:click="redirectButton('/restore')">Нет доступа к телефону</button>
            </div>
          </div>
          <vue-mask class="form-control"
                    mask="+7 (000) 000-00-00"
                    v-model="input_phone"
                    v-bind:class="{invalid : !!error.phone}"
                    @focus="error.phone = ''"
                    v-on:keyup.enter = "sendSms"
                    type="text"
                    :raw="false"
                    :options="phoneOptions">
          </vue-mask>

          <div class="error">
            <span v-if="error.phone">{{ error.phone }}</span>
          </div>

          <button v-on:click="sendSms" class="parent-spinner hover-spinner-is-blue">
            <Spinner :isActive="sms_is_load"></Spinner>
            Получить код по SMS
          </button>
          <div class="privacy">
            Нажимая на кнопку “Получить код”, я соглашаюсь с условиями <a href="#">пользовательского соглашения</a>
          </div>
        </template>
        <template v-if="is_code_window" v-slot:second_body>
          <div class="title">
            Добро пожаловать
          </div>
          <div class="subtitle">
            Введите номер телефона, чтобы войти <br> или зарегистроваться
          </div>
          <div class="form form-inactive">
            <div class="label">
              Номер телефона
            </div>
            <div class="access">
              <button v-on:click="reEnterNumber">
                Ввести другой номер
              </button>
            </div>
          </div>
          <div class="number">
            {{ input_phone }}
          </div>
          <div class="form">
            <div class="label">Код в сообщении</div>
            <div class="access">
              <button v-if="limit_time_dial > 0 && dial_is_available" class="inactive">Позвонить через
                {{ limit_time_dial }} сек
              </button>
              <button v-if="limit_time_dial <= 0 && dial_is_available" v-on:click="dialCode">Получить код по звонку
              </button>
            </div>
          </div>
          <vue-mask class="form-control"
                    mask="0000"
                    v-model="input_code"
                    v-bind:class="{invalid : !!error.code_id }"
                    @focus="error.code_id = ''"
                    v-on:keyup.enter = "checkCode"
                    type="text"
                    :raw="false"
                    :options="codeOptions">
          </vue-mask>
          <div class="error">
            <span v-if="error.code_id">{{ error.code_id }}</span>
          </div>
          <button v-on:click="checkCode" class="parent-spinner hover-spinner-is-blue">
            <Spinner :isActive="code_is_load"></Spinner>
            Ввести код
          </button>
          <div class="privacy">
            Нажимая на кнопку “Получить код”, я соглашаюсь с условиями <a href="#">пользовательского соглашения</a>
          </div>
        </template>
      </Modal>

      <MultiPopup :title="modal_title" :text="modal_text" :isOpen="modal_is_open"/>

    </div>
  </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex";
import {GETTER_IS_AUTH_WINDOW_OPEN} from "./store/types/getters";
import {MUTATION_SET_AUTH_WINDOW_STATUS} from "./store/types/mutations";
import {GETTER_USER_DATA, GETTER_USER_NAME} from '@/store/types/getters';
import {ACTION_LOGOUT, ACTION_INIT_DATA} from '@/store/types/actions';


import RedirectService from "@/common/redirect";
import SmsService from "@/api/SmsService";
import CookieService from "@/common/cookie";
import {EXCEPTION_NAME_EXTERNAL, EXCEPTION_NAME_ERROR_FIELDS} from '@/codes';
import vueMask from 'vue-jquery-mask';

import MultiPopup from "@/components/UI/MultiPopup/MultiPopup.vue";
import Modal from "@/components/UI/Modal/Modal.vue";
import Spinner from "@/components/UI/Spinner/Spinner.vue";

const DELAY_DIAL_TIME = 60;

export default {
  name: 'HeaderAuth',
  data() {
    return {
      is_menu_opened: false,
      is_code_window: false,
      input_phone: "",
      input_code: "",
      code: "",
      code_id: "",
      auth_token: "",
      modal_title: '',
      modal_text: '',
      modal_is_open: false,
      sms_is_load: false,
      code_is_load: false,
      dial_is_available: true,
      limit_time_dial: null,
      dialCountdown: null,
      phoneOptions: {
        placeholder: '+7 (___) ___-__-__',
        clearIfNotMatch: true
      },
      codeOptions: {
        placeholder: '0000',
        clearIfNotMatch: true
      },
      error: {}
    }
  },
  computed: {
    ...mapGetters({
      'isModalPhoneOpen': GETTER_IS_AUTH_WINDOW_OPEN,
      'userdata': GETTER_USER_DATA,
      'username': GETTER_USER_NAME
    })
  },
  mounted() {
    this.initUserAuth();
  },
  methods: {
    ...mapActions({
      'logout': ACTION_LOGOUT,
      'initUserAuth': ACTION_INIT_DATA
    }),

    //api methods
    sendSms() {
      let input_phone = this.input_phone;
      let component = this;

      if (input_phone) {
        this.sms_is_load = true;

        SmsService
            .sendCode(input_phone, 'user')
            .then(function (response) {
              component.sms_is_load = false;

              if (response.data.status === true) {
                component.limit_time_dial = DELAY_DIAL_TIME;
                component.is_code_window = true;
                component.dial_is_available = true;
                component.code_id = response.data.id;

                // component.closePhoneModal();
                component.clearDialCountdown();
                component.dialCountdown = setInterval(component.callbackDialCountdown, 1000)

                if (typeof response.data.code != 'undefined') {
                  component.showModal('Код авторизации', response.data.code);
                }
              }
            })
            .catch(function (error) {
              if (error.name === EXCEPTION_NAME_ERROR_FIELDS)
                component.error = error.values;
              else if (error.name === EXCEPTION_NAME_EXTERNAL)
                component.error = error.text;

              component.sms_is_load = false;
            });
      } else
        this.error.phone = 'Введите номер телефона!';
    },
    checkCode: function () {
      let input_code = this.input_code;
      let component = this;

      if (input_code) {
        this.code_is_load = true;

        SmsService
            .checkCode(component.code_id, component.input_code)
            .then(function (response) {
              if (response.data.status === true) {
                CookieService.set('token', response.data.auth_token);

                component.initUserAuth();
                component.closeAuthorizationModal();

                if (response.data.is_empty_data === true) {
                  component.redirectButton('/profile/init')
                }
              }
              component.code_is_load = false;
            })
            .catch(function (error) {
              if (error.name === EXCEPTION_NAME_ERROR_FIELDS)
                component.error = error.values;
              else if (error.name === EXCEPTION_NAME_EXTERNAL)
                component.error = error.text;

              //too many requests
              if (error.code === 429)
                component.dial_is_available = false;

              component.code_is_load = false;
            });
      } else
        this.error.code_id = 'Введите код авторизации!';
    },
    dialCode: function () {
      let component = this;

      if (this.code_id) {
        SmsService
            .dialCode(component.code_id)
            .then(function (response) {
              component.dial_is_available = false;
            }).catch(function (error) {
          component.dial_is_available = false;
        });
      }
    },

    // methods for close/open modals
    openAuthorizationModal: function (event) {
      this.$store.commit(MUTATION_SET_AUTH_WINDOW_STATUS, true);
    },
    closeAuthorizationModal: function (event) {
      this.is_code_window = false;
      this.modal_is_open = false;
      this.dial_is_available = true;
      this.input_phone = "";
      this.input_code = "";
      this.error = {};

      this.closePhoneModal();
    },
    closePhoneModal: function (event) {
      this.$store.commit(MUTATION_SET_AUTH_WINDOW_STATUS, false);
    },

    //methods for handling countdown to dial
    clearDialCountdown: function () {
      if (this.dialCountdown !== null) {
        clearInterval(this.dialCountdown);
      }
    },
    callbackDialCountdown: function () {
      this.limit_time_dial -= 1;

      if (this.limit_time_dial <= 0) {
        this.clearDialCountdown();
      }
    },
    showModal: function (title, text) {
      this.modal_title = title;
      this.modal_text = text;
      this.modal_is_open = true;
    },

    //redirect method
    redirectButton (href) {
      RedirectService
        .link(href)
    },

    //re-enter number method
    reEnterNumber(){
      this.is_code_window = !this.is_code_window;
      this.input_phone = '';
      this.input_code = '';
    },
  },
  components: {
    MultiPopup,
    vueMask,
    Modal,
    Spinner
  }
}
</script>

<style scoped lang="scss">
@import 'assets/scss/default.scss';

.logged {
  position: relative;

  .userIcon {
    display: flex;
    margin-left: 60px;
    align-items: center;
    cursor: pointer;

    .arrow {
      margin-left: 11px;
      transition: all 0.4s;
    }

    .activeArrow {
      transform: rotateZ(180deg);
    }
  }

  .userMenu {
    background: $white;
    box-shadow: 0px 5px 35px rgba(21, 30, 41, 0.1);
    border-radius: 10px;
    position: absolute;
    margin-top: 2px;
    right: 0;
    z-index: 100;
    padding: 16px 0;
    width: 259px;
    display: flex;
    flex-direction: column;

    .item {
      font-family: $base-font;
      margin-left: 35px;
    }

    .title {
      font-style: normal;
      font-weight: 600;
      font-size: 18px;
      line-height: 22px;
      color: #283848;
      padding-bottom: 15px;
    }

    hr {
      background: #F0F1F8;
      height: 2px;
      width: 100%;
      margin: 0 0 16px 0;
      border: none;
    }

    .role {
      margin-bottom: 15px;

      p {
        font-style: normal;
        font-weight: 600;
        font-size: 16px;
        line-height: 20px;
        color: #87B1CA;
        transition: all 0.4s;
      }
    }

    .link {
      cursor: pointer;
      margin-bottom: 10px;
      transition: all 0.4s;
      &:hover{
        color: #87B1CA;
      }
    }

    .logout {
      cursor: pointer;
      transition: all 0.4s;
      &:hover{
        color: #87B1CA;
      }
    }
  }
}

.enter {
  margin-left: 23px;
  background: linear-gradient(84.59deg, $base-blue 2.25%, #59B4FB 97.14%);
  font-family: $base-font;
  font-style: normal;
  font-weight: 500;
  font-size: 14px;
  line-height: 20px;
  color: $white;

  &:hover {
    background: linear-gradient(84.59deg, #59B4FB 2.25%, $base-blue 97.14%);
    color: $white;
  }
}

.modal {
  .modalWindow {
    .title {
      font-family: $base-font;
      font-style: normal;
      font-weight: 600;
      font-size: 30px;
      line-height: 37px;
      text-align: center;
      color: #283848;
      margin-bottom: 12px;
    }

    .subtitle {
      font-family: $base-font;
      font-style: normal;
      font-weight: 400;
      font-size: 14px;
      line-height: 16px;
      text-align: center;
      color: #283848;
      margin-bottom: 29px;
    }

    .form {
      display: flex;
      justify-content: space-between;
      width: 100%;

      .label {
        font-family: $base-font;
        font-style: normal;
        font-weight: 600;
        font-size: 16px;
        line-height: 20px;
        color: #283848;
      }

      .access {
        .inactive {
          color: #87B1CA;
          cursor: auto;
        }

        button {
          font-weight: 400;
          text-decoration-line: underline;
          color: $base-blue;
          background: transparent;
          padding: 0;
          border: none;
          margin: 0;
        }
      }
    }

    .number {
      font-family: $base-font;
      font-style: normal;
      font-weight: 400;
      font-size: 14px;
      line-height: 16px;
      color: #283848;
      margin: 17px auto 43px 0;
    }

    input {
      padding: 12px 17px;
      width: 435px;
      margin-top: 16px;
    }

    button {
      margin-top: 20px;
      width: 100%;
    }

    .error {
      margin-right: auto;
    }

    .privacy {
      font-family: $base-font;
      font-style: normal;
      font-weight: 400;
      font-size: 14px;
      line-height: 20px;
      max-width: 337px;
      text-align: center;
      color: #283848;
      margin-top: 45px;

      a {
        color: #283848;
        text-decoration: underline;
      }
    }
  }
}

.active {
  display: flex;
}

@media(max-width: 1250px) {
  .logged {
    display: none;
  }
}

@media(max-width: 600px) {
  .modal {
    .modalWindow {
      input {
        width: 261px;
      }
    }
  }
}
</style>
