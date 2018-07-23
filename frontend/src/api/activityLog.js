import request from '@/utils/request'

export function ActivityLogIndex(params) {
  return request({
    url: `/activity_log`,
    method: 'get',
    params
  })
}

export function ActivityLogShow(id, params) {
  return request({
    url: `/activity_log/${id}`,
    method: 'get',
    params
  })
}
