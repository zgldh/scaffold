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
  let normalData = {};

  for (let key in object) {
    if (object.hasOwnProperty(key)) {
      if (object[key] && object[key].constructor === File) {
        formData.append(key, object[key]);
      }
      else {
        normalData[key] = object[key];
      }
    }
  }

  formData.append('_data', JSON.stringify(normalData));

  return formData;
}

/**
 * @return {string}
 */
export function BuildAutoSearchQuery (columns, term) {
  if (columns.constructor !== Array) {
    columns = [columns];
  }
  columns.push('id');
  let query = 'draw=' + new Date().getTime() + '&start=0&length=25';
  query += '&search[regex]=false&search[value]=' + encodeURIComponent(term);

  for (let i = 0; i < columns.length; i++) {
    query += '&columns[' + i + '][name]=' + columns[i] + '&columns[' + i + '][searchable]=true';
  }
  return query;
}