import { ACTION_LOGOUT, ACTION_INIT_DATA, ACTION_GET_SERVICE_INFO, ACTION_GET_USER_INFO } from '@/store/types/actions';
import { MUTATION_SET_USER_DATA, MUTATION_SET_LOAD_USER_DATA, MUTATION_SET_SERVICE_INFO, MUTATION_SET_LOAD_SYSTEM_DATA } from '@/store/types/mutations';
import { GETTER_IS_LOAD_USERDATA, GETTER_IS_LOAD_SYSTEM_DATA } from '@/store/types/getters';


import CookieService from "@/common/cookie";
import UserService from "@/api/UserService";
import SystemService from "@/api/SystemService";


export const actions = {
    [ACTION_INIT_DATA]({ commit, dispatch, getters }) {
        let token = CookieService.get('token');
        let is_user_load = getters[GETTER_IS_LOAD_USERDATA];
        let is_system_load = getters[GETTER_IS_LOAD_SYSTEM_DATA];

        if (!is_system_load) {
            //тянем данные по сервису: города, метро, услуги и т.д.
            dispatch(ACTION_GET_SERVICE_INFO);
            commit(MUTATION_SET_LOAD_SYSTEM_DATA, true);
        }

        if (token && !is_user_load) {
            //тянем данные пользователя
            dispatch(ACTION_GET_USER_INFO);
        }
    },
    [ACTION_LOGOUT]({ commit }) {
        CookieService.set('token', '');

        commit(MUTATION_SET_USER_DATA, null);
    },
    [ACTION_GET_SERVICE_INFO]({ commit }) {
        return SystemService
            .getData()
            .then(function (response) {
                if (response.data.status === true)
                    commit(MUTATION_SET_SERVICE_INFO, response.data);
            })
            .catch(function (error) {
                commit(MUTATION_SET_SERVICE_INFO, null);
            });
    },
    [ACTION_GET_USER_INFO]({ commit, dispatch }) {
        UserService
            .getUserData()
            .then(function (response) {
                if (response.data.status === true)
                    commit(MUTATION_SET_USER_DATA, response.data.user);
                else
                    dispatch(ACTION_LOGOUT);

                commit(MUTATION_SET_LOAD_USER_DATA, true);
            })
            .catch(function (error) {
                dispatch(ACTION_LOGOUT);
                commit(MUTATION_SET_LOAD_USER_DATA, false);
            });
    },
};
