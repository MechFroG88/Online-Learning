import request from './request';

export function getAllChoices() {
  return request({
    method: 'GET',
    url: '/choice'
  })
}

export function submitChoice(data) {
  return request({
    method: 'POST',
    url: '/choice/submit',
    data
  })
}

export function getUserChoice() {
  return request({
    method: 'GET',
    url: '/choice/user'
  })
}

export function editChoice(data, choiceId) {
  return request({
    method: 'POST',
    url: `/choice/${choiceId}`,
    data
  })
}

export function deleteChoice(choiceId) {
  return request({
    method: 'DELETE',
    url: `/choice/${choiceId}`
  })
}