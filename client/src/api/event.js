import request from './request';

export function createEvent(data) {
  return request({
    method: 'POST',
    url: '/event',
    data
  })
}

export function getAllEvents() {
  return request({
    method: 'GET',
    url: '/event'
  })
}

export function editEvent(data, eventId) {
  return request({
    method: 'POST',
    url: `/event/${eventId}`,
    data
  })
}

export function deleteEvent(eventId) {
  return request({
    method: 'DELETE',
    url: `/event/${eventId}`
  })
}