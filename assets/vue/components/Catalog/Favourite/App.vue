<template>
  <div>
    <div class="favourite desktop_favourite" v-on:click="favouriteAction" v-bind:class="{mobile_favourite: isMobile}">
      <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
            d="M12.25 14.75L7 11L1.75 14.75V2.75C1.75 2.35218 1.90804 1.97064 2.18934 1.68934C2.47064 1.40804 2.85218 1.25 3.25 1.25H10.75C11.1478 1.25 11.5294 1.40804 11.8107 1.68934C12.092 1.97064 12.25 2.35218 12.25 2.75V14.75Z"
            stroke="#F0F1F8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <p v-if="!is_favourite">
        В избранное
      </p>
      <p v-if="is_favourite">
        Из избранного
      </p>
      <div v-if="count_likes > 0" class="line"></div>
      <p v-if="count_likes > 0">
        {{ count_likes }}
      </p>
    </div>
  </div>

  <!-- По нажатию кнопку закрыть, просто сворачивай модальное окно, никаких "window.location.href" -->
  <SuccessAlert v-if="is_success" @closeModal="closeSuccessAlert" :is-success="true" :title="'Успешно'"
                :subtitle="notion_text" :linkTitle="'Вернуться'" :noRedirect="true"/>
  <SuccessAlert v-if="is_error" @closeModal="closeSuccessAlert" :is-success="false" :title="'Ошибка'"
                :subtitle="error_body.user_id" :linkTitle="'Вернуться'" :noRedirect="true"/>
</template>

<script>
import SpecialistService from "@/api/SpecialistService";
import {EXCEPTION_NAME_EXTERNAL, EXCEPTION_NAME_ERROR_FIELDS} from '@/codes';
import SuccessAlert from "@/components/UI/SuccessAlert/SuccessAlert.vue";
import {MUTATION_SET_AUTH_WINDOW_STATUS} from "@/components/HeaderAuth/store/types/mutations";
import {mapGetters} from "vuex";
import {GETTER_USER_DATA} from '@/store/types/getters';
import {GETTER_SEARCH_RESULTS} from '@/components/Catalog/Search/store/types/getters'

export default {
  name: 'Favourite',
  components: {SuccessAlert},
  props: {
    specialistId: String,
    countLikes: String,
    isFavourite: String,
    isMobile: Boolean
  },
  data() {
    return {
      this_page: window.location.href,
      is_error: false,
      is_success: false,
      error_body: '',
      count_likes: 0,
      is_favourite: false,
      notion_text: '',
    }
  },
  mounted() {
    this.count_likes = this.countLikes;
    this.is_favourite = this.isFavourite === 'true';
  },
  methods: {
    favouriteAction: function() {
      if (this.is_favourite)
        this.removeFavourite();
      else
        this.addFavourite();
    },
    addFavourite: function () {
      let component = this;

      if (!this.userdata) {
        component.$store.commit(MUTATION_SET_AUTH_WINDOW_STATUS, true);
      } else {
        SpecialistService
            .addFavourite(this.specialistId)
            .then(function (response) {
              if (response.data.status === true) {
                component.is_favourite = true
                component.count_likes++;
              }
              component.notion_text = 'Вы добавили этого специалиста в избранное';
              component.is_success = true;
            })
            .catch(function (error) {
              if (error.name === EXCEPTION_NAME_ERROR_FIELDS) {
                component.error_body = error.values;
              } else if (error.name === EXCEPTION_NAME_EXTERNAL) {
                component.error_body = error.text;
              }

              component.is_error = true;
            });
      }
    },
    removeFavourite: function () {
      let component = this;

      if (!this.userdata) {
        component.$store.commit(MUTATION_SET_AUTH_WINDOW_STATUS, true);
      } else {
        SpecialistService
            .removeFavourite(this.specialistId)
            .then(function (response) {
              if (response.data.status === true) {
                component.is_favourite = false
                component.count_likes--;
              }
              component.notion_text = 'Вы удалили этого специалиста из избранного';
              component.is_success = true;
            })
            .catch(function (error) {
              if (error.name === EXCEPTION_NAME_ERROR_FIELDS) {
                component.error_body = error.values;
                component.is_error = true;
              } else if (error.name === EXCEPTION_NAME_EXTERNAL) {
                component.error_body = error.text;
                component.is_error = true;
              }
            });
      }
    },
    //utility method
    closeSuccessAlert: function () {
      this.is_error = false;
      this.is_success = false;
    }
  },
  computed: {
    ...mapGetters({
      'userdata': GETTER_USER_DATA,
      'results': GETTER_SEARCH_RESULTS
    })
  }
}
</script>

<style scoped lang="scss">
.favourite {
  position: absolute;
  top: 41px;
  right: 28px;
  z-index: 10;
  background: #87B1CA;
  border-radius: 34px;
  width: 191px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;

  svg {
    margin-right: 10px;
  }

  p {
    font-weight: 500;
    font-size: 14px;
    line-height: 20px;
    color: #FFFFFF;
  }

  .line {
    width: 1px;
    height: 100%;
    margin: 0 13px 0 16px;
    background: #F6F9FD;
  }
}

@media(max-width: 960px) {
  .favourite {
    top: unset;
    right: unset;
    left: 20px;
    bottom: 20px;
    width: 160px;
    padding: 15px;
    box-sizing: border-box;
    .line{
      margin: 0 7px 0 10px;
    }
    p{
      font-size: 12px;
      line-height: 16px;
    }
  }
}

@media(max-width: 767px) {
  .favourite{
    position: relative;
    top: unset;
    left: unset;
    right: unset;
    bottom: unset;
    width: auto;
    height: auto;
    padding: 18px 24px;
    svg{
      margin: 0;
    }
    .line{
      display: none;
    }
    p{
      margin-left: 10px;
      &:first-of-type{
        display: none;
      }
    }
  }
}


</style>
