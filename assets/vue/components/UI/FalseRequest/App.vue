<template>
  <SuccessAlert v-if="show_window" @closeModal="closeSuccessAlert" :title="'Ссылка устарела'" :subtitle="'Сссылка для смены номера устарела или уже активирована'" :linkTitle="'На главную'"
                :link="'/'" :is-success="false" />
</template>
<script>
import SuccessAlert from "@/components/UI/SuccessAlert/SuccessAlert.vue";
import RedirectService from "@/common/redirect";
export default {
  name: 'FalseRequest',
  components: {SuccessAlert},
  data() {
    return{
      show_window: true,
    }
  },
  methods: {
    redirectAction(href){
      RedirectService
        .link(href)
    },
    closeSuccessAlert() {
      this.show_window = false;
      this.redirectAction('/')
    }
  }
}
</script>