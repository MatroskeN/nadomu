import { createApp } from 'vue'
//подключаем глобальный стор, из которого уже тянутся сторы модулей
import { store } from '@/store.js'
import ChangeNumber from './App.vue'

createApp(ChangeNumber).use(store).mount('#ChangeNumber')
