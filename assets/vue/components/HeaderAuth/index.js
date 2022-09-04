import { createApp } from 'vue'
//подключаем глобальный стор, из которого уже тянутся сторы модулей
import { store } from '@/store.js';

import HeaderAuth from './App.vue'

createApp(HeaderAuth).use(store).mount('#HeaderAuth')
