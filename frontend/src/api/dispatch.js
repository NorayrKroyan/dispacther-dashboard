import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api',
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

export async function fetchJobs() {
  const { data } = await api.get('/dispatch/jobs')
  return data.jobs || []
}

export async function fetchDashboard(idJoin, dispatcherName) {
  const { data } = await api.get(`/dispatch/jobs/${idJoin}/dashboard`, {
    params: {
      dispatcher_name: dispatcherName || '',
    },
  })
  return data
}

export async function saveAssignment(payload) {
  const { data } = await api.post('/dispatch/assignments', payload)
  return data
}

export async function saveDriverState(payload) {
  const { data } = await api.post('/dispatch/state', payload)
  return data
}

export async function saveDriverNote(payload) {
  const { data } = await api.post('/dispatch/notes', payload)
  return data
}

export async function saveTrackingSnapshot(payload) {
  const { data } = await api.post('/dispatch/tracking-snapshot', payload)
  return data
}

export async function fetchShiftNotes(idJoin) {
  const { data } = await api.get(`/dispatch/jobs/${idJoin}/shift-notes`)
  return data.shift_notes || []
}

export async function saveActiveShiftNote(idJoin, payload) {
  const { data } = await api.post(`/dispatch/jobs/${idJoin}/shift-notes/active`, payload)
  return data
}

export async function closeShiftNote(idJoin, noteId, payload) {
  const { data } = await api.post(`/dispatch/jobs/${idJoin}/shift-notes/${noteId}/close`, payload)
  return data
}
