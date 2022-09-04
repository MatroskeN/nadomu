import ApiService from '@/common/api'
import ErrorsException from "@/common/exceptions/ErrorsException";

const SmsService = {
    sendCode(phone, role) {

        return ApiService.post(null, '/api/sms', {
            "phone": phone,
            "role": role
        }).then(function (response) {
            if (response.status !== 200)
                throw new ErrorsException(response.data.error);

            return response;
        });
    },
    checkCode(code_id, value) {
        return ApiService.patch(null, '/api/sms', {
            "code_id": code_id,
            "value": value
        }).then(function (response) {
            if (response.status !== 200)
                throw new ErrorsException(response.data.error, response.status);

            return response;
        });
    },
    dialCode(code_id) {
        return ApiService.post(null, '/api/dial', {
            "code_id": code_id
        }).then(function (response) {
            return response;
        })
    },
}

export default SmsService;
