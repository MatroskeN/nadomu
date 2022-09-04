import { createApp } from 'vue'
//подключаем глобальный стор, из которого уже тянутся сторы модулей
import { store } from '@/store.js'

import SpecialistProfile from './App.vue'

createApp(SpecialistProfile).use(store).mount('#SpecialistProfile')