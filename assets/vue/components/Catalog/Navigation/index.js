import { createApp } from 'vue'
//подключаем глобальный стор, из которого уже тянутся сторы модулей
import { store } from '@/store.js'

import Navigation from './App.vue'

createApp(Navigation).use(store).mount('#Navigation')