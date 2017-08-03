export function getCookie (cookieName) {
  if (document.cookie.length > 0) {
    var cookieStart = document.cookie.indexOf(cookieName + '=');
    if (cookieStart !== -1) {
      cookieStart = cookieStart + cookieName.length + 1;
      var cookieEnd = document.cookie.indexOf(';', cookieStart);
      if (cookieEnd === -1) cookieEnd = document.cookie.length;
      return decodeURIComponent(document.cookie.substring(cookieStart, cookieEnd));
    }
  }
  return '';
}

export function getXsrfToken () {
  return getCookie('XSRF-TOKEN');
}

export function BuildHttpRequestPayload (object) {
  let formData = new FormData();
  for (let key in object) {
    if (object.hasOwnProperty(key)) {
      formData.append(key, object[key]);
    }
  }

  return formData;
}