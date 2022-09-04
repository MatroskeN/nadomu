import ApiService from '@/common/api'
import ErrorsException from "@/common/exceptions/ErrorsException";

const SpecialistService = {
    updateSpecialistProfile(data) {
        return ApiService.patch(null, '/api/specialist', {
            "gender": data.gender,
            "region_id": data.region_id,
            "stations_id": data.stations_id,
            "cities_id": data.cities_id,
            "callback_phone": data.callback_phone,
            "services": data.services,
            "worktime": data.worktime,
            "time_range": data.time_range,
            "profile_photo": data.public_photo,
            "public_docs": data.public_docs,
            "private_docs": data.private_docs,
            "experience": data.experience,
            "education": data.education,
            "about": data.about
        }).then(function (response) {
            if (response.status !== 200)
                throw new ErrorsException(response.data.error);

            return response;
        })
    },
    addFavourite(id){
        return ApiService.post(null, '/api/specialist/favorite', {
            "specialist_id": id
        }).then(function (response) {
            if (response.status !== 200)
                throw new ErrorsException(response.data.error);

            return response;
        })
    },
    removeFavourite(id){
        return ApiService.delete(null, '/api/specialist/favorite', {
            "specialist_id": id
        }).then(function (response) {
            if (response.status !== 200)
                throw new ErrorsException(response.data.error);

            return response;
        })
    },

    getFavouriteList(id){
        return ApiService.get(null, '/api/specialist/favorite',{
            "specialist_id": id
        }).then(function (response) {
            if (response.status !== 200)
                throw new ErrorsException(response.data.error);

            return response;
        })
    }
}

export default SpecialistService;