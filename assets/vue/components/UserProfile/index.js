import { createApp } from 'vue'
//подключаем глобальный стор, из которого уже тянутся сторы модулей
import { store } from '@/store.js'

import UserProfile from './App.vue'

createApp(UserProfile).use(store).mount('#UserProfile')