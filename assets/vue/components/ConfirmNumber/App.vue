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
          <a href="">
            Смена номера телефона
          </a>
        </li>
      </ul>
    </nav>
    <div class="content">
      <h1>Смена номера телефона </h1>
      <div class="subtitle">
        Введите новый номер телефона на который мы отправим код подтверждения
      </div>
      <div class="send-code" v-if="!is_code_sent">
        <div class="forms">
          <div class="form">
            <label>Новый номер телефона</label>
            <vue-mask class="form-control"
                      mask="+7 (000) 000-00-00"
                      v-model="input_phone"
                      v-bind:class="{invalid : !!error.phone}"
                      @focus="error.phone = '', error.phone_repeat = ''"
                      v-on:keyup.enter="sendSms"
                      type="text"
                      :raw="false"
                      :options="phoneOptions">
            </vue-mask>
          </div>
          <div class="form">
            <label>Повторите номер телефона</label>
            <vue-mask class="form-control"
                      mask="+7 (000) 000-00-00"
                      v-model="input_phone_duplicate"
                      v-bind:class="{invalid : !!error.phone}"
                      @focus="error.phone = '', error.phone_repeat = ''"
                      v-on:keyup.enter="sendSms"
                      type="text"
                      :raw="false"
                      :options="phoneOptions">
            </vue-mask>
          </div>
        </div>
        <div class="error">
          <span v-if="error.phone">{{ error.phone }}</span>
          <span v-if="error.phone_repeat">{{ error.phone_repeat }}</span>
        </div>
        <button v-on:click="sendSms" class="parent-spinner hover-spinner-is-blue">
          <Spinner :isActive="sms_is_load"></Spinner>
          Отправить SMS для подтверждения
        </button>
      </div>
      <div class="confirm-code" v-if="is_code_sent">
        <div class="form-header">
          <label>Введите код авторизации</label>
          <button v-if="limit_time_dial > 0">Позвонить через {{ limit_time_dial }} сек</button>
          <button v-if="limit_time_dial <= 0" v-on:click="dialCode">Сообщить код звонком</button>
        </div>
        <vue-mask class="form-control"
                  mask="0000"
                  v-model="input_code"
                  v-bind:class="{invalid : !!error.code_id}"
                  @focus="error.code_id = ''"
                  type="text"
                  :raw="false"
                  :options="codeOptions">
        </vue-mask>
        <div class="error">
          <span v-if="error.code_id">{{ error.code_id }}</span>
        </div>
        <button v-on:click="checkCode" class="parent-spinner hover-spinner-is-blue">
          <Spinner :isActive="sms_is_load"></Spinner>
          Сменить номер
        </button>
      </div>
    </div>
    <SuccessAlert v-if="number_is_changed" @closeModal="closeSuccessAlert" :title="'Номер успешно обновлен'"
                  :subtitle="'Теперь можно авторизоваться по новому номеру'"
                  :linkTitle="'На главную'"
                  :link="'/'"
                  :is-success="true"/>
  </div>
  <MultiPopup :title="modal_title" :text="modal_text" :isOpen="modal_is_open"/>
</template>

<script>
import RestoreService from "@/api/RestoreService";
import SmsService from "@/api/SmsService";
import vueMask from 'vue-jquery-mask';
import {EXCEPTION_NAME_EXTERNAL, EXCEPTION_NAME_ERROR_FIELDS} from '@/codes';

import MultiPopup from "@/components/UI/MultiPopup/MultiPopup.vue";
import Spinner from "@/components/UI/Spinner/Spinner.vue";
import SuccessAlert from "@/components/UI/SuccessAlert/SuccessAlert.vue";

const DELAY_DIAL_TIME = 60;

export default {
  name: 'ConfirmNumber',
  data() {
    return {
      is_code_sent: false,
      input_phone: '',
      input_code: '',
      input_phone_duplicate: '',
      sms_is_load: false,
      modal_title: '',
      modal_text: '',
      modal_is_open: false,
      number_is_changed: false,
      code_id: null,
      confirmation_code: window.confirmation_code,
      user_id: window.confirmation_user_id,
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
  methods: {
    sendSms() {
      let input_phone = this.input_phone;
      let input_phone_duplicate = this.input_phone_duplicate;
      let user_id = this.user_id;
      let code = this.confirmation_code;
      let component = this;

      if (input_phone) {
        if (input_phone === input_phone_duplicate) {
          this.sms_is_load = true;
          RestoreService
              .restoreByPhone(input_phone, input_phone_duplicate, user_id, code)
              .then(function (response) {
                component.sms_is_load = false;

                if (response.data.status === true) {
                  component.code_id = response.data.id;
                  component.limit_time_dial = DELAY_DIAL_TIME;
                  component.clearDialCountdown();
                  component.dialCountdown = setInterval(component.callbackDialCountdown, 1000)

                  if (typeof response.data.code != 'undefined') {
                    component.showModal('Код авторизации', response.data.code);
                    component.is_code_sent = true;
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
          this.error.phone = 'Номера не совпадают!'
      } else
        this.error.phone = 'Введите номер телефона!';
    },
    checkCode() {
      let input_code = this.input_code;
      let user_id = this.user_id;
      let auth_id = this.code_id;
      let restore_code = this.confirmation_code;
      let component = this;

      this.sms_is_load = true;
      RestoreService
          .restoreCheckCode(user_id, auth_id, input_code, restore_code)
          .then(function (response) {
            component.sms_is_load = false;
            component.number_is_changed = true;
          })
          .catch(function (error) {
            if (error.name === EXCEPTION_NAME_ERROR_FIELDS)
              component.error = error.values;
            else if (error.name === EXCEPTION_NAME_EXTERNAL)
              component.error = error.text;

            component.sms_is_load = false;
          });
    },
    dialCode: function () {
      let component = this;

      if (this.code_id) {
        SmsService
            .dialCode(component.code_id)
      }
    },
    showModal: function (title, text) {
      this.modal_title = title;
      this.modal_text = text;
      this.modal_is_open = true;
    },
    closeSuccessAlert() {
      this.number_is_changed = false;
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
  },
  mounted() {
  },
  components: {
    SuccessAlert,
    MultiPopup,
    vueMask,
    Spinner
  }
}
</script>

<style scoped lang="scss">
@import 'assets/scss/default.scss';

.page {
  display: flex;
  flex-direction: column;
  position: relative;
  max-width: 1235px;
  margin: 0 auto;

  .content {
    font-family: $base-font;
    max-width: 475px;
    width: 100%;
    margin: 100px auto 0;
    display: flex;
    flex-direction: column;
    align-items: center;

    h1 {
      font-style: normal;
      font-weight: 600;
      font-size: 30px;
      line-height: 37px;
      text-align: center;
      color: #283848;
      margin-bottom: 12px;
    }

    .subtitle {
      font-style: normal;
      font-weight: 400;
      font-size: 14px;
      line-height: 16px;
      text-align: center;
      color: #283848;
      max-width: 300px;
      margin-bottom: 29px;
    }

    .send-code {
      display: flex;
      flex-direction: column;

      .forms {
        display: flex;

        .form {
          max-width: 220px;

          &:first-child {
            margin-right: 35px;
          }

          label {
            font-style: normal;
            font-weight: 600;
            font-size: 16px;
            line-height: 20px;
            color: #283848;
          }

          input {
            width: -webkit-fill-available;
            padding: 9px 17px;
            margin-top: 16px;
            font-weight: 400;
            font-size: 14px;
            line-height: 16px;
          }
        }
      }

      .error {
        margin: 15px auto 15px;
      }
    }

    .confirm-code {
      display: flex;
      flex-direction: column;
      max-width: 435px;
      width: 100%;

      .form-header {
        display: flex;
        justify-content: space-between;

        label {
          font-style: normal;
          font-weight: 600;
          font-size: 16px;
          line-height: 20px;
          color: #283848;
        }

        button {
          background: none;
          padding: unset;
          font-weight: 400;
          text-decoration-line: underline;
          color: $base-blue;
          margin: 0;
          border: none;
        }
      }

      input {
        margin-top: 16px;
        padding: 9px 17px;
        font-weight: 600;
        font-size: 20px;
        line-height: 25px;
        letter-spacing: 0.1em;
      }

      button {
        margin-top: 24px;
      }

      .error {
        margin: 15px auto 0;
      }
    }
  }
}

@media(max-width: 500px) {
  .page {
    nav {
      padding: 0;
    }

    .content {
      max-width: 100%;
      width: auto;
      padding: 0 24px;

      .send-code {
        .forms {
          flex-flow: row wrap;
          justify-content: center;

          .form {
            max-width: 156px;

            &:first-child {
              margin-right: 15px;
            }

            input {
              padding: 12px 9px 12px 17px;
            }
          }
        }
      }

    }
  }
}

</style>