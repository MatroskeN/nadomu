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
            Мой профиль
          </a>
        </li>
      </ul>
    </nav>
    <h1>Мой профиль</h1>
    <div class="wrapper">
      <div class="title">
        Персональная информация
      </div>
      <div class="subtitle">
        Информация вашего профиля
      </div>
      <div class="item">
        <div class="itemName">Имя*</div>
        <div class="box">
          <input
              class="itemValue"
              type="text"
              id="first_name"
              v-model="input_name"
              v-bind:class="{invalid: error.first_name}"
              @focus="error.first_name = ''"
              v-on:keyup.enter="updateUserProfile"
              placeholder="Василий">
          <div class="errorText" v-if="error.first_name">
            {{ error.first_name }}
          </div>
        </div>
      </div>
      <div class="item">
        <div class="itemName">Фамилия*</div>
        <div class="box">
          <input
              class="itemValue"
              type="text"
              id="last_name"
              v-model="input_last_name"
              v-bind:class="{invalid: error.last_name}"
              @focus="error.last_name = ''"
              v-on:keyup.enter="updateUserProfile"
              placeholder="Петров">
          <div class="errorText" v-if="error.last_name">
            {{ error.last_name }}
          </div>
        </div>
      </div>
      <div class="item">
        <div class="itemName">Отчество*</div>
        <div class="box">
          <input
              class="itemValue"
              type="text"
              id="patronymic_name"
              v-model="input_patronymic_name"
              v-bind:class="{invalid: error.patronymic_name}"
              @focus="error.patronymic_name = ''"
              v-on:keyup.enter="updateUserProfile"
              placeholder="Иванович">
          <div class="errorText" v-if="error.patronymic_name">
            {{ error.patronymic_name }}
          </div>
        </div>
      </div>
      <div class="item">
        <div class="itemName">Телефон*</div>
        <div class="box">
          <vue-mask class="form-control itemValue"
                    mask="+7 (000) 000-00-00"
                    id="phone"
                    v-model="input_phone"
                    v-bind:class="{invalid: error.phone}"
                    @focus="error.phone = ''"
                    v-on:keyup.enter="updateUserProfile"
                    type="text"
                    :raw="false"
                    :options="phoneOptions">
          </vue-mask>
          <div class="errorText" v-if="error.phone">
            {{ error.phone }}
          </div>
        </div>
      </div>
      <div class="item">
        <div class="itemName">Резервный email*</div>
        <div class="box">
          <input
              class="itemValue"
              type="email"
              id="email"
              v-model="input_email"
              v-bind:class="{invalid: error.email}"
              @focus="error.email = ''"
              v-on:keyup.enter="updateUserProfile"
              placeholder="vasiliy.petrov@gmail.com">
          <div class="errorText" v-if="error.email">
            {{ error.email }}
          </div>
        </div>
      </div>
      <div class="item">
        <div class="itemName">День рождения*</div>
        <div class="box">
          <Datepicker
              v-model="input_date"
              locale="ru"
              id="birthday"
              format="yyyy-MM-dd"
              @focus="error.birthday = ''"
              v-on:keyup.enter="updateUserProfile"
              :textInput="false"
              :enableTimePicker="false"
              selectText="Выбрать"
              cancelText="Отмена">
          </Datepicker>
          <div class="errorText" v-if="error.birthday">
            {{ error.birthday }}
          </div>
        </div>
      </div>
      <div class="item">
        <div class="itemName">
          <p>Фото профиля</p>
          <div class="load">
            <label @click="$refs.avatarComponent.openUpload()">
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
            <div class="temporaryPic" v-if="avatar">
              <div class="delete" v-on:click="removeTemporary">
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
                      fill="currentColor"
                  />
                  <path d="M9 9H11V17H9V9Z" fill="currentColor"/>
                  <path d="M13 9H15V17H13V9Z" fill="currentColor"/>
                </svg>
              </div>
              <img :src="'/'+avatar">
            </div>

            <AvatarCrop @setImageData="getAvatarData" @deleteImage="deleteImage" @removeTemporary="removeTemporary"
                        ref="avatarComponent"></AvatarCrop>
          </div>
        </div>
      </div>
      <div class="title">
        Уведомления
      </div>
      <div class="subtitle">
        Информация об уведомлениях и новых событиях
      </div>
      <div class="item notification">
        <div class="itemName">По email</div>
        <div class="box">
          <div class="boxItem" v-if="!is_specialist">
            <div class="checkbox">
              <input
                  type="checkbox"
                  v-model="email_expert_answers"
                  v-on:keyup.enter="updateUserProfile"
                  id="email_expert_answers"
              >
              <label for="email_expert_answers">Ответы от специалистов</label>
            </div>
            <p>
              Уведомлять об ответах медицинских специалистов по моим запросам
            </p>
          </div>
          <div class="boxItem" v-if="is_specialist">
            <div class="checkbox">
              <input
                  type="checkbox"
                  v-model="email_new_requests"
                  v-on:keyup.enter="updateUserProfile"
                  id="email_new_requests">
              <label for="email_new_requests">Новые заявки</label>
            </div>
            <p>
              Уведомлять о новых заявках по почте
            </p>
          </div>
          <div class="boxItem" v-if="is_specialist">
            <div class="checkbox">
              <input
                  type="checkbox"
                  v-model="email_users_response"
                  v-on:keyup.enter="updateUserProfile"
                  id="email_users_response">
              <label for="email_users_response">Ответы от пользователей</label>
            </div>
            <p>
              Уведомлять об ответах пользователей в запросах
            </p>
          </div>
        </div>
      </div>
      <div class="item notification">
        <div class="itemName">По SMS</div>
        <div class="box">
          <div class="boxItem" v-if="!is_specialist">
            <div class="checkbox">
              <input
                  type="checkbox"
                  v-model="sms_expert_answers"
                  v-on:keyup.enter="updateUserProfile"
                  id="sms_expert_answers"
              >
              <label for="sms_expert_answers">Ответы от специалистов</label>
            </div>
            <p>
              Уведомлять об ответах медицинских специалистов по моим запросам
            </p>
          </div>
          <div class="boxItem" v-if="is_specialist">
            <div class="checkbox">
              <input
                  type="checkbox"
                  v-model="sms_users_response"
                  v-on:keyup.enter="updateUserProfile"
                  id="users_response">
              <label for="users_response">Ответы от пользователей</label>
            </div>
            <p>
              Уведомлять об ответах пользователей в запросах
            </p>
          </div>
          <div class="boxItem" v-if="is_specialist">
            <div class="checkbox">
              <input
                  type="checkbox"
                  v-model="sms_new_requests"
                  v-on:keyup.enter="updateUserProfile"
                  id="sms_new_requests">
              <label for="sms_new_requests">Новые заявки</label>
            </div>
            <p>
              Уведомлять о новых заявках по SMS
            </p>
          </div>
        </div>
      </div>

      <button v-on:click="updateUserProfile" class="parent-spinner hover-spinner-is-blue">
        <Spinner :isActive="info_is_updated"></Spinner>
        Сохранить
      </button>
    </div>
    <SuccessAlert v-if="profile_is_updated" @closeModal="closeSuccessAlert" :title="'Данные сохранены'"
                  :subtitle="'Обновленная информация будет отображаться в вашем профиле'" :linkTitle="'Вернуться'" :link="this_page"  :is-success="true"/>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

import vueMask from 'vue-jquery-mask';
import Spinner from "@/components/UI/Spinner/Spinner.vue";
import SuccessAlert from "@/components/UI/SuccessAlert/SuccessAlert.vue";
import AvatarCrop from "@/components/UI/AvatarCrop/AvatarCrop.vue";
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

import UserService from "@/api/UserService";
import FileService from "@/api/FileService";
import ToolkitService from "@/common/toolkit";
import {EXCEPTION_NAME_EXTERNAL, EXCEPTION_NAME_ERROR_FIELDS} from '@/codes';
import {ACTION_INIT_DATA} from '@/store/types/actions';
import {GETTER_USER_DATA} from '@/store/types/getters';


export default {
  name: 'UserProfile',
  data() {
    return {
      this_page: window.location.href,
      is_specialist: false,
      profile_is_updated: false,
      input_name: '',
      input_last_name: '',
      input_patronymic_name: '',
      input_email: '',
      input_date: '',
      input_phone: '',
      avatar_id: '',
      avatar: '',
      email_expert_answers: false,
      email_new_requests: false,
      email_users_response: false,
      sms_expert_answers: false,
      sms_new_requests: false,
      sms_users_response: false,
      info_is_updated: false,
      avatar_image_string: '',
      avatar_filename: '',
      date_changed: false,
      dateOptions: {
        placeholder: '1990-01-01',
        clearIfNotMatch: true
      },
      phoneOptions: {
        placeholder: '+7 (900) 000-00-00',
        clearIfNotMatch: true
      },
      error: {}
    }
  },
  mounted() {
    this.initUserAuth();

  },
  watch: {
    userdata(userdata, old_val) {
      this.input_name = this.userdata?.userinfo?.first_name;
      this.input_last_name = this.userdata?.userinfo?.last_name;
      this.input_patronymic_name = this.userdata?.userinfo?.patronymic_name;
      this.input_email = this.userdata?.userinfo?.email;
      this.input_date = this.userdata?.userinfo?.birthday;
      this.input_phone = this.userdata?.userinfo?.phone;
      this.avatar_id = this.userdata?.images?.profile?.id;
      this.email_expert_answers = this.userdata?.notifications?.email_expert_answers;
      this.email_new_requests = this.userdata?.notifications?.email_new_requests;
      this.email_users_response = this.userdata?.notifications?.email_users_response;
      this.sms_expert_answers = this.userdata?.notifications?.sms_expert_answers;
      this.sms_new_requests = this.userdata?.notifications?.sms_new_requests;
      this.sms_users_response = this.userdata?.notifications?.sms_users_response;
      this.avatar = this.userdata?.images?.profile?.file_path;
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
    getAvatarData(filename, data) {
      this.avatar_image_string = data;
      this.avatar_filename = filename;
    },
    deleteImage() {
      this.avatar_image_string = '';
      this.avatar_id = '';
    },
    removeTemporary() {
      if (this.$el.querySelector('.temporaryPic')) {
        this.$el.querySelector('.temporaryPic').style.display = 'none';
        this.avatar_image_string = '';
        this.avatar_id = '';
      }
    },
    closeSuccessAlert() {
      this.profile_is_updated = false;
    },

    updateUserProfile: async function () {
      let component = this;

      this.info_is_updated = true;


      if (this.$refs.avatarComponent.isUploaded === true) {
        component.$refs.avatarComponent.getImageValue();

        await FileService
            .uploadFile(this.avatar_filename, 'photo_profile', this.avatar_image_string)
            .then(function (response) {
              if (response.data.status === true) {
                component.avatar_id = response.data.id.toString();
              }
            })
            .catch(function (error) {
              if (error.name === EXCEPTION_NAME_ERROR_FIELDS)
                component.error = error.values;
              else if (error.name === EXCEPTION_NAME_EXTERNAL)
                component.error = error.text;
            })
      }

      let date_birthday = '';
      if (this.input_date && typeof (this.input_date) === "object") {
        date_birthday = this.input_date.getFullYear() + "-" + (this.input_date.getMonth() + 1) + "-" + this.input_date.getDate()
      } else {
        date_birthday = this.input_date;
      }

      let update_profile = {
        first_name: this.input_name,
        last_name: this.input_last_name,
        patronymic_name: this.input_patronymic_name,
        phone: this.input_phone,
        email: this.input_email,
        birthday: date_birthday,
        avatar_id: this.avatar_id,
        email_expert_answers: this.email_expert_answers,
        email_new_requests: this.email_new_requests,
        email_users_response: this.email_users_response,
        sms_expert_answers: this.sms_expert_answers,
        sms_new_requests: this.sms_new_requests,
        sms_users_response: this.sms_users_response
      };
      UserService
          .updateUserProfile(update_profile)
          .then(function (response) {
            if (response.data.status === true) {
              component.info_is_updated = false;
              component.profile_is_updated = true;
            }
          })
          .catch(function (error) {
            if (error.name === EXCEPTION_NAME_ERROR_FIELDS) {
              ToolkitService.scrollElementByError(error.values);
              component.error = error.values;
            } else if (error.name === EXCEPTION_NAME_EXTERNAL)
              component.error = error.text;

            component.info_is_updated = false;
          });
    }
  },
  components: {
    SuccessAlert,
    Spinner,
    vueMask,
    AvatarCrop,
    Datepicker
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

  .breadcrumb {
    margin-bottom: 70px;
  }

  h1 {
    font-family: $base-font;
    font-style: normal;
    font-weight: 500;
    font-size: 35px;
    line-height: 50px;
    color: $base-blue;
    margin: 0 0 35px 0;
  }

  .wrapper {
    font-family: $base-font;
    font-style: normal;
    background: $white;
    box-shadow: 0px 5px 35px rgba(21, 30, 41, 0.1);
    border-radius: 15px;
    padding: 60px 80px;

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
      margin-bottom: 34px;
    }

    .item {
      display: flex;
      align-items: center;
      padding: 0 0 33px 0;
      margin-bottom: 33px;
      border-bottom: 2px solid #F0F1F8;

      .itemName {
        font-weight: 600;
        font-size: 18px;
        line-height: 22px;
        color: #283848;
        width: 150px;
        margin-right: 126px;

        .load {
          position: relative;
          cursor: pointer;
          display: flex;
          align-items: flex-start;

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

          input {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 0;
            opacity: 0;
          }

          .temporaryPic {
            display: flex;
            position: relative;

            .delete {
              position: absolute;
              z-index: 1;
              padding: 5px 5px 0 5px;
              background: $white;
              border-radius: 5px;
              opacity: 0.7;
              top: 10px;
              right: 10px;;
            }

            img {
              position: relative;
              z-index: 0;
              width: 196px;
              margin-left: 130px;
            }
          }
        }
      }

      input {
        font-family: $base-font;
        padding: 12px 17px;
        width: 217px;
      }
    }

    .notification {
      align-items: flex-start;
    }

    .box {
      display: flex;
      flex-direction: column;

      .boxItem {
        display: flex;
        flex-direction: column;
        border-bottom: 2px solid #F0F1F8;
        margin-bottom: 15px;
        padding-bottom: 15px;

        .checkbox {
          display: flex;
          align-items: center;

          input {
            width: 22px;
            height: 22px;
          }

          label {
            font-weight: 600;
            font-size: 16px;
            line-height: 20px;
            color: #283848;
            margin-left: 10px;
          }
        }

        p {
          font-weight: 400;
          font-size: 16px;
          line-height: 19px;
          color: #283848;
          margin: 15px 0 0 0;
        }
      }
    }

    button {
      border-radius: 45px;
      padding: 15px 51px;
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

@media(max-width: 1250px) {
  .page {
    max-width: 100%;
    padding: 0 44px;
  }
}

@media(max-width: 767px) {
  .page {
    padding: 0 24px;

    .wrapper {
      padding: 35px 24px;

      .item {
        justify-content: space-between;

        .itemName {
          margin: 0;
          font-size: 16px;
          line-height: 22px;

          .load {
            flex-direction: column;

            .temporaryPic {
              .delete {
                top: 35px;
              }

              img {
                margin-left: unset;
                margin-top: 25px;
              }
            }
          }
        }
      }

      .notification {
        flex-direction: column;

        .itemName {
          margin-bottom: 20px;
        }
      }
    }
  }
}

@media(max-width: 500px) {
  .page {
    .wrapper {
      .item {
        flex-flow: row wrap;

        input {
          width: 180px;
        }

        .itemName {
          margin-bottom: 15px;
        }
      }
    }
  }
}
</style>
