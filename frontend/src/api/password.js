import request from '@/utils/request'

export function forget(email) {
  return request({
    url: '/auth/recovery',
    method: 'post',
    data: {
      email
    }
  })
}

export function reset(email, password, password_confirmation, token) {
  return request({
    url: '/auth/reset',
    method: 'post',
    data: {
      email,
      password,
      password_confirmation,
      token
    }
  })
}
