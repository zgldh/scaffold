import request from '@/utils/request'

export function SettingIndex() {
  return request({
    url: `/setting`,
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
