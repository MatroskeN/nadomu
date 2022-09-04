import { EXCEPTION_NAME_EXTERNAL } from '@/codes'

function ExternalExceptions (message, code) {
    const error = new Error(message);

    error.name = EXCEPTION_NAME_EXTERNAL;
    error.text = message;
    error.code = code;

    return error;
}

ExternalExceptions.prototype = Object.create(Error.prototype);

export default ExternalExceptions;
