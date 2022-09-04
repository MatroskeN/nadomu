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
        Для смены вашего номера введите e-mail, указанный при регистрации аккаунта
      </div>
      <div class="email-change">
        <label for="email">Ваш email</label>
        <input v-model="input_email"
               v-bind:class="{invalid : !!error.email}"
               @focus="error.email = ''"
               v-on:keyup.enter="restoreNumber"
               type="text"
               id="email"
               placeholder="email@gmail.com">
        <div class="error">
          <span v-if="error.email">{{ error.email }}</span>
        </div>
        <button v-on:click="restoreNumber" class="parent-spinner hover-spinner-is-blue">
          <Spinner :isActive="email_is_load"></Spinner>
          Запросить смену номера
        </button>
      </div>
    </div>
    <SuccessAlert v-if="mail_is_send" @closeModal="closeSuccessAlert" :title="'Ссылка отправлена на почту'"
                  :subtitle="'Проверьте ваш почтовый ящик, скоро вам придет письмо со ссылкой для смены номера'"
                  :linkTitle="'На главную'"
                  :link="'/'"
                  :is-success="true"/>
  </div>
</template>

<script>

import RestoreService from "@/api/RestoreService";
import {EXCEPTION_NAME_EXTERNAL, EXCEPTION_NAME_ERROR_FIELDS} from '@/codes';

import Spinner from "@/components/UI/Spinner/Spinner.vue";
import SuccessAlert from "@/components/UI/SuccessAlert/SuccessAlert.vue";

export default {
  name: 'ChangeNumber',
  components: {SuccessAlert, Spinner},
  data() {
    return {
      input_email: "",
      email_is_load: false,
      mail_is_send: false,
      error: {},
    }
  },
  methods: {
    restoreNumber: function () {
      let input_email = this.input_email;
      let component = this;

      if (input_email) {
        this.email_is_load = true;

        RestoreService
            .restoreNumber(input_email)
            .then(function (response) {
              if (response.data.status === true) {
                component.email_is_load = false;
                component.mail_is_send = true;
              }
            })
            .catch(function (error) {
              if (error.name === EXCEPTION_NAME_ERROR_FIELDS)
                component.error = error.values;
              else if (error.name === EXCEPTION_NAME_EXTERNAL)
                component.error = error.text;

              component.email_is_load = false;
            });
      } else
        this.error.email = 'Введите e-mail!';
    },
    closeSuccessAlert() {
      this.mail_is_send = false;
    }
  }
}
</script>

<style scoped lang="scss">
@import 'assets/scss/default.scss';

.page {
  display: flex;
  flex-direction: column;
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

    .email-change {
      width: 100%;
      display: flex;
      flex-direction: column;

      label {
        font-style: normal;
        font-weight: 600;
        font-size: 16px;
        line-height: 20px;
        color: #283848;
        margin-bottom: 16px;
      }

      input {
        padding: 12px 17px;
        font-weight: 400;
        font-size: 14px;
        line-height: 16px;
      }

      .error {
        margin-right: auto;
      }

      button {
        margin-top: 24px;
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
    }
  }
}

</style>