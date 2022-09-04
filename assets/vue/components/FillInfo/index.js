import { createApp } from 'vue'
//подключаем глобальный стор, из которого уже тянутся сторы модулей
import { store } from '@/store.js'
import FillInfo from './App.vue'

createApp(FillInfo).use(store).mount('#FillInfo')
