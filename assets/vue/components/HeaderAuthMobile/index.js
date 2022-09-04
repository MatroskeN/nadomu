import { createApp } from 'vue'
//подключаем глобальный стор, из которого уже тянутся сторы модулей
import { store } from '@/store.js'
import HeaderAuthMobile from './App.vue'

createApp(HeaderAuthMobile).use(store).mount('#HeaderAuthMobile')
