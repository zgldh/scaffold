import request from '@/utils/request'

export function login(email, password) {
  return request({
    url: '/auth/login',
    method: 'post',
    data: {
      email,
      password
    }
  })
}

export function refreshToken() {
  return request({
    url: '/auth/refresh',
    method: 'post'
  })
}

export function getInfo(token) {
  return request({
    url: '/user/current',
    method: 'get',
    params: { token }
  })
}

export function logout(token) {
  return request({
    url: '/auth/logout',
    method: 'post'
  })
}
