"use strict";

/**
 * (Hermann Ingjaldsson) http://stackoverflow.com/a/16938481
 */
function getBrowserFromUserAgent(ua) {
    if (ua) {
        var tem, M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
        if (/trident/i.test(M[1])) {
            tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
            return { name: 'IE', version: (tem[1] || '') };
        }
        if (M[1] === 'Chrome') {
            tem = ua.match(/\b(OPR|Edge)\/(\d+)/);
            if (tem != null) return { name: tem[1].replace('OPR', 'Opera'), version: tem[2] };
        }
        M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
        if ((tem = ua.match(/version\/(\d+)/i)) != null) M.splice(1, 1, tem[1]);
        return { name: M[0], version: M[1] };
    } else {
        return (null);
    }
}

function getOSFromUserAgent(ua) {
    if (ua) {
        var tmp = [
            // Match user agent string with operating systems
            { os: 'Windows 3.11', regex: 'Win16' },
            { os: 'Windows 95', regex: '(Windows 95)|(Win95)|(Windows_95)' },
            { os: 'Windows 98', regex: '(Windows 98)|(Win98)' },
            { os: 'Windows 2000', regex: '(Windows NT 5.0)|(Windows 2000)' },
            { os: 'Windows XP', regex: '(Windows NT 5.1)|(Windows XP)' },
            { os: 'Windows Server 2003', regex: '(Windows NT 5.2)' },
            { os: 'Windows Vista', regex: '(Windows NT 6.0)' },
            { os: 'Windows 7', regex: '(Windows NT 6.1)|(Windows NT 7.0)' },
            { os: 'Windows NT 4.0', regex: '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)' },
            { os: 'Windows ME', regex: 'Windows ME' },
            { os: 'Open BSD', regex: 'OpenBSD' },
            { os: 'Sun OS', regex: 'SunOS' },
            { os: 'Linux', regex: '(Linux)|(X11)' },
            { os: 'Mac OS', regex: '(Mac_PowerPC)|(Macintosh)' },
            { os: 'QNX', regex: 'QNX' },
            { os: 'BeOS', regex: 'BeOS' },
            { os: 'OS/2', regex: 'OS/2' },
            { os: 'Search Bot', regex: '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)' }
        ];
        for (var i = 0; i < tmp.length; i++) {
            var a = new RegExp(tmp[i].regex, 'gi');
            if (ua.match(a)) {
                return (tmp[i].os);
            }
        }
        return (null);
    } else {
        return (null);
    }
}