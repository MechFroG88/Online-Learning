import request from './request';

export function createUser(data) {
  return request({
    method: 'POST',
    url: '/user',
    data
  })
}

export function getCurrentUser() {
  return request({
    method: 'GET',
    url: '/user'
  })
}

export function getUsers() {
  return request({
    method: 'GET',
    url: '/users'
  })
}

export function userLogin(data) {
  return request({
    method: 'POST',
    url: '/user/login',
    data
  })
}

export function userLogout() {
  return request({
    method: 'POST',
    url: '/user/logout'
  })
}

export function editUser(data, userId) {
  return request({
    method: 'POST',
    url: `/user/${userId}`,
    data
  })
}

export function deleteUser(userId) {
  return request({
    method: 'DELETE',
    url: `/user/${userId}`
  })
}

// user-class-subject relations

export function addSubjectClass(data) {
  return request({
    method: 'POST',
    url: '/class_user',
    data
  })
}

export function getSubjectClass() {
  return request({
    method: 'GET',
    url: '/class_user'
  })
}

export function deleteSubjectClass(classSubjectId) {
  return request({
    method: 'DELETE',
    url: `/class_user/${classSubjectId}`
  })
}