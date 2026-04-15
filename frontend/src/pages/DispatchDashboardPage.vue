<template>
  <div class="app">
    <div class="title">{{ jobName || 'Dispatch Dashboard' }}</div>

    <JobSelectorBar
      :jobs="jobs"
      :selected-join-id="selectedJoinId"
      :search-term="searchTerm"
      :dispatcher-name="dispatcherName"
      :shift-key="shiftKey"
      @change-job="changeJob"
      @update:search-term="searchTerm = $event"
      @update:dispatcher-name="dispatcherName = $event"
      @update:shift-key="shiftKey = $event"
    />

    <div v-if="errorMessage || flashMessage" class="feedback-bar">
      <span v-if="errorMessage" class="error-text">{{ errorMessage }}</span>
      <span v-else class="success-text">{{ flashMessage }}</span>
    </div>

    <div class="viewport">
      <div class="canvas">
        <DriverRosterTable
          :rows="rows"
          :search-term="searchTerm"
          :dispatcher-name="dispatcherName"
          :statuses="statuses"
          :events="events"
          @save-state="handleSaveState"
          @save-note="handleSaveNote"
          @message="handleMessage"
          @call="handleCall"
        />

        <ShiftJournalPanel
          :notes="shiftNotes"
          :dispatcher-name="dispatcherName"
          :shift-key="shiftKey"
          @save-active="handleSaveActiveShiftNote"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'

import DriverRosterTable from '../components/DriverRosterTable.vue'
import JobSelectorBar from '../components/JobSelectorBar.vue'
import ShiftJournalPanel from '../components/ShiftJournalPanel.vue'

import {
  fetchDashboard,
  fetchJobs,
  saveDriverNote,
  saveDriverState,
  saveActiveShiftNote,
} from '../api/dispatch'

const jobs = ref([])
const selectedJoinId = ref(null)
const searchTerm = ref('')
const dispatcherName = ref('Miguel')
const shiftKey = ref('DAY')
const rows = ref([])
const shiftNotes = ref([])
const job = ref(null)
const flashMessage = ref('')
const errorMessage = ref('')

const statuses = ['ON DUTY', 'OFF DUTY', 'BREAKDOWN', 'DAYS OFF']
const events = ['', 'Enter Job Site', 'Exit Job Site', 'Enter Pull Point', 'Exit Pull Point']

const jobName = computed(() => job.value?.job_name || '')

onMounted(async () => {
  await loadJobs()
})

watch(dispatcherName, async () => {
  if (selectedJoinId.value) {
    await loadDashboard()
  }
})

async function loadJobs() {
  clearMessages()

  try {
    jobs.value = await fetchJobs()

    if (!selectedJoinId.value && jobs.value.length > 0) {
      selectedJoinId.value = Number(jobs.value[0].id_join)
    }

    if (selectedJoinId.value) {
      await loadDashboard()
    }
  } catch (error) {
    setError(extractError(error, 'Failed to load jobs.'))
  }
}

async function loadDashboard() {
  if (!selectedJoinId.value) {
    rows.value = []
    shiftNotes.value = []
    job.value = null
    return
  }

  clearMessages()

  try {
    const data = await fetchDashboard(selectedJoinId.value, dispatcherName.value)

    job.value = data.job || null
    rows.value = Array.isArray(data.rows) ? data.rows : []
    shiftNotes.value = Array.isArray(data.shift_notes) ? data.shift_notes : []
  } catch (error) {
    rows.value = []
    shiftNotes.value = []
    job.value = null
    setError(extractError(error, 'Failed to load dashboard.'))
  }
}

async function changeJob(idJoin) {
  selectedJoinId.value = Number(idJoin)
  await loadDashboard()
}

async function handleSaveState(payload) {
  clearMessages()

  try {
    await saveDriverState(payload)
    flashMessage.value = 'Driver state saved.'
    await loadDashboard()
  } catch (error) {
    setError(extractError(error, 'Failed to save driver state.'))
  }
}

async function handleSaveNote(payload) {
  clearMessages()

  try {
    await saveDriverNote(payload)
    flashMessage.value = 'Driver note saved.'
    await loadDashboard()
  } catch (error) {
    setError(extractError(error, 'Failed to save driver note.'))
  }
}

async function handleSaveActiveShiftNote(payload) {
  clearMessages()

  if (!selectedJoinId.value) {
    setError('Select a job first.')
    return
  }

  if (!payload.started_by_name) {
    setError('Dispatcher name is required.')
    return
  }

  if (!payload.note_text || !payload.note_text.trim()) {
    setError('Note text is required.')
    return
  }

  try {
    await saveActiveShiftNote(selectedJoinId.value, payload)
    flashMessage.value = 'Shift note saved.'
    await loadDashboard()
  } catch (error) {
    setError(extractError(error, 'Failed to save shift note.'))
  }
}

function handleMessage(row) {
  if (row.phone_number) {
    alert(`Message action placeholder for ${row.driver_name} (${row.phone_number})`)
    return
  }

  alert(`No phone number found for ${row.driver_name}.`)
}

function handleCall(row) {
  if (row.phone_number) {
    alert(`Call action placeholder for ${row.driver_name} (${row.phone_number})`)
    return
  }

  alert(`No phone number found for ${row.driver_name}.`)
}

function clearMessages() {
  flashMessage.value = ''
  errorMessage.value = ''
}

function setError(message) {
  errorMessage.value = message
}

function extractError(error, fallback) {
  return error?.response?.data?.message || fallback
}
</script>
