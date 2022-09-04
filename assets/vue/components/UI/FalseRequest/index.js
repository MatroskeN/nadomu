import { createApp } from 'vue'
//подключаем глобальный стор, из которого уже тянутся сторы модулей
import { store } from '@/store.js';

import FalseRequest from './App.vue'

createApp(FalseRequest).use(store).mount('#FalseRequest')
