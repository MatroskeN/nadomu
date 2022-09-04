<template>
  <div v-if="userdata" class="userMenu">
    <div class="controls">
      <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="20" cy="20" r="20" fill="white"/>
        <path
            d="M28 29V27C28 25.9391 27.5786 24.9217 26.8284 24.1716C26.0783 23.4214 25.0609 23 24 23H16C14.9391 23 13.9217 23.4214 13.1716 24.1716C12.4214 24.9217 12 25.9391 12 27V29"
            stroke="#59B4FB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path
            d="M20 19C22.2091 19 24 17.2091 24 15C24 12.7909 22.2091 11 20 11C17.7909 11 16 12.7909 16 15C16 17.2091 17.7909 19 20 19Z"
            stroke="#59B4FB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <p>Личный кабинет</p>
      <button v-on:click="logout()">Выход</button>
    </div>
    <div class="name">
      {{ username }}
    </div>
    <div class="role" v-if="userdata.is_specialist">
      Медицинский специалист
      <br>
      Подписка до хх.хх.хххх
    </div>
    <div class="role" v-if="!userdata.is_specialist">
      Пользователь сервиса
    </div>
    <div class="link">
      Администрация сервиса
    </div>
    <div class="link" v-on:click="redirectButton('/profile/user')">
      Мой профиль
    </div>
  </div>

  <div v-if="!userdata" class="unLoggedMenu">
    <p>Личный кабинет</p>
    <button v-on:click="openAuthWindow()">Войти</button>
  </div>

</template>

<script>

import {MUTATION_SET_AUTH_WINDOW_STATUS} from "@/components/HeaderAuth/store/types/mutations";
import {mapGetters, mapActions} from "vuex";
import {GETTER_USER_DATA, GETTER_USER_NAME} from '@/store/types/getters';
import {ACTION_LOGOUT} from '@/store/types/actions';

import RedirectService from "@/common/redirect";

export default {
  name: 'HeaderAuthMobile',
  computed: {
    ...mapGetters({
      'userdata': GETTER_USER_DATA,
      'username': GETTER_USER_NAME
    }),
  },
  mounted() {

  },
  methods: {
    ...mapActions({
      'logout': ACTION_LOGOUT
    }),
    openAuthWindow() {
      this.$store.commit(MUTATION_SET_AUTH_WINDOW_STATUS, true);
    },
    redirectButton(href) {
      RedirectService
          .link(href)
    }
  }
}
</script>

<style scoped lang="scss">
@import 'assets/scss/default.scss';

button {
  border: 1px solid $white !important;
}

.userMenu {
  font-family: $base-font;

  .controls {
    display: flex;
    align-items: center;

    p {
      font-style: normal;
      font-weight: 500;
      font-size: 28px;
      line-height: 24px;
      color: #FBFBFB;
      margin: 0 22px 0 19px;
    }
  }

  .name {
    font-style: normal;
    font-weight: 600;
    font-size: 25px;
    line-height: 31px;
    color: $white;
    margin-top: 26px;
    margin-bottom: 10px;
  }

  .role {
    font-style: normal;
    font-weight: 500;
    font-size: 14px;
    line-height: 16px;
    color: $white;
    margin-bottom: 26px;
  }

  .link {
    font-style: normal;
    font-weight: 400;
    font-size: 25px;
    line-height: 29px;
    color: $white;
    margin-bottom: 20px;

    &:last-child {
      margin-bottom: unset;
    }
  }
}
.unLoggedMenu{
  font-family: $base-font;
  display: flex;
  justify-content: space-between;
  align-items: center;
  p{
    font-style: normal;
    font-weight: 500;
    font-size: 28px;
    line-height: 24px;
    color: #FBFBFB;
    margin-right: 82px;
  }
  button{
    background: $base-blue !important;
    border: transparent !important;
  }
}

@media(max-width: 600px) {
  .userMenu {
    .controls {
      p {
        font-size: 18px;
      }
    }

    .name {
      font-size: 18px;
      margin-top: 15px;
    }

    .role {
      margin-bottom: 10px;
    }

    .link {
      font-size: 18px;
      margin-bottom: 10px;
    }
  }
  .unLoggedMenu{
    p{
      font-size: 20px;
    }
  }
}
</style>
