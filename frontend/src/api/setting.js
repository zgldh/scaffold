import request from '@/utils/request'
export function SettingIndex() {
  return request({
    url: `/setting`,
    method: 'get'
    // params
  })
}

export function SettingStore(data) {
  return request({
    // params,
    data,
    url: `/setting`,
    method: 'post'
  })
}
export function SettingShow(id) {
  return request({
    url: `/setting/${id}`,
    method: 'get'
    // params
  })
}

export function SettingUpdate(id, data) {
  return request({
    // params,
    data,
    url: `/setting/${id}`,
    method: 'put'
  })
}
export function SettingDestroy(id) {
  return request({
    // params,
    url: `/setting/${id}`,
    method: 'delete'
  })
}
