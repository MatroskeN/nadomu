import { createApp } from 'vue'
//подключаем глобальный стор, из которого уже тянутся сторы модулей
import { store } from '@/store.js'

import Favourite from './App.vue'

document.querySelectorAll('.favouriteItemsTwig').forEach(function(item) {
    let el_id = item.getAttribute('id');

    createApp(Favourite, { ...item.dataset }).use(store).mount('#' + el_id)
});
