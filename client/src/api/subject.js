import request from './request';

export function createSubject(data) {
  return request({
    method: 'POST',
    url: '/subject',
    data
  })
}

export function getAllSubjects() {
  return request({
    method: 'GET',
    url: '/subject'
  })
}

export function editSubject(data, subjectId) {
  return request({
    method: 'POST',
    url: `/subject/${subjectId}`,
    data
  })
}

export function deleteSubject(subjectId) {
  return request({
    method: 'DELETE',
    url: `/subject/${subjectId}`
  })
}