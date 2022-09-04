import { createApp } from 'vue'
//подключаем глобальный стор, из которого уже тянутся сторы модулей
import { store } from '@/store.js'
import ConfirmNumber from './App.vue'

createApp(ConfirmNumber).use(store).mount('#ConfirmNumber')
