<template>
  <div class="modal" v-bind:class="{active : isOpen}">
    <div class="backgroundLayer" v-on:click="closeModal()"></div>

    <div class="modalWindow">
      <div class="xClose" v-on:click="closeModal()">
        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M18 1L1 18" stroke="#283848" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M1 1L18 18" stroke="#283848" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>

      <slot name="body">
      </slot>
      <slot name="second_body">
      </slot>
    </div>
  </div>

</template>


<script>
export default {
  name: 'Modal',
  props: {
    isOpen: Boolean
  },
  methods: {
    closeModal() {
      this.$emit('closeModal')
    }
  }
}
</script>

<style scoped lang="scss">
@import 'assets/scss/default.scss';

.modal {
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

  .modalWindow {
    background: $white;
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
  }
}

@media(max-width: 600px) {
  .modal {
    .modalWindow {
      padding: 74px 39px;
    }
  }
}
</style>
