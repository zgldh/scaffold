import request from '@/utils/request'

export function notificationIndex(params) {
  return request({
    url: `/notification`,
    method: 'get',
    params
  })
}

export function notificationShow(id) {
  return request({
    url: `/notification/${id}`,
    method: 'get'
    // params
  })
}

export function notificationDestroy(id) {
  return request({
    // params,
    url: `/notification/${id}`,
    method: 'delete'
  })
}

export function putRead(id, data) {
  return request({
    // params,
    data,
    url: `/notification/${id}/read`,
    method: 'put'
  })
}

export function putReadAll(data) {
  return request({
    // params,
    data,
    url: `/notification/read_all`,
    method: 'put'
  })
}

export function putUnread(id, data) {
  return request({
    // params,
    data,
    url: `/notification/${id}/unread`,
    method: 'put'
  })
}

export function getReadLatest(lastCreatedAt) {
  return request({
    url: `/notification/read_latest/${lastCreatedAt}`,
    method: 'get'
    // params
  })
}
