let pelayananStorage = {
    removeSoapSession : (instalasi = []) => {
        for (var i = 0; i < instalasi.length; i++) {
            let _sessionKey = 'suggestsoap';
            switch (instalasi[i]) {
                case 'rajal':
                    _sessionKey = _sessionKey + 'rj';
                    break;
                case 'ranap':
                    _sessionKey = _sessionKey + 'ranap';
                    break;
                case 'igd':
                    _sessionKey = _sessionKey + 'igd';
                    break;
                default:
                    return false;
            }
            for (const key in sessionStorage) {
                if(key.includes(_sessionKey)){
                    // sessionStorage.removeItem(key);
                }
            }
        }
    },
    checkUriModulSoapSession : () => {
        let pathName = window.location.pathname.split('/');
        let uriSegment1 = pathName.hasOwnProperty(1) ? pathName[1] : null; // ngambil dari 1, soalnya 0 nya sudah pasti string kosong
        let filterModule = ['rajal', 'ranap', 'igd'].filter((val) => val == uriSegment1);

        let lastUrl = localStorage.getItem("last-url");
        let urlBrowser = window.location.pathname;

        if (lastUrl != urlBrowser) {
            pelayananStorage.removeSoapSession(filterModule);
        }
    }
}

$(() => {
    pelayananStorage.checkUriModulSoapSession();
})