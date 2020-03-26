import request from './request';

export function createClass(data) {
  return request({
    method: 'POST',
    url: '/class',
    data
  })
}

export function getAllClass() {
  return request({
    method: 'GET',
    url: '/class'
  })
}

export function editClass(data, classId) {
  return request({
    method: 'POST',
    url: `/class/${classId}`,
    data
  })
}

export function deleteClass(classId) {
  return request({
    method: 'DELETE',
    url: `/class/${classId}`
  })
}