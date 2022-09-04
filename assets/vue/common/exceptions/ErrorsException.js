import { EXCEPTION_NAME_ERROR_FIELDS } from '@/codes'

function ErrorsException (errors, code) {
    const message = JSON.stringify(errors);
    const error = new Error(message);

    error.name = EXCEPTION_NAME_ERROR_FIELDS;
    error.code = code || null;
    error.values = errors;

    return error;
}

ErrorsException.prototype = Object.create(Error.prototype);

export default ErrorsException;
