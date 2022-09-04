import { createApp } from 'vue'
//подключаем глобальный стор, из которого уже тянутся сторы модулей
import { store } from '@/store.js'

import Search from './Search.vue'

createApp(Search).use(store).mount('#Search')