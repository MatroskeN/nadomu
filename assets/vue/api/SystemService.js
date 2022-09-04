import ApiService from '@/common/api'
import ErrorsException from "@/common/exceptions/ErrorsException";

const SystemService = {
    getData() {
        return ApiService.get(null, '/api/system/data').then(function (response) {
            if (response.status !== 200)
                throw new ErrorsException(response.data.error);

            return response;
        });
    }
}

export default SystemService;
