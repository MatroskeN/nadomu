import { createApp } from 'vue'
//подключаем глобальный стор, из которого уже тянутся сторы модулей
import { store } from '@/store.js'

import Filter from './App.vue'

createApp(Filter).use(store).mount('#Filter')