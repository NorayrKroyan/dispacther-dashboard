<template>
  <aside class="right-panel">
    <div class="panel-title">Dispatcher Job Journal</div>

    <div class="journal-stack">
      <article class="journal-card">
        <div class="journal-editor">
          <div class="note-toolbar">
            <div class="label">Note Editor</div>
            <div class="sub">{{ hasEditableNote ? 'Editable' : 'New active note' }}</div>
          </div>

          <div class="note-body">
            <textarea
                v-if="editableNote"
                rows="3"
                class="note-textarea compact-textarea"
                :value="drafts[editableNote.id] ?? editableNote.note_text ?? ''"
                @input="drafts[editableNote.id] = $event.target.value"
            ></textarea>

            <textarea
                v-else
                v-model="newNoteText"
                rows="3"
                class="note-textarea compact-textarea"
            ></textarea>
          </div>
        </div>

        <div class="journal-actions">
          <button class="meta-button" type="button" @click="saveCurrent">Save</button>
          <button class="meta-button" type="button" @click="clearCurrent">Clear</button>
          <div class="journal-actions-fill"></div>
        </div>
      </article>
    </div>

    <div class="small-note">
      Only the top current shift note is editable by the dispatcher who started it. Notes below are historical log entries.
    </div>

    <div class="history-shell history-shell-right">
      <div class="history-title">History</div>

      <table class="history-table">
        <thead>
        <tr>
          <th class="history-note-col">Note</th>
          <th class="history-dispatcher-col">Dispatcher</th>
          <th class="history-created-col">Created</th>
          <th class="history-shift-col">Shift</th>
        </tr>
        </thead>

        <tbody>
        <tr v-if="!historyRows.length">
          <td colspan="4" class="empty-row">No history yet.</td>
        </tr>

        <tr v-for="note in historyRows" :key="note.id">
          <td class="history-note-cell" :title="note.note_text || ''">
            {{ note.note_text || '' }}
          </td>
          <td :title="note.started_by_name || ''">
            {{ note.started_by_name || '' }}
          </td>
          <td :title="formatDateTime(note.started_at)">
            {{ formatDateTime(note.started_at) }}
          </td>
          <td :title="note.shift_key || ''">
            {{ note.shift_key || '' }}
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </aside>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'

const props = defineProps({
  notes: {
    type: Array,
    default: () => [],
  },
  dispatcherName: {
    type: String,
    default: '',
  },
  shiftKey: {
    type: String,
    default: 'DAY',
  },
})

const emit = defineEmits(['save-active'])

const drafts = reactive({})
const newNoteText = ref('')

watch(
    () => props.notes,
    (notes) => {
      Object.keys(drafts).forEach((key) => delete drafts[key])

      notes.forEach((note) => {
        drafts[note.id] = note.note_text ?? ''
      })
    },
    { immediate: true, deep: true },
)

const editableNote = computed(() => props.notes.find((note) => note.is_editable) || null)
const hasEditableNote = computed(() => Boolean(editableNote.value))

const historyRows = computed(() => {
  return [...props.notes].sort((a, b) => {
    const aTime = a?.started_at ? new Date(a.started_at).getTime() : 0
    const bTime = b?.started_at ? new Date(b.started_at).getTime() : 0
    return bTime - aTime
  })
})

function saveCurrent() {
  if (editableNote.value) {
    emit('save-active', {
      shift_key: editableNote.value.shift_key || props.shiftKey,
      started_by_name: editableNote.value.started_by_name || props.dispatcherName,
      note_text: drafts[editableNote.value.id] ?? '',
    })
    return
  }

  emit('save-active', {
    shift_key: props.shiftKey,
    started_by_name: props.dispatcherName,
    note_text: newNoteText.value,
  })
}

function clearCurrent() {
  if (editableNote.value) {
    drafts[editableNote.value.id] = ''
    emit('save-active', {
      shift_key: editableNote.value.shift_key || props.shiftKey,
      started_by_name: editableNote.value.started_by_name || props.dispatcherName,
      note_text: '',
    })
    return
  }

  newNoteText.value = ''
}

function formatDateTime(value) {
  if (!value) {
    return ''
  }

  const date = new Date(value)

  if (Number.isNaN(date.getTime())) {
    return value
  }

  return date.toLocaleString()
}
</script>