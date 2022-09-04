import {
    MUTATION_SET_USER_DATA,
    MUTATION_SET_LOAD_USER_DATA,
    MUTATION_SET_SERVICE_INFO,
    MUTATION_SET_LOAD_SYSTEM_DATA
} from '@/store/types/mutations';


export const mutations = {
    [MUTATION_SET_USER_DATA](state, status) {
        state.userdata = status;
    },
    [MUTATION_SET_LOAD_USER_DATA](state, status) {
        state.load_flags.userdata = status;
    },
    [MUTATION_SET_LOAD_SYSTEM_DATA](state, status) {
        state.load_flags.system = status;
    },

    [MUTATION_SET_SERVICE_INFO](state, data) {
        state.service_info = data;
    }

};
