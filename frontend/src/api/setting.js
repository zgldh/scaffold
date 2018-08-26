import request from '@/utils/request'

export function SettingIndex(params) {
  return request({
    url: `/setting`,
    method: 'get',
    params
  })
}

export function SettingUpdateItem(name, value) {
  return request({
    // params,
    data: {
      value: value
    },
    url: `/setting/${name}`,
    method: 'put'
  })
}

export function SettingUpdate(data) {
  return request({
    // params,
    data,
    url: `/setting`,
    method: 'put'
  })
}

export function SettingReset() {
  return request({
    params: {
      reset: 1
    },
    url: `/setting`,
    method: 'put'
  })
}
