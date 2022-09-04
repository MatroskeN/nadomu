import {API_HOST} from '@/config';
import {ERROR_API_UNAUTHORIZED, ERROR_API_SYSTEM_FAILED} from '@/codes'
import ExternalException from "./exceptions/ExternalException";


const ApiService = {
    request(token, method, url, data) {
        let request_url = API_HOST + url;
        let fetch_options = {
            method: method
        };

        if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH' || method === 'DELETE'))
            fetch_options.body = JSON.stringify(data);

        if (token)
            fetch_options.headers = {
                'Authorization': 'Bearer ' + token,
            };

        return fetch(request_url, fetch_options)
            .then(function (response) {
                return response.json().then(function (data) {
                    return {
                        data: data,
                        status: response.status
                    };
                });
            })
            .then(function (response) {
                if (response.status === 401)
                    throw new ExternalException('Ошибка авторизации', ERROR_API_UNAUTHORIZED);
                if (response.status === 500)
                    throw new ExternalException('Системная ошибка', ERROR_API_SYSTEM_FAILED);

                return response;
            });
    },

    get(token, url) {
        return this.request(token, 'GET', url);
    },

    post(token, url, data) {
        return this.request(token, 'POST', url, data);
    },

    put(token, url, data) {
        return this.request(token, 'PUT', url, data);
    },

    patch(token, url, data) {
        return this.request(token, 'PATCH', url, data);
    },

    delete(token, url, data) {
        return this.request(token, 'DELETE', url, data);
    },
};

export default ApiService;
