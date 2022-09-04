import {
    GETTER_USER_DATA,
    GETTER_USER_NAME,
    GETTER_IS_LOAD_USERDATA,
    GETTER_IS_LOAD_SYSTEM_DATA,
    GETTER_SYSTEM_DATA,
    GETTER_ALPHABET_STATIONS,
    GETTER_ALPHABET_CITIES,
    GETTER_SYSTEM_SERVICES,
} from '@/store/types/getters';

import ToolkitService from "@/common/toolkit";


export const getters = {
    [GETTER_USER_DATA](state) {
        return state.userdata;
    },
    [GETTER_IS_LOAD_USERDATA](state) {
        return state.load_flags.userdata;
    },
    [GETTER_IS_LOAD_SYSTEM_DATA](state) {
        return state.load_flags.system;
    },
    [GETTER_SYSTEM_DATA](state) {
        return state.service_info;
    },
    [GETTER_SYSTEM_SERVICES](state) {
        return (state.service_info && state.service_info.services) ? state.service_info.services : [];
    },
    [GETTER_ALPHABET_STATIONS](state) {
        if (state.service_info && state.service_info.stations) {
            return groupByFirstLetter(state.service_info.stations);
        }
        return null;
    },
    [GETTER_ALPHABET_CITIES](state) {
        if (state.service_info && state.service_info.cities) {
            return groupByFirstLetter(state.service_info.cities);
        }
        return null;
    },
    [GETTER_USER_NAME](state) {
        if (state.userdata) {
            let userinfo = state.userdata.userinfo;

            let name = ((userinfo.last_name ?? '') + ' ' + (userinfo.first_name ?? '')).trim();
            if (name === '')
                name = ToolkitService.formatPhoneNumber(userinfo.phone);

            return name;
        } else
            return null;
    }
};

// разбиваем на группы по буквам
function groupByFirstLetter(items) {
    let result = {};

    items.forEach(function(v) {
        let letter = (v.name).charAt(0).toUpperCase();

        if (typeof result[letter] == 'undefined')
            result[letter] = {
                letter: letter,
                items: []
            };

        result[letter].items.push(v);
    });

    return Object.values(result);
}
