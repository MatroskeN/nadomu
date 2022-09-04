<template>
  <div class="warning">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path
          d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
          stroke="#EF3F3F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M15 9L9 15" stroke="#EF3F3F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M9 9L15 15" stroke="#EF3F3F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <div class="text">
      <div class="warningTitle">
        {{ title }}
      </div>
      <div class="warningSubtitle">
        {{ subtitle }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Warning',
  props: {
    title: String,
    subtitle: String
  }
}
</script>

<style scoped lang="scss">
@import "assets/scss/default.scss";

.warning {
  padding: 21px 30px;
  background: #F8D3D3;
  border-radius: 10px;
  width: -webkit-fill-available;
  width: -moz-available;
  display: flex;
  align-items: flex-start;
  margin-bottom: 20px;

  .text {
    display: flex;
    flex-direction: column;
    margin-left: 19px;
    font-family: $base-font;
    font-style: normal;
    .warningTitle{
      font-weight: 600;
      font-size: 18px;
      line-height: 22px;
      color: #E53945;
    }
    .warningSubtitle{
      font-weight: 400;
      font-size: 14px;
      line-height: 16px;
      color: #E53945;
    }
  }
}
</style>