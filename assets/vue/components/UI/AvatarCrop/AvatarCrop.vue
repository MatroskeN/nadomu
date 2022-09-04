<template>
  <div class="avatar">
    <div class="delete" v-show="isUploaded" v-on:click="deleteImage">
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
        <path d="M9 9H11V17H9V9Z" fill="currentColor" />
        <path d="M13 9H15V17H13V9Z" fill="currentColor" />
      </svg>
    </div>
    <label for="avatarCrop" class="image-previewer img" data-cropzee="avatarCrop"></label>
    <input id="avatarCrop" type="file" accept="image/*" style="display: none" :multiple="isMultiple">
  </div>
</template>

<script>
import './scripts/cropzee.js';
import Croppr from 'croppr';


export default {
  name: 'AvatarCrop',
  props: {
    isMultiple: Boolean,
  },
  data() {
    return {
      isUploaded: false
    }
  },
  mounted: function () {
    let component = this;

    //на этой лестинице держится все здание
    window.Croppr = Croppr;

    window.closeModal = function() {
      $('#cropzee-modal').remove();
    };

    window.confirmCrop = function() {
      component.isUploaded = true;
      component.$emit('removeTemporary');
      cropzeeCreateImage(`avatarCrop`);
    }


    $('#avatarCrop').cropzee({
      aspectRatio: 1 / 1,
      minSize: [300, 300, 'px'],
      maxSize: [2000, 2000, 'px'],
      onInitialize: function (instance) {

        component.isUploaded = false;

        $('.avatarBlock img.userPhoto').hide();

        let cropper_width = $(instance.cropperEl).width();
        let cropper_height = $(instance.cropperEl).height();

        //берем минимальный размер либо ширину, либо высоту, чтобы не вылезать за пределы пропорций
        let cropper_size = cropper_width > cropper_height ? cropper_height : cropper_width;
        let original_size = window.original_image_width > window.original_image_height ? window.original_image_height : window.original_image_width;

        let coef_min = Math.ceil(instance.options.minSize.width / (original_size / cropper_size));
        let coef_max = Math.floor(instance.options.maxSize.width / (original_size / cropper_size));
        if (coef_max > cropper_size)
          coef_max = cropper_size;

        let options = instance.options;
        options.minSize = {width: coef_min, height: coef_min};
        options.maxSize = {width: coef_max, height: coef_max};
        instance.options = options;
        instance.resizeTo(coef_max, coef_max);
      }
    });
  },
  methods: {
    openUpload() {
      $('#avatarCrop').click();
    },
    getImageValue() {
      let filename = $('#avatarCrop').val().replace(/.*(\/|\\)/, '');
      let image_data = cropzeeGetImage('avatarCrop');

      this.$emit('setImageData', filename, image_data);
    },
    deleteImage() {
      this.isUploaded = false;
      $('.image-previewer').hide()

      this.$emit('deleteImage');
    }
  }
}
</script>

<style lang="scss">
@import 'assets/scss/var.scss';
@import './styles/croppr.css';
@import './styles/light-modal.css';

.avatar{
  position: relative;
  display: flex;
  align-items: flex-start;
  .delete{
    position: absolute;
    z-index: 1;
    padding: 5px 5px 0 5px;
    background: #ffffff;
    border-radius: 5px;
    opacity: 0.7;
    top: 10px;
    right: 10px;
  }
  .image-previewer{
    position: relative;
    z-index: 0;
    width: 196px;
    margin-left: 130px;
  }
}

.light-modal-content{
  background: #ffffff;
  .light-modal-footer{
    background: #FFFFFF;
  }
  .light-modal-close-btn{
    background: #3f85ef;
    border-radius: 10px;
    font-family: $base-font;
    font-size: 14px;
    padding: 13px 27px;
  }
}

@media screen and (max-width: 767px){
  .avatar{
    .delete{
      top: 35px;
    }
    .image-previewer{
      margin-left: unset;
      margin-top: 25px;
    }
  }
  .light-modal-footer{
    flex-direction: column-reverse;
    .light-modal-close-btn{
      &:first-child{
        margin-top: 15px;
      }
    }
  }
}

</style>