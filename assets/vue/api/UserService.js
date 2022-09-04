import ApiService from '@/common/api'
import ErrorsException from "@/common/exceptions/ErrorsException";

const UserService = {
    getUserData() {
        return ApiService.get(null, '/api/user').then(function (response) {
            if (response.status !== 200)
                throw new ErrorsException(response.data.error);

            return response;
        });
    },
    fillUserProfile(email, last_name, first_name, patronymic_name, promo) {
        return ApiService.post(null, '/api/user/init', {
            "email": email,
            "last_name": last_name,
            "first_name": first_name,
            "patronymic_name": patronymic_name,
            "promo": promo
        }).then(function (response) {
            if (response.status !== 200)
                throw new ErrorsException(response.data.error);

            return response;
        })
    },
    updateUserProfile(data) {
        return ApiService.patch(null, '/api/user', {
            "first_name": data.first_name,
            "last_name": data.last_name,
            "patronymic_name": data.patronymic_name,
            "phone": data.phone,
            "email": data.email,
            "birthday": data.birthday,
            "avatar_id": data.avatar_id,
            "email_expert_answers": data.email_expert_answers,
            "email_new_requests": data.email_new_requests,
            "email_users_response": data.email_users_response,
            "sms_expert_answers": data.sms_expert_answers,
            "sms_new_requests": data.sms_new_requests,
            "sms_users_response": data.sms_users_response
        }).then(function (response) {
            if (response.status !== 200)
                throw new ErrorsException(response.data.error);

            return response;
        })
    },
}

export default UserService;
