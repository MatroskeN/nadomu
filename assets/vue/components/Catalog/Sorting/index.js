import { createApp } from 'vue'
//подключаем глобальный стор, из которого уже тянутся сторы модулей
import { store } from '@/store.js'

import Sorting from './App.vue'

createApp(Sorting).use(store).mount('#Sorting')