import request from './request';

export function createPeriod(data) {
  return request({
    method: 'POST',
    url: '/period',
    data
  })
}

export function getAllPeriods() {
  return request({
    method: 'GET',
    url: '/period'
  })
}

export function deletePeriod(periodId) {
  return request({
    method: 'DELETE',
    url: `/period/${periodId}`
  })
}