import { createApp } from 'vue'
//подключаем глобальный стор, из которого уже тянутся сторы модулей
import { store } from '@/store.js'

import Catalog from './App.vue'

createApp(Catalog).use(store).mount('#Catalog')