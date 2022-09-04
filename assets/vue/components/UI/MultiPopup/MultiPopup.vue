<template>
  <div class="multipurpose_popup" v-show="isOpen" v-bind:class="{active : isOpen}">
    <div class="backgroundLayer" v-on:click="isOpen = !isOpen"></div>

    <div class="popup_window">
      <div class="xClose" v-on:click="isOpen = !isOpen">
        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M18 1L1 18" stroke="#283848" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M1 1L18 18" stroke="#283848" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="title">
        {{ title }}
      </div>

      <div class="subtitle">
        {{ text }}
      </div>
    </div>
  </div>
</template>
<script>

export default {
  name: 'MultiPopup',
  props: {
    title: String,
    text: String,
    isOpen: Boolean
  }
}
</script>

<style scoped lang="scss">
@import 'assets/scss/default.scss';
.multipurpose_popup {
  display: none;
  position: fixed;
  z-index: 1100;
  width: 100vw;
  height: 100vh;
  top: 0;
  left: 0;
  align-items: center;
  justify-content: center;

  .backgroundLayer {
    background: rgba(44, 83, 122, 0.7);
    position: absolute;
    width: 100%;
    height: 100%;
  }

  .popup_window {
    background: #FFFFFF;
    box-shadow: 0 5px 35px rgba(21, 30, 41, 0.1);
    border-radius: 15px;
    padding: 74px 60px;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;

    .xClose {
      position: absolute;
      right: 24px;
      top: 23px;
      cursor: pointer;
    }

    .title {
      font-family: $base-font;
      font-style: normal;
      font-weight: 600;
      font-size: 16px;
      line-height: 20px;
      color: #283848;
    }
    .subtitle{
      font-family: $base-font;
      font-style: normal;
      font-weight: 600;
      font-size: 20px;
      line-height: 20px;
      color: #283848;
      margin-top: 20px;
    }

  }
}

.active {
  display: flex;
}
</style>
