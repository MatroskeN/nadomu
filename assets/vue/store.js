import {createStore} from 'vuex';

import { actions } from '@/store/actions';
import { getters } from '@/store/getters';
import { mutations } from '@/store/mutations';

import HeaderAuth from "@/components/HeaderAuth/store/";
import Search from "@/components/Catalog/Search/store/";



const state = {
    userdata: null,
    service_info: null,
    load_flags: {
        userdata: false,
        system: false
    }
};

export const store = createStore({
    state,
    getters,
    actions,
    mutations,
    modules: {
        HeaderAuth,
        Search
    }
});
