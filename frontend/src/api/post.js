import request from '@/utils/request'
export function PostIndex(params) {
  return request({
    url: `/post`,
    method: 'get',
    params
  })
}

export function PostStore(data, params) {
  return request({
    params,
    data,
    url: `/post`,
    method: 'post'
  })
}
export function PostShow(id, params) {
  return request({
    url: `/post/${id}`,
    method: 'get',
    params
  })
}

export function PostUpdate(id, data, params) {
  return request({
    params,
    data,
    url: `/post/${id}`,
    method: 'put'
  })
}
export function PostDestroy(id, params) {
  return request({
    params,
    url: `/post/${id}`,
    method: 'delete'
  })
}
