<script>

import Favourite from '@/components/Catalog/Favourite/App.vue'
import ToolkitService from "@/common/toolkit";

export default {
  name: 'SearchCard',
  template: '#specialist_item',
  props: {
    data: Object
  },
  components: {
    Favourite
  },
  computed: {
    gender_rus() {
      if (this.data.userinfo.gender === 'male')
        return 'Медбрат / Мужчина';
      else if (this.data.userinfo.gender === 'female')
        return 'Медсестра / Женщина';
      else
        return null;
    },
    comment_count() {
      return this.comment_plural();
    },
    comment_plural() {
      return ToolkitService.plural(this.data.comments.length, 'комментарий','комментария','комментариев')
    },
    experience_plural() {
      return ToolkitService.plural(this.data.experience, 'год', 'года', 'лет')
    },
    services_list() {
      let servicesList = this.data.services.map((v, k) => v.service.name);
      return servicesList.join(', ');
    },
    metro_string() {
      let metro = this.data.location.metro.map((v, k) => 'м. ' + v.name);
      let cities = this.data.location.cities.map((v, k) => 'г. ' + v.name);
      let result = [...metro, ...cities]
      return result.join(', ');
    },
    buffer_rating() {
      return Math.floor(this.data.userinfo.rating);
    },
    star_count() {
      if (this.buffer_rating > 0 && this.buffer_rating <= 5){
        return this.buffer_rating
      } else if (this.buffer_rating > 5) {
        return 5
      } else {
        return 0
      }
    },
    left_rating() {
      if (this.buffer_rating >= 0 && this.buffer_rating <= 5) {
        return 5 - this.buffer_rating
      } else if (this.buffer_rating < 0){
        return 5
      } else if (this.buffer_rating > 5) {
        return 0
      }
    },
    specialist_image() {
      let photo = this.data.images?.profile?.filepath;

      if (photo)
        return '/' + photo;
      else if (!photo && this.data.userinfo.gender === 'male')
        return '/images/default_man.png';
      else
        return '/images/default_woman.png';
    }
  }
}
</script>

<style scoped lang="scss">

</style>
