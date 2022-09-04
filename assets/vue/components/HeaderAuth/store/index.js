import { GETTER_IS_AUTH_WINDOW_OPEN } from "./types/getters";
import { MUTATION_SET_AUTH_WINDOW_STATUS } from "./types/mutations";


const state = {
    is_auth_open: false
};

const getters = {
    [GETTER_IS_AUTH_WINDOW_OPEN](state) {
        return state.is_auth_open;
    }
};

const actions = {

};

const mutations = {
    [MUTATION_SET_AUTH_WINDOW_STATUS](state, status) {
        state.is_auth_open = status;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
