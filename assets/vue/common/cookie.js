const CookieService = {
    set: function (name, value, expires, path, domain, secure) {
        var expires = expires || 32140800;
        var path = path || '/';

        expires instanceof Date ? expires = expires.toGMTString() : typeof (expires) == 'number' && (expires = (new Date(+(new Date) + expires * 1e3)).toGMTString());
        var r = [name + "=" + escape(value)], s, i;
        for (i in s = {expires: expires, path: path, domain: domain}) {
            s[i] && r.push(i + "=" + s[i]);
        }
        return secure && r.push("secure"), document.cookie = r.join(";"), true;
    },
    get: function (name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");

        if (parts.length == 2)
            return parts.pop().split(";").shift();
        else
            return false;
    }
}

export default CookieService;