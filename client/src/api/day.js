import request from './request';

export function getAllDays() {
  return request({
    method: 'GET',
    url: '/day'
  })
}

export function editDay(dayId, count) {
  return request({
    method: 'POST',
    url: `/day/${dayId}`,
    data: { count }
  })
}