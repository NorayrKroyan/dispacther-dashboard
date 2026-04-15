<template>
  <aside class="right-panel">
    <div class="panel-title">Dispatcher Job Journal</div>

    <div class="journal-editor-card">
      <textarea
        v-model="newNoteText"
        class="note-textarea compact-note-textarea"
        rows="3"
        placeholder="Enter dispatcher journal note..."
      ></textarea>

      <div class="journal-editor-actions">
        <button class="meta-button compact-button" type="button" @click="saveNew">Save</button>
        <button class="meta-button compact-button secondary-button" type="button" @click="clearNew">Clear</button>
      </div>
    </div>

    <div class="journal-history">
      <div class="journal-history-header">
        <span class="history-note-col">Note</span>
        <span class="history-dispatcher-col">Dispatcher</span>
        <span class="history-created-col">Created</span>
        <span class="history-shift-col">Shift</span>
      </div>

      <div v-if="historyNotes.length === 0" class="journal-history-empty">
        No journal history yet.
      </div>

      <div v-for="note in historyNotes" :key="note.id" class="journal-history-row">
        <span class="history-note-col" :title="note.note_text || ''">{{ note.note_text || '' }}</span>
        <span class="history-dispatcher-col">{{ note.started_by_name || '-' }}</span>
        <span class="history-created-col">{{ formatDateTime(note.started_at) }}</span>
        <span class="history-shift-col">{{ note.shift_key || '-' }}</span>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed, ref } from 'vue'

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

const newNoteText = ref('')

const historyNotes = computed(() => {
  return [...props.notes].sort((a, b) => {
    const aDate = a?.started_at ? new Date(a.started_at).getTime() : 0
    const bDate = b?.started_at ? new Date(b.started_at).getTime() : 0

    if (aDate !== bDate) {
      return bDate - aDate
    }

    return Number(b?.id || 0) - Number(a?.id || 0)
  })
})

function saveNew() {
  const noteText = newNoteText.value.trim()

  if (!noteText) {
    return
  }

  emit('save-active', {
    shift_key: props.shiftKey,
    started_by_name: props.dispatcherName,
    note_text: noteText,
  })

  newNoteText.value = ''
}

function clearNew() {
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
