import request from '@/utils/request'

export function UploadIndex(params) {
  return request({
    url: `/upload`,
    method: 'get',
    params
  })
}

export function UploadStore(params, data) {
  return request({
    url: `/upload`,
    method: 'post',
    params,
    data
  })
}

export function UploadShow(id, params) {
  return request({
    url: `/upload/${id}`,
    method: 'get',
    params
  })
}

export function UploadUpdate(id, params, data) {
  return request({
    url: `/upload/${id}`,
    method: 'put',
    params,
    data
  })
}

export function UploadDestroy(id) {
  return request({
    url: `/upload/${id}`,
    method: 'delete'
    // params
  })
}

export function UploadBundle(action, indexes, options) {
  return request({
    url: `/upload/bundle`,
    method: 'post',
    // params,
    data: {
      action, indexes, options
    }
  })
}
