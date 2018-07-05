import request from '@/utils/request'

export function putMobile(data) {
  return request({
    url: `/user/mobile`,
    method: 'put',
    // params,
    data
  })
}

export function putGender(data) {
  return request({
    url: `/user/gender`,
    method: 'put',
    // params,
    data
  })
}

export function putPassword(data) {
  return request({
    url: `/user/password`,
    method: 'put',
    // params,
    data
  })
}

export function postAvatar(data) {
  return request({
    url: `/user/avatar`,
    method: 'post',
    // params,
    data
  })
}

export function UserIndex(params) {
  return request({
    url: `/user`,
    method: 'get',
    params
  })
}

export function UserStore(params, data) {
  return request({
    url: `/user`,
    method: 'post',
    params,
    data
  })
}

export function UserShow(id, params) {
  return request({
    url: `/user/${id}`,
    method: 'get',
    params
  })
}

export function UserUpdate(id, params, data) {
  return request({
    url: `/user/${id}`,
    method: 'put',
    params,
    data
  })
}

export function UserDestroy(id) {
  return request({
    url: `/user/${id}`,
    method: 'delete'
    // params
  })
}

export function RoleIndex(params) {
  return request({
    url: `/user/role`,
    method: 'get',
    params
  })
}

export function RoleStore(data, params) {
  return request({
    url: `/user/role`,
    method: 'post',
    params,
    data
  })
}

export function RoleShow(id, params) {
  return request({
    url: `/user/role/${id}`,
    method: 'get',
    params
  })
}

export function RoleUpdate(id, params, data) {
  return request({
    url: `/user/role/${id}`,
    method: 'put',
    params,
    data
  })
}

export function RoleDestroy(id) {
  return request({
    url: `/user/role/${id}`,
    method: 'delete'
    // params
  })
}

export function PermissionIndex(params) {
  return request({
    url: `/user/permission`,
    method: 'get',
    params
  })
}

export function PermissionUpdate(id, data) {
  return request({
    url: `/user/permission/${id}`,
    method: 'put',
    // params,
    data
  })
}

export function PermissionStore(data, params) {
  return request({
    url: `/user/permission`,
    method: 'post',
    params,
    data
  })
}

export function PermissionDestroy(id) {
  return request({
    url: `/user/permission/${id}`,
    method: 'delete'
    // params
  })
}

export function PermissionSyncRoles(id, data) {
  return request({
    // params,
    data,
    url: `/user/permission/${id}/sync_roles`,
    method: 'put'
  })
}

export function RoleCopy(data) {
  return request({
    // params,
    data,
    url: `/user/role/copy`,
    method: 'post'
  })
}
