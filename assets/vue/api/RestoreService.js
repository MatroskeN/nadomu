import ApiService from '@/common/api'
import ErrorsException from "@/common/exceptions/ErrorsException";

const RestoreService = {
    restoreNumber(email){
        return ApiService.post(null, '/api/user/restore', {
            "email": email
        }).then(function (response) {
            if (response.status !== 200)
                throw new ErrorsException(response.data.error);
            return response;
        })
    },
    restoreByPhone(phone,phone_repeat,user_id,code){
        return ApiService.post(null, '/api/user/restore/phones', {
            "phone": phone,
            "phone_repeat": phone_repeat,
            "user_id": user_id,
            "code": code
        }).then(function (response) {
            if (response.status !== 200)
                throw new ErrorsException(response.data.error);
            return response;
        })
    },
    restoreCheckCode(user_id, auth_id, auth_code, restore_code){
        return ApiService.post(null, '/api/user/restore/code', {
            "user_id": user_id,
            "auth_id": auth_id,
            "auth_code": auth_code,
            "restore_code": restore_code
        }).then(function (response){
            if (response.status !== 200)
                throw new ErrorsException(response.data.error);
            return response;
        })
    }
}

export default RestoreService;