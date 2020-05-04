import request from './request';

export function resetPassword(data) {
    return request({
      method: 'POST',
      url: '/user/forget_password',
      data
    })
}

export function changePassword(data) {
  return request({
    method: 'POST',
    url: '/user/reset_password',
    data
  })
}