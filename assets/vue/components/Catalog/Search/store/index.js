import {
    GETTER_SEARCH_RESULTS,
    GETTER_IS_FILTER_RESULT_LOADING,
    GETTER_RESULT_TOTAL_COUNT,
    GETTER_PAGE,
    GETTER_SEARCH_BASE_DATA
} from "@/components/Catalog/Search/store/types/getters";
import {
    MUTATION_SET_SEARCH_FILTER_DATA,
    MUTATION_SET_SEARCH_SORT_DATA,
    MUTATION_SET_SEARCH_RESULT,
    MUTATION_SET_SEARCH_BASE_DATA,
    MUTATION_SET_FILTER_WORK_TIME,
    MUTATION_SET_INIT_SEARCH_STATUS,
    MUTATION_SET_FILTER_PAGE,
    MUTATION_SET_RESULT_TOTAL_COUNT
} from "@/components/Catalog/Search/store/types/mutations";
import {ACTION_SEARCH_REQUEST, ACTION_INIT_SEARCH_RESULT} from "@/components/Catalog/Search/store/types/actions";
import FilterService from "@/api/FilterService";

import {createApp} from 'vue';
import {store} from '@/store.js';

import SearchResults from '@/components/Catalog/Results/App.vue';

const state = {
    filter_data: {
        city_id: [],
        metro_id: [],
        service_id: '',
        gender: null,
        experience: null,
        price_min: 0,
        price_max: 100000,
        rating: null,
        worktime: [],
        page: 1,
    },
    is_mounted_result_component: false,
    is_filter_result_loading: false,
    result: [],
    resultTotalCount: null
};

const getters = {
    [GETTER_SEARCH_RESULTS](state) {
        return state.result;
    },
    [GETTER_RESULT_TOTAL_COUNT](state) {
        return state.resultTotalCount;
    },
    [GETTER_IS_FILTER_RESULT_LOADING](state) {
        return state.is_filter_result_loading;
    },
    [GETTER_SEARCH_BASE_DATA](state) {
        return state.filter_data;
    }
};

export const actions = {
    [ACTION_INIT_SEARCH_RESULT]({commit, state}) {
        if (!state.is_mounted_result_component) {
            createApp(SearchResults).use(store).mount('#search-results');

            commit(MUTATION_SET_INIT_SEARCH_STATUS);
        }
    },
    [ACTION_SEARCH_REQUEST]({commit, state, dispatch}) {
        dispatch(ACTION_INIT_SEARCH_RESULT);
        state.is_filter_result_loading = true;

        return FilterService
            .filterSpecialist(state.filter_data)
            .then(function (response) {
                if (response.data.status === true) {
                    commit(MUTATION_SET_SEARCH_RESULT, response.data.result)
                    commit(MUTATION_SET_RESULT_TOTAL_COUNT, response.data.resultTotalCount)
                    state.is_filter_result_loading = false;
                }
            })
            .catch(function (error) {
                commit(MUTATION_SET_SEARCH_RESULT, null);
                state.is_filter_result_loading = false;
            });
    }
};

const mutations = {
    [MUTATION_SET_INIT_SEARCH_STATUS](state) {
        state.is_mounted_result_component = true;
    },
    [MUTATION_SET_SEARCH_SORT_DATA](state, sort) {
        state.filter_data['sort'] = sort;
    },
    [MUTATION_SET_FILTER_PAGE](state, page) {
        state.filter_data['page'] = page;
    },
    [MUTATION_SET_SEARCH_BASE_DATA](state, data) {
        state.filter_data['city_id'] = data.cities_id;
        state.filter_data['metro_id'] = data.stations_id;
        state.filter_data['service_id'] = data.service_id;
    },
    [MUTATION_SET_FILTER_WORK_TIME](state, timesheet) {
        state.filter_data['worktime'] = timesheet;
    },
    [MUTATION_SET_SEARCH_FILTER_DATA](state, data) {
        state.filter_data['gender'] = data.gender;
        state.filter_data['experience'] = data.experience;
        state.filter_data['rating'] = data.rating;
        state.filter_data['price_min'] = data.price_min;
        state.filter_data['price_max'] = data.price_max;
    },
    [MUTATION_SET_SEARCH_RESULT](state, data) {
        state.result = data;
    },
    [MUTATION_SET_RESULT_TOTAL_COUNT](state, data) {
        state.resultTotalCount = data;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
