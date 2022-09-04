import ApiService from '@/common/api'
import ErrorsException from "@/common/exceptions/ErrorsException";

const FilterService = {
    filterSpecialist(data) {
        return ApiService.post(null, '/api/filter/specialist', {
            "gender": data.gender,
            "page": data.page,
            "experience": data.experience,
            "price_min": data.price_min,
            "price_max": data.price_max,
            "city_id": data.city_id,
            "metro_id": data.metro_id,
            "sort": data.sort,
            "rating": data.rating,
            "service_id": data.service_id,
            "worktime": data.worktime
        }).then(function (response) {
            if (response.status !== 200)
                throw new ErrorsException(response.data.error);

            return response;
        })
    }
}

export default FilterService;