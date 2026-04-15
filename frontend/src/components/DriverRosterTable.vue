<template>
  <section class="left-panel">
    <div class="toolbar">
      <input v-model="search" class="search" type="text" placeholder="Search personnel..." />
    </div>

    <table class="personnel-table">
      <thead>
        <tr>
          <th class="col-truck">Truck</th>
          <th class="col-driver">Driver</th>
          <th class="col-icons"></th>
          <th class="col-icons"></th>
          <th class="col-status">Current Status</th>
          <th class="col-duty">Duty Duration</th>
          <th class="col-event">Last Event</th>
          <th class="col-miles">Miles To Job</th>
          <th class="col-eta">ETA-Deliver</th>
          <th class="col-notes">Driver Notes</th>
        </tr>
      </thead>

      <tbody>
        <template v-for="(row, index) in filteredRows" :key="`${row.id_join}-${row.id_driver}`">
          <tr v-if="needsSectionGap(index)" class="section-gap-row">
            <td colspan="10"></td>
          </tr>

          <tr>
            <td class="truck-cell">{{ row.truck_number || '' }}</td>
            <td class="driver-cell">{{ row.driver_name || '' }}</td>

            <td class="icon-cell">
              <div class="icon-slot">
                <button class="icon-btn message-icon" type="button" title="Message" @click="emitMessage(row)">
                  <svg viewBox="0 0 20 20" aria-hidden="true">
                    <rect class="bubble" x="2" y="2" width="16" height="16" rx="4" ry="4"></rect>
                    <path class="bubble" d="M6 16 L7.6 13.6 L10 16 Z"></path>
                    <circle class="dot" cx="7" cy="9.5" r="1"></circle>
                    <circle class="dot" cx="10" cy="9.5" r="1"></circle>
                    <circle class="dot" cx="13" cy="9.5" r="1"></circle>
                  </svg>
                </button>
              </div>
            </td>

            <td class="icon-cell">
              <div class="icon-slot">
                <button class="icon-btn call-icon" type="button" title="Call" @click="emitCall(row)">
                  <svg viewBox="0 0 20 20" aria-hidden="true">
                    <circle class="ring" cx="10" cy="10" r="9"></circle>
                    <path
                      class="phone"
                      d="M7.4 5.8c.3-.3.7-.4 1.1-.1l1.2.9c.3.2.4.7.2 1l-.5.9c-.1.2-.1.5 0 .7.6 1 1.4 1.8 2.4 2.4.2.1.5.1.7 0l.9-.5c.4-.2.8-.1 1 .2l.9 1.2c.2.4.2.8-.1 1.1l-.8.8c-.5.5-1.2.7-1.9.5-1.8-.6-3.5-1.8-5-3.3s-2.7-3.2-3.3-5c-.2-.7 0-1.4.5-1.9l.8-.8z"
                    ></path>
                  </svg>
                </button>
              </div>
            </td>

            <td :class="['status-cell', row.status_color]">
              <div class="select-pill">
                <select :value="row.current_status || ''" @change="changeStatus(row, $event.target.value)">
                  <option v-for="status in statuses" :key="status" :value="status">
                    {{ status }}
                  </option>
                </select>
                <span class="caret">▼</span>
              </div>
            </td>

            <td :class="['duty-cell', row.duty_color]">
              {{ row.duty_duration_label || '' }}
            </td>

            <td class="event-cell">
              <div class="select-pill">
                <select :value="row.last_event || ''" @change="changeLastEvent(row, $event.target.value)">
                  <option v-for="eventLabel in events" :key="eventLabel" :value="eventLabel">
                    {{ eventLabel }}
                  </option>
                </select>
                <span class="caret">▼</span>
              </div>
            </td>

            <td class="numeric-cell">{{ row.miles_to_job ?? '' }}</td>
            <td class="numeric-cell">{{ formatEta(row.eta_to_deliver_minutes) }}</td>

            <td class="driver-notes-cell">
              <input
                class="driver-notes-input"
                :value="draftNotes[row.id_driver] ?? row.driver_note ?? ''"
                type="text"
                @input="draftNotes[row.id_driver] = $event.target.value"
                @blur="saveNote(row)"
              />
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </section>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'

const props = defineProps({
  rows: {
    type: Array,
    default: () => [],
  },
  dispatcherName: {
    type: String,
    default: '',
  },
  statuses: {
    type: Array,
    default: () => ['ON DUTY', 'OFF DUTY', 'BREAKDOWN', 'DAYS OFF'],
  },
  events: {
    type: Array,
    default: () => ['', 'Enter Job Site', 'Exit Job Site', 'Enter Pull Point', 'Exit Pull Point'],
  },
})

const emit = defineEmits(['save-state', 'save-note', 'message', 'call'])

const search = ref('')
const draftNotes = reactive({})

watch(
  () => props.rows,
  (rows) => {
    Object.keys(draftNotes).forEach((key) => delete draftNotes[key])

    rows.forEach((row) => {
      draftNotes[row.id_driver] = row.driver_note ?? ''
    })
  },
  { immediate: true, deep: true },
)

const filteredRows = computed(() => {
  const q = search.value.trim().toLowerCase()

  if (!q) {
    return props.rows
  }

  return props.rows.filter((row) => {
    return [
      row.truck_number,
      row.driver_name,
      row.phone_number,
      row.current_status,
      row.last_event,
      row.driver_note,
      row.miles_to_job,
      row.eta_to_deliver_minutes,
    ]
      .join(' ')
      .toLowerCase()
      .includes(q)
  })
})

function needsSectionGap(index) {
  if (index === 0) {
    return false
  }

  const previous = filteredRows.value[index - 1]
  const current = filteredRows.value[index]

  return previous?.current_status === 'ON DUTY' && current?.current_status === 'OFF DUTY'
}

function changeStatus(row, status) {
  emit('save-state', {
    id_driver: row.id_driver,
    id_join: row.id_join,
    current_status: status,
    last_event: row.last_event || '',
    updated_by_name: props.dispatcherName || '',
  })
}

function changeLastEvent(row, lastEvent) {
  emit('save-state', {
    id_driver: row.id_driver,
    id_join: row.id_join,
    current_status: row.current_status,
    last_event: lastEvent,
    updated_by_name: props.dispatcherName || '',
  })
}

function saveNote(row) {
  emit('save-note', {
    id_driver: row.id_driver,
    id_join: row.id_join,
    note_text: draftNotes[row.id_driver] ?? '',
    updated_by_name: props.dispatcherName || '',
  })
}

function emitMessage(row) {
  emit('message', row)
}

function emitCall(row) {
  emit('call', row)
}

function formatEta(minutes) {
  if (minutes === null || minutes === undefined || minutes === '') {
    return ''
  }

  return String(minutes)
}
</script>
