<template>
  <div class="mask">
    <div class="window">
      <svg
          v-on:click="closeModal"
          class="xClose"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
      >
        <path
            d="M6.2253 4.81108C5.83477 4.42056 5.20161 4.42056 4.81108 4.81108C4.42056 5.20161 4.42056 5.83477 4.81108 6.2253L10.5858 12L4.81114 17.7747C4.42062 18.1652 4.42062 18.7984 4.81114 19.1889C5.20167 19.5794 5.83483 19.5794 6.22535 19.1889L12 13.4142L17.7747 19.1889C18.1652 19.5794 18.7984 19.5794 19.1889 19.1889C19.5794 18.7984 19.5794 18.1652 19.1889 17.7747L13.4142 12L19.189 6.2253C19.5795 5.83477 19.5795 5.20161 19.189 4.81108C18.7985 4.42056 18.1653 4.42056 17.7748 4.81108L12 10.5858L6.2253 4.81108Z"
            fill="currentColor"
        />
      </svg>
      <svg v-if="isSuccess" width="75" height="70" viewBox="0 0 75 70" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="37.5" cy="34.5612" rx="37.5" ry="34.5612" fill="#EDF4FE"/>
        <path d="M54 23.9624L31.3125 45.16L21 35.5247" stroke="#3F85EF" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <svg v-if="!isSuccess" width="75" height="70" viewBox="0 0 75 70" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="37.5" cy="34.5612" rx="37.5" ry="34.5612" fill="#EDF4FE"/>
        <g clip-path="url(#clip0_644_841)">
          <path d="M36.998 57.6647C49.4245 57.6647 59.498 47.7777 59.498 35.5814C59.498 23.3851 49.4245 13.498 36.998 13.498C24.5716 13.498 14.498 23.3851 14.498 35.5814C14.498 47.7777 24.5716 57.6647 36.998 57.6647Z" stroke="#EF3F3F" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M37 24.4316V39.4316" stroke="#EF3F3F" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M37 45.7651H37.0219" stroke="#EF3F3F" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
        </g>
        <defs>
          <clipPath id="clip0_644_841">
            <rect width="54" height="49" fill="white" transform="translate(10 10.4316)"/>
          </clipPath>
        </defs>
      </svg>
      <div class="title">
        {{title}}
      </div>
      <div class="subtitle">
        {{subtitle}}
      </div>
      <a :href="link" v-if="!noRedirect">
        <button>
          {{ linkTitle }}
        </button>
      </a>
      <a v-if="noRedirect">
        <button v-on:click="closeModal">
          {{linkTitle}}
        </button>
      </a>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SuccessAlert',
  props: {
    isSuccess: Boolean,
    noRedirect: Boolean,
    title: '',
    subtitle: '',
    linkTitle: '',
    link: '',
  },
  methods: {
    closeModal() {
      this.$emit('closeModal');
    }
  }
}

</script>

<style scoped lang="scss">
@import 'assets/scss/default.scss';

.mask {
  background: rgba(0,0,0,0.5);
  width: 100vw;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 190;
  display: flex;
  align-items: center;
  justify-content: center;
  .window{
    background: #FFFFFF;
    max-width: 500px;
    box-shadow: 0px 5px 35px rgba(21, 30, 41, 0.1);
    border-radius: 15px;
    padding: 63px 101px;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    .xClose{
      position: absolute;
      top: 15px;
      right: 15px;
      cursor: pointer;
    }
    .title{
      font-family: $base-font;
      font-style: normal;
      font-weight: 600;
      font-size: 25px;
      line-height: 31px;
      color: #283848;
      margin-top: 49px;
      text-align: center;
    }
    .subtitle{
      margin-top: 15px;
      font-family: $base-font;
      font-style: normal;
      font-weight: 400;
      font-size: 18px;
      line-height: 22px;
      color: #283848;
      text-align: center;
    }
    button{
      margin-top: 34px;
    }
  }
}

@media(max-width: 600px){
  .mask{
    .window{
      max-width: 260px;
      padding: 15px 25px;
    }
  }
}
</style>