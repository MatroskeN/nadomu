import ApiService from '@/common/api'
import ErrorsException from "@/common/exceptions/ErrorsException";

const FileService = {
    uploadFile(filename,filetype,filedata){
        return ApiService.post(null, '/api/file', {
            "filename": filename,
            "filetype": filetype,
            "filedata": filedata
        }).then(function (response) {
            if (response.status !== 200)
                throw new ErrorsException(response.data.error);

            return response;
        })
    }
}

export default FileService;